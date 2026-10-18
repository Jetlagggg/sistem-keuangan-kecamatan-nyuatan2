<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // --- 1. HALAMAN UTAMA MONEV ---
    public function index()
    {
        // A. REALISASI PER POS (1 query agregat, hindari N+1)
        $categories = Category::withSum(['transactions as terpakai_saat_ini' => function ($q) {
            $q->where('jenis', 'keluar')->where('status', Transaction::STATUS_DISETUJUI);
        }], 'nominal')->get();

        foreach ($categories as $cat) {
            $cat->terpakai_saat_ini = $cat->terpakai_saat_ini ?? 0;
            $cat->sisa_saat_ini = $cat->budget - $cat->terpakai_saat_ini;
            $cat->persen_realisasi = $cat->budget > 0 ? ($cat->terpakai_saat_ini / $cat->budget) * 100 : 0;
        }

        // B. DATA GLOBAL
        $totalPagu = BudgetStage::sum('nominal');
        $totalTerserap = Transaction::where('jenis', 'keluar')->approved()->sum('nominal');
        $persen = $totalPagu > 0 ? ($totalTerserap / $totalPagu) * 100 : 0;

        // B2. MENUNGGU VERIFIKASI
        $totalPending = Transaction::where('jenis', 'keluar')->where('status', Transaction::STATUS_DRAFT)->sum('nominal');
        $jumlahPending = Transaction::where('status', Transaction::STATUS_DRAFT)->count();

        // C. SISA ALOKASI
        $totalBudgetPos = Category::sum('budget');
        $sisaAlokasi = $totalPagu - $totalBudgetPos;

        // D. RIWAYAT GU
        $stages = BudgetStage::orderBy('tanggal_cair', 'desc')->get();

        // E. TREN REALISASI BULANAN (tahun berjalan)
        $tahun = (int) now()->year;
        $perBulan = Transaction::where('jenis', 'keluar')
            ->whereYear('tanggal', $tahun)
            ->get(['tanggal', 'nominal'])
            ->groupBy(fn ($t) => (int) $t->tanggal->month)
            ->map(fn ($rows) => (int) $rows->sum('nominal'));

        $trenLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $trenData = collect(range(1, 12))->map(fn ($m) => $perBulan[$m] ?? 0);
        $trenTahun = $tahun;

        return view('categories.index', compact(
            'categories',
            'totalPagu',
            'totalTerserap',
            'persen',
            'sisaAlokasi',
            'stages',
            'trenLabels',
            'trenData',
            'trenTahun',
            'totalPending',
            'jumlahPending'
        ));
    }

    // --- 2. INPUT DANA GU ---
    public function storeBudget(Request $request)
    {
        $this->authorize('create', BudgetStage::class);

        $request->validate([
            'nama_tahap' => 'required|string|max:255',
            'nominal' => ['required', 'regex:/^[0-9.]{1,30}$/'],
            'tanggal_cair' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $nominal = $this->parseAmount($request->nominal, 'nominal');

        $stage = BudgetStage::create([
            'nama_tahap' => $request->nama_tahap,
            'nominal' => $nominal,
            'tanggal_cair' => $request->tanggal_cair,
            'keterangan' => $request->keterangan,
        ]);

        ActivityLog::record('create', 'budget_stages', $stage->id, 'Menambah dana '.$stage->nama_tahap);

        return redirect()->back()->with('success', 'Dana berhasil ditambahkan!');
    }

    // --- 3. HAPUS DANA GU ---
    public function destroyBudget(BudgetStage $budgetStage)
    {
        $this->authorize('delete', $budgetStage);

        if (Transaction::where('budget_stage_id', $budgetStage->id)->exists()) {
            return redirect()->back()->with(
                'error',
                'Dana tidak bisa dihapus karena masih dipakai pada transaksi. Hapus atau ubah transaksi terkait terlebih dahulu.'
            );
        }

        ActivityLog::record('delete', 'budget_stages', $budgetStage->id, 'Menghapus dana '.$budgetStage->nama_tahap);

        $budgetStage->delete();

        return redirect()->back()->with('success', 'Dana dihapus. Pagu otomatis berkurang.');
    }

    // --- 4. SIMPAN POS KEGIATAN BARU ---
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $request->validate([
            'nama' => 'required|string|max:255',
            'budget' => ['required', 'regex:/^[0-9.]{1,30}$/'],
        ]);

        $category = Category::create([
            'nama' => $request->nama,
            'budget' => $this->parseAmount($request->budget, 'budget'),
        ]);

        ActivityLog::record('create', 'categories', $category->id, 'Membuat pos '.$category->nama);

        return redirect()->route('categories.index')->with('success', 'Pos Kegiatan berhasil dibuat');
    }

    // --- 5. UPDATE POS KEGIATAN ---
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $request->validate([
            'nama' => 'required|string|max:255',
            'budget' => ['required', 'regex:/^[0-9.]{1,30}$/'],
        ]);

        $category->update([
            'nama' => $request->nama,
            'budget' => $this->parseAmount($request->budget, 'budget'),
        ]);

        ActivityLog::record('update', 'categories', $category->id, 'Mengubah pos '.$category->nama);

        return redirect()->route('categories.index')->with('success', 'Pos Kegiatan diperbarui');
    }

    // --- 6. HAPUS POS KEGIATAN ---
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        if ($category->transactions()->exists()) {
            return redirect()->route('categories.index')->with(
                'error',
                'Pos tidak bisa dihapus karena masih memiliki transaksi. Hapus atau ubah transaksi terkait terlebih dahulu.'
            );
        }

        ActivityLog::record('delete', 'categories', $category->id, 'Menghapus pos '.$category->nama);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Pos Kegiatan dihapus');
    }

    // Batas atas kolom decimal(15,2)
    private const MAX_AMOUNT = 9999999999999;

    private function parseAmount(string $value, string $field): int
    {
        $amount = (int) preg_replace('/[^0-9]/', '', $value);

        if ($amount < 1 || $amount > self::MAX_AMOUNT) {
            throw ValidationException::withMessages([
                $field => 'Nominal tidak valid (rentang Rp 1 s/d Rp 9.999.999.999.999).',
            ]);
        }

        return $amount;
    }
}

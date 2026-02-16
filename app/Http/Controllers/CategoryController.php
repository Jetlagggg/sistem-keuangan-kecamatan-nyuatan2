<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\BudgetStage; // Wajib import ini

class CategoryController extends Controller
{
    // --- 1. HALAMAN UTAMA MONEV (INI YANG DI-UPDATE BIAR GAK ERROR) ---
    public function index()
    {
        // A. HITUNG REALISASI PER POS KEGIATAN
        $categories = Category::all();
        foreach ($categories as $cat) {
            // Hitung uang keluar di pos ini
            $terpakai = Transaction::where('category_id', $cat->id)->sum('nominal');

            // Simpan ke object category buat ditampilkan di view
            $cat->terpakai_saat_ini = $terpakai;
            $cat->sisa_saat_ini = $cat->budget - $terpakai;
            
            // Hitung persen (cegah error division by zero)
            $cat->persen_realisasi = $cat->budget > 0 ? ($terpakai / $cat->budget) * 100 : 0;
        }

        // B. HITUNG DATA GLOBAL (KOTAK WARNA-WARNI DI ATAS)
        $totalPagu = BudgetStage::sum('nominal'); // Total Uang Masuk (UP + GU)
        $totalTerserap = Transaction::where('jenis', 'keluar')->sum('nominal'); // Total Belanja
        
        // Persen Serapan Global
        $persen = $totalPagu > 0 ? ($totalTerserap / $totalPagu) * 100 : 0;

        // C. HITUNG SISA ALOKASI (Uang Kantor - Total Budget Pos)
        // Ini buat tau berapa uang yang belum dibagi ke amplop pos
        $totalBudgetPos = Category::sum('budget');
        $sisaAlokasi = $totalPagu - $totalBudgetPos;

        // D. AMBIL RIWAYAT GU (BUAT TABEL DI BAWAH SUMMARY)
        $stages = BudgetStage::orderBy('tanggal_cair', 'desc')->get();

        // E. KIRIM SEMUA KE VIEW
        return view('categories.index', compact(
            'categories', 
            'totalPagu', 
            'totalTerserap', 
            'persen', 
            'sisaAlokasi', 
            'stages'
        ));
    }

    // --- 2. INPUT DANA GU (YANG TOMBOL KUNING) ---
    // (Fungsi ini tetap ada, jangan dihapus)
    public function storeBudget(Request $request)
    {
        $request->validate([
            'nama_tahap' => 'required',
            'nominal' => 'required',
            'tanggal_cair' => 'required'
        ]);

        // Hapus titik format rupiah
        $nominalMurni = str_replace('.', '', $request->nominal);

        BudgetStage::create([
            'nama_tahap' => $request->nama_tahap,
            'nominal' => $nominalMurni,
            'tanggal_cair' => $request->tanggal_cair,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Dana berhasil ditambahkan!');
    }

    // --- 3. HAPUS DANA GU ---
    public function destroyBudget($id)
    {
        BudgetStage::destroy($id);
        return redirect()->back()->with('success', 'Dana dihapus. Pagu otomatis berkurang.');
    }

    // --- 4. SIMPAN POS KEGIATAN BARU ---
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'budget' => 'required'
        ]);

        $budgetMurni = str_replace('.', '', $request->budget);

        Category::create([
            'nama' => $request->nama,
            'budget' => $budgetMurni
        ]);

        return redirect()->route('categories.index')->with('success', 'Pos Kegiatan berhasil dibuat');
    }

    // --- 5. UPDATE POS KEGIATAN ---
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama' => 'required',
            'budget' => 'required'
        ]);

        $budgetMurni = str_replace('.', '', $request->budget);

        $category->update([
            'nama' => $request->nama,
            'budget' => $budgetMurni
        ]);

        return redirect()->route('categories.index')->with('success', 'Pos Kegiatan diperbarui');
    }

    // --- 6. HAPUS POS KEGIATAN ---
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Pos Kegiatan dihapus');
    }
}
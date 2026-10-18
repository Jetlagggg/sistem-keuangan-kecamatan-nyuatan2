<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Support\DashboardCache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = $this->filteredQuery($request)
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $stats = DashboardCache::stats();

        $paguGlobal = $stats['pagu'];
        $totalKeluar = $stats['terserap'];
        $saldoAkhir = $paguGlobal - $totalKeluar;

        $totalPending = $stats['pending'];
        $jumlahPending = $stats['pending_count'];

        $categories = DashboardCache::pos(approvedOnly: false);
        $stages = BudgetStage::orderBy('id', 'desc')->get();

        return view('transactions.index', compact(
            'transactions', 'categories', 'stages',
            'paguGlobal', 'totalKeluar', 'saldoAkhir',
            'totalPending', 'jumlahPending'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Transaction::class);

        $request->validate([
            'tanggal' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'budget_stage_id' => 'required|exists:budget_stages,id',
            'nominal' => ['required', 'regex:/^[0-9.]{1,30}$/'],
            'penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $nominal = $this->parseNominal($request->nominal);
        $approved = $request->user()->isAdmin();

        $transaction = DB::transaction(function () use ($request, $nominal, $approved) {
            $this->guardBudget($nominal, (int) $request->category_id, (int) $request->budget_stage_id);

            return Transaction::create([
                'tanggal' => $request->tanggal,
                'category_id' => $request->category_id,
                'budget_stage_id' => $request->budget_stage_id,
                'nominal' => $nominal,
                'penerima' => $request->penerima,
                'keterangan' => $request->keterangan,
                'jenis' => 'keluar',
                'status' => $approved ? Transaction::STATUS_DISETUJUI : Transaction::STATUS_DRAFT,
                'verified_by' => $approved ? $request->user()->id : null,
                'verified_at' => $approved ? now() : null,
            ]);
        });

        ActivityLog::record('create', 'transactions', $transaction->id, 'Menambahkan transaksi');

        $message = $approved
            ? 'Transaksi berhasil disimpan!'
            : 'Transaksi tersimpan dan menunggu verifikasi admin.';

        return redirect()->route('transactions.index')->with('success', $message);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $request->validate([
            'tanggal' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'budget_stage_id' => 'required|exists:budget_stages,id',
            'nominal' => ['required', 'regex:/^[0-9.]{1,30}$/'],
            'penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $nominal = $this->parseNominal($request->nominal);

        DB::transaction(function () use ($request, $transaction, $nominal) {
            $this->guardBudget($nominal, (int) $request->category_id, (int) $request->budget_stage_id, $transaction);

            $transaction->update([
                'tanggal' => $request->tanggal,
                'category_id' => $request->category_id,
                'budget_stage_id' => $request->budget_stage_id,
                'nominal' => $nominal,
                'penerima' => $request->penerima,
                'keterangan' => $request->keterangan,
            ]);
        });

        ActivityLog::record('update', 'transactions', $transaction->id, 'Mengubah transaksi');

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        ActivityLog::record('delete', 'transactions', $transaction->id, 'Menghapus transaksi');

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi dihapus!');
    }

    public function verify(Request $request, Transaction $transaction)
    {
        $this->authorize('verify', $transaction);

        $transaction->update([
            'status' => Transaction::STATUS_DISETUJUI,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        ActivityLog::record('update', 'transactions', $transaction->id, 'Menyetujui transaksi');

        return back()->with('success', 'Transaksi disetujui.');
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $this->authorize('verify', $transaction);

        $transaction->update([
            'status' => Transaction::STATUS_DITOLAK,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        ActivityLog::record('update', 'transactions', $transaction->id, 'Menolak transaksi');

        return back()->with('success', 'Transaksi ditolak.');
    }

    public function cetak(Request $request)
    {
        $transactions = $this->filteredQuery($request, approvedOnly: true)->oldest()->get();

        $totalKeluar = $transactions->where('jenis', 'keluar')->sum('nominal');

        if ($request->filled('budget_stage_id')) {
            $gu = BudgetStage::find($request->budget_stage_id);
            $totalMasuk = $gu ? $gu->nominal : 0;
            $judulLaporan = 'Laporan Transaksi - '.($gu ? $gu->nama_tahap : '');
        } else {
            $totalMasuk = BudgetStage::sum('nominal');
            $judulLaporan = 'Laporan Transaksi Keuangan (Semua Dana)';
        }

        $saldoAkhir = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('transactions.cetak', compact(
            'transactions',
            'totalMasuk',
            'totalKeluar',
            'saldoAkhir',
            'judulLaporan'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-transaksi.pdf');
    }

    public function export(Request $request)
    {
        $transactions = $this->filteredQuery($request, approvedOnly: true)->oldest()->get();

        $filename = 'laporan-transaksi-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($transactions) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['No', 'Tanggal', 'Sumber Dana', 'Pos Kegiatan', 'Uraian', 'Penerima', 'Nominal (Rp)'], ';');

            foreach ($transactions as $i => $t) {
                fputcsv($out, [
                    $i + 1,
                    $t->tanggal?->format('d/m/Y'),
                    $t->budgetStage->nama_tahap ?? 'Non-GU',
                    $t->category->nama ?? 'Umum',
                    $t->keterangan,
                    $t->penerima,
                    $t->nominal,
                ], ';');
            }

            fputcsv($out, ['', '', '', '', '', 'TOTAL', $transactions->where('jenis', 'keluar')->sum('nominal')], ';');

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filteredQuery(Request $request, bool $approvedOnly = false)
    {
        $query = Transaction::query()->with(['category', 'budgetStage']);

        if ($approvedOnly) {
            $query->approved();
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $this->escapeLike($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('keterangan', 'like', '%'.$keyword.'%')
                    ->orWhere('penerima', 'like', '%'.$keyword.'%');
            });
        }

        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('budget_stage_id')) {
            $query->where('budget_stage_id', $request->budget_stage_id);
        }

        return $query;
    }

    private function parseNominal(string $value): int
    {
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    // Netralkan wildcard LIKE agar pencarian aman (bukan % atau _ liar).
    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    // Cegah pengeluaran melebihi sisa pagu pos dan sisa dana tahap.
    private function guardBudget(int $nominal, int $categoryId, int $stageId, ?Transaction $existing = null): void
    {
        if ($nominal < 1) {
            throw ValidationException::withMessages(['nominal' => 'Nominal harus lebih dari 0.']);
        }

        $category = Category::whereKey($categoryId)->lockForUpdate()->firstOrFail();
        $stage = BudgetStage::whereKey($stageId)->lockForUpdate()->firstOrFail();

        $categoryUsed = Transaction::where('category_id', $categoryId)
            ->where('jenis', 'keluar')
            ->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))
            ->sum('nominal');
        $sisaPos = $category->budget - $categoryUsed;

        $stageUsed = Transaction::where('budget_stage_id', $stageId)
            ->where('jenis', 'keluar')
            ->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))
            ->sum('nominal');
        $sisaStage = $stage->nominal - $stageUsed;

        if ($nominal > $sisaPos) {
            throw ValidationException::withMessages([
                'nominal' => 'Melebihi sisa pagu Pos "'.$category->nama.'". Sisa: Rp '.number_format($sisaPos, 0, ',', '.'),
            ]);
        }

        if ($nominal > $sisaStage) {
            throw ValidationException::withMessages([
                'nominal' => 'Melebihi sisa dana tahap "'.$stage->nama_tahap.'". Sisa: Rp '.number_format($sisaStage, 0, ',', '.'),
            ]);
        }
    }
}

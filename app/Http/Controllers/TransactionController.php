<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\BudgetStage;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->with(['category', 'budgetStage']);

        if ($request->keyword) {
            $query->where(function($q) use ($request) {
                $q->where('keterangan', 'like', '%' . $request->keyword . '%')
                  ->orWhere('penerima', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->budget_stage_id) {
            $query->where('budget_stage_id', $request->budget_stage_id);
        }

        $transactions = $query->latest()->get();

        $paguGlobal = BudgetStage::sum('nominal');
        $totalKeluar = Transaction::where('jenis', 'keluar')->sum('nominal');
        $saldoAkhir = $paguGlobal - $totalKeluar;

        $categories = Category::all();
        foreach($categories as $cat) {
            $terpakai = Transaction::where('category_id', $cat->id)->sum('nominal');
            $cat->sisa_saat_ini = $cat->budget - $terpakai;
            $cat->jenis = 'keluar';
        }

        $stages = BudgetStage::orderBy('id', 'desc')->get();

        return view('transactions.index', compact(
            'transactions', 'categories', 'stages',
            'paguGlobal', 'totalKeluar', 'saldoAkhir'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'category_id' => 'required',
            'budget_stage_id' => 'required',
            'nominal' => 'required',
        ]);

        $nominalMurni = str_replace('.', '', $request->nominal);

        $transaction = Transaction::create([
            'tanggal' => $request->tanggal,
            'category_id' => $request->category_id,
            'budget_stage_id' => $request->budget_stage_id,
            'nominal' => $nominalMurni,
            'penerima' => $request->penerima,
            'keterangan' => $request->keterangan,
            'jenis' => 'keluar'
        ]);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'create',
            'table_name' => 'transactions',
            'record_id'  => $transaction->id,
            'description'=> 'Menambahkan transaksi'
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'category_id' => 'required',
            'budget_stage_id' => 'required',
            'nominal' => 'required',
        ]);

        $nominalMurni = str_replace('.', '', $request->nominal);

        $transaction->update([
            'tanggal' => $request->tanggal,
            'category_id' => $request->category_id,
            'budget_stage_id' => $request->budget_stage_id,
            'nominal' => $nominalMurni,
            'penerima' => $request->penerima,
            'keterangan' => $request->keterangan
        ]);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'update',
            'table_name' => 'transactions',
            'record_id'  => $transaction->id,
            'description'=> 'Mengubah transaksi'
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'delete',
            'table_name' => 'transactions',
            'record_id'  => $transaction->id,
            'description'=> 'Menghapus transaksi'
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi dihapus!');
    }

    public function cetak(Request $request)
    {
        $query = Transaction::query()->with(['category', 'budgetStage']);

        if ($request->keyword) {
            $query->where(function($q) use ($request) {
                $q->where('keterangan', 'like', '%' . $request->keyword . '%')
                  ->orWhere('penerima', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->budget_stage_id) {
            $query->where('budget_stage_id', $request->budget_stage_id);
        }

        $transactions = $query->oldest()->get();

        $totalKeluar = $transactions->where('jenis', 'keluar')->sum('nominal');

        if ($request->budget_stage_id) {
            $gu = BudgetStage::find($request->budget_stage_id);
            $totalMasuk = $gu ? $gu->nominal : 0;
            $judulLaporan = 'Laporan Transaksi - ' . ($gu ? $gu->nama_tahap : '');
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
}

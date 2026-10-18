<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;

class TrashController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $transactions = Transaction::onlyTrashed()
            ->with(['category', 'budgetStage'])
            ->latest('deleted_at')
            ->get();

        $categories = Category::onlyTrashed()->latest('deleted_at')->get();

        $stages = BudgetStage::onlyTrashed()->latest('deleted_at')->get();

        return view('arsip.index', compact('transactions', 'categories', 'stages'));
    }

    public function restoreTransaction(int $id)
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $transaction);

        $transaction->restore();

        ActivityLog::record('restore', 'transactions', $transaction->id, 'Memulihkan transaksi');

        return back()->with('success', 'Transaksi dipulihkan.');
    }

    public function restoreCategory(int $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $category);

        $category->restore();

        ActivityLog::record('restore', 'categories', $category->id, 'Memulihkan pos '.$category->nama);

        return back()->with('success', 'Pos Kegiatan dipulihkan.');
    }

    public function restoreBudgetStage(int $id)
    {
        $stage = BudgetStage::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $stage);

        $stage->restore();

        ActivityLog::record('restore', 'budget_stages', $stage->id, 'Memulihkan dana '.$stage->nama_tahap);

        return back()->with('success', 'Dana dipulihkan.');
    }

    public function forceDeleteTransaction(int $id)
    {
        $transaction = Transaction::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $transaction);

        ActivityLog::record('force-delete', 'transactions', $transaction->id, 'Menghapus permanen transaksi');

        $transaction->forceDelete();

        return back()->with('success', 'Transaksi dihapus permanen.');
    }

    public function forceDeleteCategory(int $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $category);

        if (Transaction::withTrashed()->where('category_id', $category->id)->exists()) {
            return back()->with('error', 'Pos masih memiliki transaksi (termasuk yang diarsipkan). Pulihkan dan hapus transaksinya dahulu.');
        }

        ActivityLog::record('force-delete', 'categories', $category->id, 'Menghapus permanen pos '.$category->nama);

        $category->forceDelete();

        return back()->with('success', 'Pos Kegiatan dihapus permanen.');
    }

    public function forceDeleteBudgetStage(int $id)
    {
        $stage = BudgetStage::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $stage);

        if (Transaction::withTrashed()->where('budget_stage_id', $stage->id)->exists()) {
            return back()->with('error', 'Dana masih dipakai transaksi (termasuk yang diarsipkan). Pulihkan dan hapus transaksinya dahulu.');
        }

        ActivityLog::record('force-delete', 'budget_stages', $stage->id, 'Menghapus permanen dana '.$stage->nama_tahap);

        $stage->forceDelete();

        return back()->with('success', 'Dana dihapus permanen.');
    }
}

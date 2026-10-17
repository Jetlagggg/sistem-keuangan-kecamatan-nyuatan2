<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- 1. HALAMAN LOGIN (Tamu) ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 2. HALAMAN TERKUNCI (Harus Login) ---
Route::middleware(['auth'])->group(function () {

    // Halaman Utama langsung ke MONEV ANGGARAN
    Route::get('/', [CategoryController::class, 'index'])->name('dashboard');

    // === FITUR MONEV & ANGGARAN (CategoryController) ===
    // 1. Kelola Pos Kegiatan (hanya method yang tersedia)
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // 2. Simpan Dana GU (Tombol Kuning)
    Route::post('/simpan-dana', [CategoryController::class, 'storeBudget'])->name('budget.store');

    // 3. Hapus Dana GU (Tombol Sampah)
    Route::delete('/hapus-dana/{budgetStage}', [CategoryController::class, 'destroyBudget'])->name('budget.destroy');

    // === FITUR TRANSAKSI HARIAN (TransactionController) ===
    // 1. Cetak Laporan PDF
    Route::get('/transactions/cetak', [TransactionController::class, 'cetak'])->name('transactions.cetak');

    // 2. Kelola Transaksi (hanya method yang tersedia)
    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // 3. Riwayat Aktivitas (audit trail)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // === PROFIL SAYA (semua role) ===
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // === MANAJEMEN PENGGUNA (admin) ===
    Route::resource('users', UserController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // === ARSIP / PEMULIHAN DATA (soft delete) ===
    Route::get('/arsip', [TrashController::class, 'index'])->name('arsip.index');
    Route::post('/arsip/transactions/{id}/restore', [TrashController::class, 'restoreTransaction'])->name('arsip.transactions.restore');
    Route::post('/arsip/categories/{id}/restore', [TrashController::class, 'restoreCategory'])->name('arsip.categories.restore');
    Route::post('/arsip/budget-stages/{id}/restore', [TrashController::class, 'restoreBudgetStage'])->name('arsip.budget-stages.restore');
    Route::delete('/arsip/transactions/{id}', [TrashController::class, 'forceDeleteTransaction'])->name('arsip.transactions.destroy');
    Route::delete('/arsip/categories/{id}', [TrashController::class, 'forceDeleteCategory'])->name('arsip.categories.destroy');
    Route::delete('/arsip/budget-stages/{id}', [TrashController::class, 'forceDeleteBudgetStage'])->name('arsip.budget-stages.destroy');

});

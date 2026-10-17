<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
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

});

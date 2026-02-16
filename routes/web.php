<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;

// --- 1. HALAMAN LOGIN (Tamu) ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 2. HALAMAN TERKUNCI (Harus Login) ---
Route::middleware(['auth'])->group(function () {

    // Halaman Utama langsung ke MONEV ANGGARAN
    Route::get('/', [CategoryController::class, 'index'])->name('dashboard');

    // === FITUR MONEV & ANGGARAN (CategoryController) ===
    // 1. Kelola Pos Kegiatan (CRUD otomatis)
    Route::resource('categories', CategoryController::class);
    
    // 2. Simpan Dana GU (Tombol Kuning)
    Route::post('/simpan-dana', [CategoryController::class, 'storeBudget'])->name('budget.store');

    // 3. Hapus Dana GU (Tombol Sampah) - INI YANG TADI ERROR
    Route::get('/hapus-dana/{id}', [CategoryController::class, 'destroyBudget'])->name('budget.destroy');


    // === FITUR TRANSAKSI HARIAN (TransactionController) ===
    // 1. Cetak Laporan PDF
    Route::get('/transactions/cetak', [TransactionController::class, 'cetak'])->name('transactions.cetak');
    
    // 2. Kelola Transaksi (CRUD otomatis)
    Route::resource('transactions', TransactionController::class);

});
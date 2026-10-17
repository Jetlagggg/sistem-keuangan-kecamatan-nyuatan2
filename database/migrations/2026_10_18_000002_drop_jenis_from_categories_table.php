<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom `jenis` pada categories adalah warisan yang tidak dipakai
     * (kategori transaksi selalu pengeluaran). Karena NOT NULL tanpa default,
     * kolom ini membuat pembuatan Pos Kegiatan gagal. Solusi: hapus kolomnya.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('jenis', ['masuk', 'keluar'])->nullable()->after('nama');
        });
    }
};

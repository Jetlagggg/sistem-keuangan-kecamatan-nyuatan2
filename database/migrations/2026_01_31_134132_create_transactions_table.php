<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal');
        $table->enum('jenis', ['masuk', 'keluar']);
        $table->bigInteger('nominal'); // BigInt untuk nominal hingga miliaran
        $table->text('keterangan')->nullable(); // Boleh dikosongin
        // Ini kuncinya: Menyambungkan ke tabel kategori
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('budget_stages', function (Blueprint $table) {
        $table->id();
        $table->string('nama_tahap'); // Contoh: "UP", "GU 1", "GU 2", "TU"
        $table->decimal('nominal', 15, 2); // Jumlah Uangnya
        $table->date('tanggal_cair'); // Tanggal uang masuk
        $table->string('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_stages');
    }
};

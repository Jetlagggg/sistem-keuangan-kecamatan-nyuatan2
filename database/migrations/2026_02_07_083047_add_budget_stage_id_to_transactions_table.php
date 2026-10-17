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
    Schema::table('transactions', function (Blueprint $table) {
        // Nullable agar data transaksi lama tetap aman
        $table->foreignId('budget_stage_id')
              ->nullable()
              ->constrained('budget_stages')
              ->onDelete('set null'); 
    });
}

public function down()
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropForeign(['budget_stage_id']);
        $table->dropColumn('budget_stage_id');
    });
}
};

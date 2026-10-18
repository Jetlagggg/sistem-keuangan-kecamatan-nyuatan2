<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('status');
            $table->index(['jenis', 'status']);
        });

        Schema::table('budget_stages', function (Blueprint $table) {
            $table->index('tanggal_cair');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['status']);
            $table->dropIndex(['jenis', 'status']);
        });

        Schema::table('budget_stages', function (Blueprint $table) {
            $table->dropIndex(['tanggal_cair']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};

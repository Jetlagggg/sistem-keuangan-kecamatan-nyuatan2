<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('categories', function (Blueprint $table) {
        // Kita tambah kolom budget, default 0 biar data lama gak error
        $table->decimal('budget', 15, 2)->default(0)->after('nama');
    });
}

public function down()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropColumn('budget');
    });
}
};

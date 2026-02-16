<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetStage extends Model
{
    use HasFactory;

    // Ini biar kita bisa isi kolom-kolom ini
    protected $fillable = [
        'nama_tahap',   // UP, GU 1, GU 2...
        'nominal',      // Jumlah Duit
        'tanggal_cair', // Kapan cairnya
        'keterangan'    // Catatan
    ];
}
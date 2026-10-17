<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_tahap',
        'nominal',
        'tanggal_cair',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_cair' => 'date',
        ];
    }
}

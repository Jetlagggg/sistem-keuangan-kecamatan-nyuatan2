<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'category_id',
        'budget_stage_id', // <--- PASTIIN BARIS INI ADA!
        'nominal',
        'penerima',
        'keterangan',
        'jenis' 
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function budgetStage()
    {
        return $this->belongsTo(BudgetStage::class, 'budget_stage_id');
    }
}
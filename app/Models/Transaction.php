<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal',
        'category_id',
        'budget_stage_id',
        'nominal',
        'penerima',
        'keterangan',
        'jenis',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function budgetStage()
    {
        return $this->belongsTo(BudgetStage::class, 'budget_stage_id');
    }
}

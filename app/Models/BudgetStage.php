<?php

namespace App\Models;

use App\Support\DashboardCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon $tanggal_cair
 */
class BudgetStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_tahap',
        'nominal',
        'tanggal_cair',
        'keterangan',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => DashboardCache::flush());
        static::deleted(fn () => DashboardCache::flush());
        static::restored(fn () => DashboardCache::flush());
        static::forceDeleted(fn () => DashboardCache::flush());
    }

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_cair' => 'date',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'budget_stage_id');
    }
}

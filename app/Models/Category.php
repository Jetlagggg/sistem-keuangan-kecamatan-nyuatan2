<?php

namespace App\Models;

use App\Support\DashboardCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $budget
 * @property int|null $terpakai_saat_ini
 * @property int|null $sisa_saat_ini
 * @property float|null $persen_realisasi
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'budget',
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
            'budget' => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

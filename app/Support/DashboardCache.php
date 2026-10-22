<?php

namespace App\Support;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class DashboardCache
{
    public const TTL_MINUTES = 2;

    public const KEYS = [
        'dashboard.pos',
        'dashboard.pos_hint',
        'dashboard.stats',
        'dashboard.tren',
        'dashboard.stages',
    ];

    public static function flush(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }

    public static function stats(): array
    {
        return Cache::remember('dashboard.stats', now()->addMinutes(self::TTL_MINUTES), function () {
            $pagu = BudgetStage::sum('nominal');
            $terserap = Transaction::where('jenis', 'keluar')->approved()->sum('nominal');
            $budgetPos = Category::sum('budget');

            return [
                'pagu' => $pagu,
                'terserap' => $terserap,
                'persen' => $pagu > 0 ? ($terserap / $pagu) * 100 : 0,
                'budget_pos' => $budgetPos,
                'sisa_alokasi' => $pagu - $budgetPos,
                'pending' => Transaction::where('jenis', 'keluar')->where('status', Transaction::STATUS_DRAFT)->sum('nominal'),
                'pending_count' => Transaction::where('status', Transaction::STATUS_DRAFT)->count(),
            ];
        });
    }

    public static function pos(bool $approvedOnly = true)
    {
        $key = $approvedOnly ? 'dashboard.pos' : 'dashboard.pos_hint';

        return Cache::remember($key, now()->addMinutes(self::TTL_MINUTES), function () use ($approvedOnly) {
            $query = Category::withSum(['transactions as terpakai_saat_ini' => function ($q) use ($approvedOnly) {
                $q->where('jenis', 'keluar');

                if ($approvedOnly) {
                    $q->where('status', Transaction::STATUS_DISETUJUI);
                }
            }], 'nominal');

            return $query->get()->each(function (Category $cat) {
                $cat->terpakai_saat_ini = $cat->terpakai_saat_ini ?? 0;
                $cat->sisa_saat_ini = $cat->budget - $cat->terpakai_saat_ini;
                $cat->persen_realisasi = $cat->budget > 0 ? ($cat->terpakai_saat_ini / $cat->budget) * 100 : 0;
            });
        });
    }

    public static function tren(): array
    {
        return Cache::remember('dashboard.tren', now()->addMinutes(self::TTL_MINUTES), function () {
            $tahun = (int) now()->year;

            $perBulan = Transaction::where('jenis', 'keluar')
                ->whereYear('tanggal', $tahun)
                ->get(['tanggal', 'nominal'])
                ->groupBy(fn ($t) => (int) $t->tanggal->month)
                ->map(fn ($rows) => (int) $rows->sum('nominal'));

            return [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                'data' => collect(range(1, 12))->map(fn ($m) => $perBulan[$m] ?? 0),
                'tahun' => $tahun,
            ];
        });
    }

    public static function stages()
    {
        return Cache::remember('dashboard.stages', now()->addMinutes(self::TTL_MINUTES), function () {
            return BudgetStage::orderBy('tanggal_cair')->orderBy('id')->get();
        });
    }
}

<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Support\DashboardCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_are_invalidated_when_budget_stage_changes(): void
    {
        BudgetStage::factory()->create(['nominal' => 1_000_000]);
        $this->assertSame(1_000_000, DashboardCache::stats()['pagu']);

        BudgetStage::factory()->create(['nominal' => 500_000]);
        $this->assertSame(1_500_000, DashboardCache::stats()['pagu']);
    }

    public function test_pos_and_tren_are_invalidated_when_transaction_changes(): void
    {
        $category = Category::factory()->create(['budget' => 2_000_000]);

        Transaction::factory()->create([
            'category_id' => $category->id,
            'jenis' => 'keluar',
            'status' => Transaction::STATUS_DISETUJUI,
            'nominal' => 750_000,
            'tanggal' => now()->toDateString(),
        ]);

        $pos = DashboardCache::pos()->firstWhere('id', $category->id);
        $this->assertSame(750_000, (int) $pos->terpakai_saat_ini);

        $tren = DashboardCache::tren();
        $this->assertSame(750_000, (int) $tren['data'][now()->month - 1]);
    }

    public function test_flush_clears_cached_values(): void
    {
        BudgetStage::factory()->create(['nominal' => 400_000]);
        $this->assertSame(400_000, DashboardCache::stats()['pagu']);

        DashboardCache::flush();
        BudgetStage::query()->delete();

        $this->assertSame(0, DashboardCache::stats()['pagu']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetStageTest extends TestCase
{
    use RefreshDatabase;

    public function test_bendahara_can_store_budget_stage(): void
    {
        $bendahara = User::factory()->bendahara()->create();

        $this->actingAs($bendahara)->post(route('budget.store'), [
            'nama_tahap' => 'GU 1',
            'nominal' => '5.000.000',
            'tanggal_cair' => now()->toDateString(),
            'keterangan' => 'Pencairan pertama',
        ])->assertRedirect();

        $this->assertDatabaseHas('budget_stages', [
            'nama_tahap' => 'GU 1',
            'nominal' => 5000000,
        ]);
    }

    public function test_stage_used_by_a_transaction_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $stage = BudgetStage::factory()->create();
        Transaction::factory()->create(['budget_stage_id' => $stage->id]);

        $response = $this->actingAs($admin)
            ->from(route('categories.index'))
            ->delete(route('budget.destroy', $stage));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('budget_stages', ['id' => $stage->id]);
    }

    public function test_unused_stage_can_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $stage = BudgetStage::factory()->create();

        $this->actingAs($admin)
            ->delete(route('budget.destroy', $stage))
            ->assertRedirect();

        $this->assertModelRemoved(BudgetStage::class, $stage);
    }
}

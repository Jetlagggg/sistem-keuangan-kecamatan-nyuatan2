<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_bendahara_can_store_transaction_within_budget(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['budget' => 1_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);

        $this->actingAs($bendahara)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => '100.000',
            'penerima' => 'Vendor A',
            'keterangan' => 'Pembelian ATK',
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 100000,
            'jenis' => 'keluar',
        ]);
    }

    public function test_transaction_exceeding_category_budget_is_rejected(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['budget' => 50_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);

        $this->actingAs($bendahara)
            ->from(route('transactions.index'))
            ->post(route('transactions.store'), [
                'tanggal' => now()->toDateString(),
                'category_id' => $category->id,
                'budget_stage_id' => $stage->id,
                'nominal' => '100.000',
            ])
            ->assertSessionHasErrors('nominal');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_transaction_exceeding_stage_budget_is_rejected(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['budget' => 5_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 50_000]);

        $this->actingAs($bendahara)
            ->from(route('transactions.index'))
            ->post(route('transactions.store'), [
                'tanggal' => now()->toDateString(),
                'category_id' => $category->id,
                'budget_stage_id' => $stage->id,
                'nominal' => '100.000',
            ])
            ->assertSessionHasErrors('nominal');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_viewer_cannot_store_transaction(): void
    {
        $viewer = User::factory()->viewer()->create();
        $category = Category::factory()->create();
        $stage = BudgetStage::factory()->create();

        $this->actingAs($viewer)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => '100.000',
        ])->assertForbidden();

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_admin_can_destroy_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $transaction = Transaction::factory()->create();

        $this->actingAs($admin)
            ->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'));

        $this->assertModelRemoved(Transaction::class, $transaction);
    }

    public function test_update_exceeding_budget_leaves_transaction_unchanged(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['budget' => 200_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);
        $transaction = Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 150_000,
            'jenis' => 'keluar',
        ]);

        $this->actingAs($bendahara)
            ->from(route('transactions.index'))
            ->put(route('transactions.update', $transaction), [
                'tanggal' => now()->toDateString(),
                'category_id' => $category->id,
                'budget_stage_id' => $stage->id,
                'nominal' => '500.000',
            ])
            ->assertSessionHasErrors('nominal');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'nominal' => 150000,
        ]);
    }

    public function test_bendahara_can_update_transaction_within_budget(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['budget' => 1_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);
        $transaction = Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 150_000,
            'jenis' => 'keluar',
        ]);

        $this->actingAs($bendahara)
            ->put(route('transactions.update', $transaction), [
                'tanggal' => now()->toDateString(),
                'category_id' => $category->id,
                'budget_stage_id' => $stage->id,
                'nominal' => '250.000',
            ])
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'nominal' => 250000,
        ]);
    }

    public function test_transactions_can_be_filtered_by_date_range(): void
    {
        $viewer = User::factory()->viewer()->create();
        $category = Category::factory()->create();
        $stage = BudgetStage::factory()->create();

        Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'tanggal' => now()->subDays(10)->toDateString(),
            'keterangan' => 'Transaksi Lama',
            'jenis' => 'keluar',
        ]);

        Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Transaksi Baru',
            'jenis' => 'keluar',
        ]);

        $response = $this->actingAs($viewer)->get(route('transactions.index', [
            'start_date' => now()->subDay()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('Transaksi Baru');
        $response->assertDontSee('Transaksi Lama');
    }
}

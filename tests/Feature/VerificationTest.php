<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Category $category, BudgetStage $stage, string $nominal = '100.000'): array
    {
        return [
            'tanggal' => now()->toDateString(),
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => $nominal,
            'keterangan' => 'Uji verifikasi',
        ];
    }

    public function test_bendahara_transaction_is_saved_as_draft(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['budget' => 1_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);

        $this->actingAs($bendahara)
            ->post(route('transactions.store'), $this->payload($category, $stage))
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'nominal' => 100000,
            'status' => Transaction::STATUS_DRAFT,
            'verified_by' => null,
        ]);
    }

    public function test_admin_transaction_is_auto_approved(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['budget' => 1_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);

        $this->actingAs($admin)
            ->post(route('transactions.store'), $this->payload($category, $stage))
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'nominal' => 100000,
            'status' => Transaction::STATUS_DISETUJUI,
            'verified_by' => $admin->id,
        ]);
    }

    public function test_admin_can_verify_draft_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $transaction = Transaction::factory()->draft()->create();

        $this->actingAs($admin)
            ->post(route('transactions.verify', $transaction->id))
            ->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_DISETUJUI,
            'verified_by' => $admin->id,
        ]);
    }

    public function test_admin_can_reject_draft_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $transaction = Transaction::factory()->draft()->create();

        $this->actingAs($admin)
            ->post(route('transactions.reject', $transaction->id))
            ->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_DITOLAK,
            'verified_by' => $admin->id,
        ]);
    }

    public function test_bendahara_cannot_verify_transaction(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $transaction = Transaction::factory()->draft()->create();

        $this->actingAs($bendahara)
            ->post(route('transactions.verify', $transaction->id))
            ->assertForbidden();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_DRAFT,
        ]);
    }

    public function test_viewer_cannot_verify_transaction(): void
    {
        $viewer = User::factory()->viewer()->create();
        $transaction = Transaction::factory()->draft()->create();

        $this->actingAs($viewer)
            ->post(route('transactions.verify', $transaction->id))
            ->assertForbidden();
    }

    public function test_approved_scope_excludes_drafts_and_rejected(): void
    {
        Transaction::factory()->create(['nominal' => 100_000, 'status' => Transaction::STATUS_DISETUJUI]);
        Transaction::factory()->create(['nominal' => 200_000, 'status' => Transaction::STATUS_DRAFT]);
        Transaction::factory()->create(['nominal' => 300_000, 'status' => Transaction::STATUS_DITOLAK]);

        $this->assertEquals(100_000, Transaction::approved()->sum('nominal'));
    }
}

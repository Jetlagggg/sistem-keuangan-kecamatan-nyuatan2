<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArsipTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_arsip_page(): void
    {
        $this->get(route('arsip.index'))->assertRedirect(route('login'));
    }

    public function test_bendahara_cannot_view_arsip_page(): void
    {
        $bendahara = User::factory()->bendahara()->create();

        $this->actingAs($bendahara)->get(route('arsip.index'))->assertForbidden();
    }

    public function test_admin_can_view_arsip_page(): void
    {
        $admin = User::factory()->admin()->create();
        Transaction::factory()->create()->delete();

        $this->actingAs($admin)->get(route('arsip.index'))->assertOk();
    }

    public function test_admin_can_restore_archived_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $transaction = Transaction::factory()->create();
        $transaction->delete();

        $this->assertSoftDeleted($transaction);

        $this->actingAs($admin)
            ->post(route('arsip.transactions.restore', $transaction->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($transaction);
    }

    public function test_admin_can_force_delete_archived_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $transaction = Transaction::factory()->create();
        $transaction->delete();

        $this->actingAs($admin)
            ->delete(route('arsip.transactions.destroy', $transaction->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_cannot_force_delete_category_with_archived_transactions(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $transaction = Transaction::factory()->create(['category_id' => $category->id]);
        $transaction->delete();
        $category->delete();

        $this->actingAs($admin)
            ->delete(route('arsip.categories.destroy', $category->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_restore_archived_budget_stage(): void
    {
        $admin = User::factory()->admin()->create();
        $stage = BudgetStage::factory()->create();
        $stage->delete();

        $this->actingAs($admin)
            ->post(route('arsip.budget-stages.restore', $stage->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($stage);
    }
}

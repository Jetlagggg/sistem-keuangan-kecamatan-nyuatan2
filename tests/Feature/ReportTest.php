<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_export_report(): void
    {
        $this->get(route('transactions.cetak'))->assertRedirect(route('login'));
    }

    public function test_admin_can_export_transactions_as_pdf(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['budget' => 5_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);

        Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 250_000,
            'jenis' => 'keluar',
        ]);

        $response = $this->actingAs($admin)->get(route('transactions.cetak'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_report_can_be_filtered_by_budget_stage(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['budget' => 5_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000, 'nama_tahap' => 'GU 1']);

        Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 100_000,
            'jenis' => 'keluar',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('transactions.cetak', ['budget_stage_id' => $stage->id]));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_guest_cannot_export_csv(): void
    {
        $this->get(route('transactions.export'))->assertRedirect(route('login'));
    }

    public function test_admin_can_export_transactions_as_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['budget' => 5_000_000, 'nama' => 'Belanja ATK']);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000, 'nama_tahap' => 'GU 1']);

        Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 250_000,
            'jenis' => 'keluar',
            'keterangan' => 'Pembelian kertas',
        ]);

        $response = $this->actingAs($admin)->get(route('transactions.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Belanja ATK', $content);
        $this->assertStringContainsString('Pembelian kertas', $content);
        $this->assertStringContainsString('250000', $content);
    }
}

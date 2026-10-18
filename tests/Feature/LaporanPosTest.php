<?php

namespace Tests\Feature;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPosTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_laporan_pos(): void
    {
        $this->get(route('categories.laporan'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_laporan_pos_pdf(): void
    {
        $user = User::factory()->bendahara()->create();
        $category = Category::factory()->create(['nama' => 'Belanja ATK', 'budget' => 5_000_000]);
        $stage = BudgetStage::factory()->create(['nominal' => 5_000_000]);

        Transaction::factory()->create([
            'category_id' => $category->id,
            'budget_stage_id' => $stage->id,
            'nominal' => 250_000,
            'jenis' => 'keluar',
            'status' => Transaction::STATUS_DISETUJUI,
        ]);

        $response = $this->actingAs($user)->get(route('categories.laporan'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_authenticated_user_can_export_laporan_pos_csv(): void
    {
        $user = User::factory()->viewer()->create();
        Category::factory()->create(['nama' => 'Belanja Makan', 'budget' => 3_000_000]);

        $response = $this->actingAs($user)->get(route('categories.laporan.csv'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Belanja Makan', $response->streamedContent());
    }
}

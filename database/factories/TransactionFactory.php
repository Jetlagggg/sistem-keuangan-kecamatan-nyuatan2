<?php

namespace Database\Factories;

use App\Models\BudgetStage;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tanggal' => now()->toDateString(),
            'category_id' => Category::factory(),
            'budget_stage_id' => BudgetStage::factory(),
            'nominal' => 100_000,
            'penerima' => fake()->name(),
            'keterangan' => fake()->sentence(),
            'jenis' => 'keluar',
            'status' => Transaction::STATUS_DISETUJUI,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Transaction::STATUS_DRAFT,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }
}

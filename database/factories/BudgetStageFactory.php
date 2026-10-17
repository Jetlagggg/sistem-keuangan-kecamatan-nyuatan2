<?php

namespace Database\Factories;

use App\Models\BudgetStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetStage>
 */
class BudgetStageFactory extends Factory
{
    protected $model = BudgetStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_tahap' => 'GU '.fake()->unique()->numberBetween(1, 9999),
            'nominal' => 5_000_000,
            'tanggal_cair' => now()->toDateString(),
            'keterangan' => null,
        ];
    }
}

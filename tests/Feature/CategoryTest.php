<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_is_parsed_and_stored_when_creating_category(): void
    {
        $bendahara = User::factory()->bendahara()->create();

        $this->actingAs($bendahara)->post(route('categories.store'), [
            'nama' => 'Pos Besar',
            'budget' => '1.500.000',
        ])->assertRedirect(route('categories.index'));

        $category = Category::where('nama', 'Pos Besar')->first();

        $this->assertNotNull($category);
        $this->assertEquals(1500000, $category->budget);
        $this->assertDatabaseHas('categories', [
            'nama' => 'Pos Besar',
            'budget' => 1500000,
        ]);
    }

    public function test_non_numeric_budget_is_rejected(): void
    {
        $bendahara = User::factory()->bendahara()->create();

        $this->actingAs($bendahara)
            ->from(route('categories.index'))
            ->post(route('categories.store'), [
                'nama' => 'Pos Invalid',
                'budget' => 'abc',
            ])
            ->assertSessionHasErrors('budget');

        $this->assertDatabaseMissing('categories', ['nama' => 'Pos Invalid']);
    }
}

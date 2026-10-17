<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_cannot_create_category(): void
    {
        $viewer = User::factory()->viewer()->create();

        $this->actingAs($viewer)->post(route('categories.store'), [
            'nama' => 'Pos Viewer',
            'budget' => '1.000.000',
        ])->assertForbidden();

        $this->assertDatabaseMissing('categories', ['nama' => 'Pos Viewer']);
    }

    public function test_bendahara_can_create_category(): void
    {
        $bendahara = User::factory()->bendahara()->create();

        $this->actingAs($bendahara)->post(route('categories.store'), [
            'nama' => 'Pos Bendahara',
            'budget' => '1.000.000',
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['nama' => 'Pos Bendahara']);
    }

    public function test_viewer_cannot_delete_category(): void
    {
        $viewer = User::factory()->viewer()->create();
        $category = Category::factory()->create();

        $this->actingAs($viewer)
            ->delete(route('categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_bendahara_cannot_delete_category(): void
    {
        $bendahara = User::factory()->bendahara()->create();
        $category = Category::factory()->create();

        $this->actingAs($bendahara)
            ->delete(route('categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertModelRemoved(Category::class, $category);
    }
}

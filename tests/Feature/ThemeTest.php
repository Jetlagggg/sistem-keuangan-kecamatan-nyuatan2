<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_exposes_the_theme_switch(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('js/theme.js', false);
    }

    public function test_authenticated_layout_exposes_the_theme_switch(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('js/theme.js', false);
    }

    public function test_theme_script_is_shipped(): void
    {
        $this->assertFileExists(public_path('js/theme.js'));
    }
}

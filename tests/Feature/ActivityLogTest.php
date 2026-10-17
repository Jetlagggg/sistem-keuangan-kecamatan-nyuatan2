<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_activity_logs(): void
    {
        $this->get(route('activity-logs.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_activity_logs_with_a_logged_description(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/transactions');

        $this->get(route('activity-logs.index'))
            ->assertOk()
            ->assertSee('Login berhasil');
    }
}

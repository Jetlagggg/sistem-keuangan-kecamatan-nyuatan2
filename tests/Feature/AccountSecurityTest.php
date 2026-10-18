<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_is_locked_after_max_failed_attempts(): void
    {
        config(['keuangan.security.max_login_attempts' => 3, 'keuangan.security.lockout_minutes' => 15]);

        $user = User::factory()->bendahara()->create();

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'salah-sekali'])
                ->assertSessionHas('error');
        }

        $this->assertNotNull($user->fresh()->locked_until);
    }

    public function test_locked_account_cannot_login_even_with_correct_password(): void
    {
        $user = User::factory()->bendahara()->create([
            'locked_until' => now()->addMinutes(10),
        ]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_successful_login_resets_failed_attempts(): void
    {
        $user = User::factory()->bendahara()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah-sekali']);
        $this->assertEquals(1, $user->fresh()->failed_login_attempts);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/transactions');

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertEquals(0, $user->fresh()->failed_login_attempts);
        $this->assertNull($user->fresh()->locked_until);
    }

    public function test_weak_password_is_rejected_when_creating_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->from(route('users.index'))->post(route('users.store'), [
            'name' => 'Pengguna Lemah',
            'email' => 'lemah@example.com',
            'password' => 'password',
            'role' => User::ROLE_VIEWER,
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'lemah@example.com']);
    }

    public function test_weak_password_is_rejected_when_changing_own_password(): void
    {
        $user = User::factory()->bendahara()->create();

        $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'tanpaangka',
            'password_confirmation' => 'tanpaangka',
        ])->assertSessionHasErrors('password');
    }
}

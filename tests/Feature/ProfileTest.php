<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_profile_page(): void
    {
        $user = User::factory()->bendahara()->create();

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->viewer()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'nama.baru@example.com',
        ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
            'email' => 'nama.baru@example.com',
        ]);
    }

    public function test_user_can_change_own_password(): void
    {
        $user = User::factory()->bendahara()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'sandi-baru-aman',
            'password_confirmation' => 'sandi-baru-aman',
        ])->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('sandi-baru-aman', $user->fresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->bendahara()->create();

        $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'salah-sekali',
            'password' => 'sandi-baru-aman',
            'password_confirmation' => 'sandi-baru-aman',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->bendahara()->create();

        $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'sandi-baru-aman',
            'password_confirmation' => 'beda-dong',
        ])->assertSessionHasErrors('password');
    }

    public function test_user_cannot_take_another_users_email(): void
    {
        $user = User::factory()->bendahara()->create();
        $other = User::factory()->create(['email' => 'milik.orang@example.com']);

        $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
            'name' => $user->name,
            'email' => 'milik.orang@example.com',
        ])->assertSessionHasErrors('email');
    }
}

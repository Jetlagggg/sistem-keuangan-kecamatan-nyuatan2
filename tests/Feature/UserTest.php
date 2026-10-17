<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_users_page(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_users_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }

    public function test_bendahara_cannot_view_users_page(): void
    {
        $bendahara = User::factory()->bendahara()->create();

        $this->actingAs($bendahara)->get(route('users.index'))->assertForbidden();
    }

    public function test_viewer_cannot_create_user(): void
    {
        $viewer = User::factory()->viewer()->create();

        $this->actingAs($viewer)->post(route('users.store'), [
            'name' => 'Baru',
            'email' => 'baru@example.com',
            'password' => 'rahasia123',
            'role' => User::ROLE_VIEWER,
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Bendahara Baru',
            'email' => 'bendahara@example.com',
            'password' => 'rahasia123',
            'role' => User::ROLE_BENDAHARA,
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'bendahara@example.com',
            'role' => User::ROLE_BENDAHARA,
        ]);

        $created = User::where('email', 'bendahara@example.com')->first();
        $this->assertTrue(Hash::check('rahasia123', $created->password));
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'sama@example.com']);

        $this->actingAs($admin)->from(route('users.index'))->post(route('users.store'), [
            'name' => 'Kembar',
            'email' => 'sama@example.com',
            'password' => 'rahasia123',
            'role' => User::ROLE_VIEWER,
        ])->assertSessionHasErrors('email');
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin->id))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_demote_last_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('users.update', $admin->id), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => User::ROLE_VIEWER,
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->viewer()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $target->id))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->bendahara()->create();

        $this->actingAs($admin)->put(route('users.update', $target->id), [
            'name' => $target->name,
            'email' => $target->email,
            'role' => User::ROLE_BENDAHARA,
            'password' => 'passwordbaru123',
        ])->assertRedirect(route('users.index'));

        $this->assertTrue(Hash::check('passwordbaru123', $target->fresh()->password));
    }
}

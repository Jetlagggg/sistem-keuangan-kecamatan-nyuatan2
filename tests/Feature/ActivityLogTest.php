<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
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

    public function test_activity_logs_can_be_filtered_by_action(): void
    {
        $admin = User::factory()->admin()->create();

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'create',
            'table_name' => 'transactions',
            'record_id' => 1,
            'description' => 'Menambahkan transaksi baru',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'delete',
            'table_name' => 'categories',
            'record_id' => 2,
            'description' => 'Menghapus pos lama',
        ]);

        $this->actingAs($admin)
            ->get(route('activity-logs.index', ['action' => 'delete']))
            ->assertOk()
            ->assertSee('Menghapus pos lama')
            ->assertDontSee('Menambahkan transaksi baru');
    }

    public function test_activity_logs_can_be_filtered_by_keyword(): void
    {
        $admin = User::factory()->admin()->create();

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'create',
            'table_name' => 'transactions',
            'record_id' => 1,
            'description' => 'Pembelian alat tulis',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'create',
            'table_name' => 'transactions',
            'record_id' => 2,
            'description' => 'Pembayaran listrik',
        ]);

        $this->actingAs($admin)
            ->get(route('activity-logs.index', ['keyword' => 'alat tulis']))
            ->assertOk()
            ->assertSee('Pembelian alat tulis')
            ->assertDontSee('Pembayaran listrik');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFcmTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_fcm_token(): void
    {
        $role = Role::create([
            'role' => 'Super Admin',
            'permession' => json_encode(['settings', 'admins']),
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson('/admin/fcm-token', [
                'fcm_token' => 'test-admin-fcm-token',
            ]);

        $response->assertOk();
        $response->assertJsonPath('status', true);

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'fcm_token' => 'test-admin-fcm-token',
        ]);
    }
}

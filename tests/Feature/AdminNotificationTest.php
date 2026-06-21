<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'role' => 'Super Admin',
            'permession' => json_encode(['settings', 'admins']),
        ]);

        $this->admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => true,
        ]);
    }

    public function test_admin_notifications_endpoint_filters_by_dashboard_type(): void
    {
        $this->admin->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return [
                    'title' => 'Dashboard notification',
                    'message' => 'Visible in dashboard feed',
                    'target_type' => 'dashboard',
                    'entity_type' => 'delivery_user',
                    'entity_id' => 9,
                ];
            }
        });

        $this->admin->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return [
                    'title' => 'Store notification',
                    'message' => 'Should be filtered out',
                    'target_type' => 'store',
                    'target_id' => 17,
                ];
            }
        });

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/notifications?type=dashboard');

        $response->assertOk();
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.pagination.total', 1);
        $response->assertJsonPath('data.items.0.title', 'Dashboard notification');
        $response->assertJsonPath('data.items.0.type', 'dashboard');
    }

    public function test_admin_can_mark_notification_as_read(): void
    {
        $this->admin->notify(new class extends Notification
        {
            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return [
                    'title' => 'Unread notification',
                    'message' => 'Needs read flag',
                    'target_type' => 'dashboard',
                ];
            }
        });

        $notification = $this->admin->notifications()->firstOrFail();

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/admin/notifications/{$notification->id}/read");

        $response->assertOk();
        $response->assertJsonPath('status', true);

        $this->assertNotNull($notification->fresh()->read_at);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\StoreType;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\UserOrderStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_notifications(): void
    {
        Queue::fake();

        [$user, $order] = $this->createUserAndOrder();

        $user->notify(new UserOrderStatusUpdatedNotification($order));

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/user/notifications')
            ->assertStatus(200)
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.unread_count', 1)
            ->assertJsonPath('data.items.0.type', 'order_status_update')
            ->assertJsonPath('data.items.0.order_id', $order->id)
            ->assertJsonPath('data.items.0.status', 'ready');
    }

    public function test_user_can_mark_notifications_as_read(): void
    {
        Queue::fake();

        [$user, $order] = $this->createUserAndOrder();

        $user->notify(new UserOrderStatusUpdatedNotification($order));
        $user->notify(new UserOrderStatusUpdatedNotification($order));

        $firstNotification = $user->notifications()->latest()->firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/user/notifications/{$firstNotification->id}/read")
            ->assertStatus(200)
            ->assertJsonPath('code', 200);

        $this->assertNotNull($firstNotification->fresh()->read_at);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/notifications/read-all')
            ->assertStatus(200)
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.updated_count', 1);

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    private function createUserAndOrder(): array
    {
        $storeType = StoreType::create(['name' => 'Pharmacy']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01020202020',
            'password' => Hash::make('password123'),
            'store_name' => 'Notify Store',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'is_active' => true,
            'approval_status' => 'approved',
            'ban' => false,
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'customer_first_name' => 'Test',
            'customer_last_name' => 'User',
            'customer_phone' => '01030303030',
            'status' => OrderStatus::Ready,
            'payment_method' => PaymentMethod::Cash,
            'payment_status' => PaymentStatus::Pending,
            'delivery_address' => 'Cairo',
            'delivery_latitude' => 30.0500000,
            'delivery_longitude' => 31.2400000,
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 15,
            'total' => 115,
        ]);

        return [$user, $order];
    }
}

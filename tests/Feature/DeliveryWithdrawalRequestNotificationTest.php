<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Admin;
use App\Models\DeliveryUser;
use App\Models\Order;
use App\Models\Role;
use App\Models\StoreType;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeliveryWithdrawalRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_withdraw_request_notifies_admins_in_database(): void
    {
        Queue::fake();

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

        $user = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'phone' => '01011111111',
            'password' => Hash::make('password123'),
        ]);

        $storeType = StoreType::create(['name' => 'Pharmacy']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01022222222',
            'password' => Hash::make('password123'),
            'store_name' => 'Withdraw Store',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'approval_status' => 'approved',
            'is_active' => true,
            'ban' => false,
        ]);

        $delivery = DeliveryUser::create([
            'name' => 'Captain Notify',
            'phone' => '01033333333',
            'email' => 'captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivery/front.png',
            'back_ident' => 'delivery/back.png',
            'personal_deriving_license' => 'delivery/personal.png',
            'machine_license' => 'delivery/machine.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $delivery->id,
            'customer_first_name' => 'Captain',
            'customer_last_name' => 'Client',
            'customer_phone' => '01044444444',
            'status' => OrderStatus::Delivered,
            'delivery_status' => 'delivered',
            'payment_method' => PaymentMethod::Paymob,
            'payment_status' => PaymentStatus::Paid,
            'paymob_transaction_id' => 'txn_12345',
            'delivery_address' => 'Cairo',
            'delivery_latitude' => 30.0500000,
            'delivery_longitude' => 31.2400000,
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 20,
            'total' => 120,
        ]);

        $response = $this->actingAs($delivery, 'sanctum')
            ->postJson('/delivery/wallet/withdraw-requests', [
                'method' => 'instapay',
                'transfer_details' => 'captain@instapay',
                'amount' => 20,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('code', 201)
            ->assertJsonPath('data.delivery_id', $delivery->id)
            ->assertJsonPath('data.amount', 20);

        $this->assertDatabaseHas('delivery_withdrawal_requests', [
            'delivery_id' => $delivery->id,
            'amount' => 20.00,
            'method' => 'instapay',
        ]);

        $this->assertDatabaseHas('delivery_withdrawal_request_orders', [
            'order_id' => $order->id,
            'amount' => 20.00,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Admin::class,
            'notifiable_id' => $admin->id,
        ]);

        $notification = $admin->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame('new_delivery_withdraw_request', $notification->data['type']);
        $this->assertSame('dashboard', $notification->data['target_type']);
        $this->assertSame($delivery->id, $notification->data['delivery_id']);
    }
}

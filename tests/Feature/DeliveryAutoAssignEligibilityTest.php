<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\DeliveryUser;
use App\Models\Order;
use App\Models\StoreType;
use App\Models\User;
use App\Models\Vendor;
use App\Services\DeliveryAutoAssignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeliveryAutoAssignEligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_orders_relation_counts_only_active_delivery_orders(): void
    {
        $vendor = $this->createVendor();
        $deliveryUser = $this->createDeliveryUser([
            'max_active_orders' => 1,
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Ready,
            'delivery_status' => 'assigned',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Accepted,
            'delivery_status' => 'assigned',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Ready,
            'delivery_status' => 'picked_up',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::OnTheWay,
            'delivery_status' => 'assigned',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Pending,
            'delivery_status' => 'pending',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Ready,
            'delivery_status' => 'no_candidates',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Delivered,
            'delivery_status' => 'delivered',
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Cancelled,
            'delivery_status' => 'cancelled',
        ]);

        $this->assertSame(4, $deliveryUser->fresh()->activeOrders()->count());
    }

    public function test_eligible_candidates_excludes_driver_when_max_active_orders_limit_is_reached(): void
    {
        $vendor = $this->createVendor();
        $deliveryUser = $this->createDeliveryUser([
            'max_active_orders' => 1,
        ]);

        $this->createOrder($vendor, [
            'delivery_id' => $deliveryUser->id,
            'status' => OrderStatus::Ready,
            'delivery_status' => 'assigned',
        ]);

        $order = $this->createOrder($vendor, [
            'status' => OrderStatus::Ready,
            'delivery_status' => 'searching',
        ]);

        $candidates = app(DeliveryAutoAssignService::class)->eligibleCandidates($order);

        $this->assertCount(0, $candidates);
        $this->assertFalse($candidates->contains('id', $deliveryUser->id));
    }

    private function createVendor(): Vendor
    {
        $storeType = StoreType::create(['name' => 'Eligibility Store']);

        return Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => fake()->unique()->numerify('010########'),
            'password' => Hash::make('password123'),
            'store_name' => 'Eligibility Vendor',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'is_active' => true,
            'approval_status' => 'approved',
            'ban' => false,
        ]);
    }

    private function createDeliveryUser(array $overrides = []): DeliveryUser
    {
        return DeliveryUser::create(array_merge([
            'name' => 'Eligible Captain',
            'phone' => fake()->unique()->numerify('010########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'front_ident' => 'deliveries/front.png',
            'back_ident' => 'deliveries/back.png',
            'personal_deriving_license' => 'deliveries/license.png',
            'machine_license' => 'deliveries/machine.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 1,
        ], $overrides));
    }

    private function createOrder(Vendor $vendor, array $overrides = []): Order
    {
        $user = User::factory()->create();

        return Order::create(array_merge([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'customer_first_name' => 'Test',
            'customer_last_name' => 'Customer',
            'customer_phone' => fake()->unique()->numerify('010########'),
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
        ], $overrides));
    }
}

<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\CartItem;
use App\Models\DeliveryUser;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreType;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeliveryLocationAndAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_user_can_update_live_location_via_api_endpoint(): void
    {
        $deliveryUser = DeliveryUser::create([
            'name' => 'Captain One',
            'phone' => '01011111111',
            'email' => 'captain1@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front1.png',
            'back_ident' => 'delivaries/back1.png',
            'personal_deriving_license' => 'delivaries/personal1.png',
            'machine_license' => 'delivaries/machine1.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
        ]);

        $this->actingAs($deliveryUser, 'sanctum')
            ->postJson('/api/update_location', [
                'lat' => 30.1234567,
                'lng' => 31.7654321,
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.lat', 30.1234567)
            ->assertJsonPath('data.lng', 31.7654321);

        $this->assertDatabaseHas('delivery_users', [
            'id' => $deliveryUser->id,
            'lat' => '30.1234567',
            'lng' => '31.7654321',
        ]);
    }

    public function test_new_order_is_not_assigned_immediately_before_vendor_marks_it_ready(): void
    {
        $storeType = StoreType::create(['name' => 'Pharmacy']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01022222222',
            'password' => Hash::make('password123'),
            'store_name' => 'Nearby Store',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'is_active' => true,
            'approval_status' => 'approved',
            'ban' => false,
        ]);

        $subcategory = Subcategory::create([
            'store_type_id' => $storeType->id,
            'vendor_id' => $vendor->id,
            'name' => 'Pain Relief',
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Paracetamol',
            'description' => 'Pain killer',
            'quantity' => 10,
            'remaining_quantity' => 10,
            'discount' => 0,
            'is_available' => true,
            'unit' => 'box',
            'price' => 50,
        ]);

        $user = User::factory()->create();

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $nearest = DeliveryUser::create([
            'name' => 'Nearest Captain',
            'phone' => '01033333333',
            'email' => 'nearest@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front2.png',
            'back_ident' => 'delivaries/back2.png',
            'personal_deriving_license' => 'delivaries/personal2.png',
            'machine_license' => 'delivaries/machine2.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 1,
        ]);

        DeliveryUser::create([
            'name' => 'Far Captain',
            'phone' => '01044444444',
            'email' => 'far@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front3.png',
            'back_ident' => 'delivaries/back3.png',
            'personal_deriving_license' => 'delivaries/personal3.png',
            'machine_license' => 'delivaries/machine3.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.5000000,
            'lng' => 31.8000000,
            'max_active_orders' => 1,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/orders', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'phone' => '01055555555',
                'payment_method' => 'cash',
                'delivery_address' => 'Cairo',
                'delivery_latitude' => 30.0500000,
                'delivery_longitude' => 31.2400000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.vendor.id', $vendor->id);

        $this->assertDatabaseHas('orders', [
            'vendor_id' => $vendor->id,
            'delivery_id' => null,
        ]);
    }

    public function test_vendor_status_ready_marks_order_as_searching_for_delivery(): void
    {
        $storeType = StoreType::create(['name' => 'Supermarket']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01066666666',
            'password' => Hash::make('password123'),
            'store_name' => 'Ready Store',
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
            'customer_first_name' => 'Ready',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01077777777',
            'status' => OrderStatus::Preparing,
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

        $nearest = DeliveryUser::create([
            'name' => 'Ready Captain',
            'phone' => '01088888888',
            'email' => 'ready-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front4.png',
            'back_ident' => 'delivaries/back4.png',
            'personal_deriving_license' => 'delivaries/personal4.png',
            'machine_license' => 'delivaries/machine4.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 1,
        ]);

        DeliveryUser::create([
            'name' => 'Ready Far Captain',
            'phone' => '01099999998',
            'email' => 'ready-far@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front5.png',
            'back_ident' => 'delivaries/back5.png',
            'personal_deriving_license' => 'delivaries/personal5.png',
            'machine_license' => 'delivaries/machine5.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.5000000,
            'lng' => 31.8000000,
            'max_active_orders' => 1,
        ]);

        $this->actingAs($vendor, 'sanctum')
            ->postJson("/api/vendor/orders/{$order->id}/status", [
                'status' => 'ready',
            ])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'ready');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'ready',
            'delivery_id' => null,
            'delivery_status' => 'searching',
        ]);

        $notification = $user->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame('order_status_update', $notification->data['type']);
        $this->assertSame($order->id, $notification->data['order_id']);
        $this->assertSame('ready', $notification->data['status']);
    }

    public function test_first_delivery_user_to_accept_gets_the_order(): void
    {
        $storeType = StoreType::create(['name' => 'Supermarket']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01012121212',
            'password' => Hash::make('password123'),
            'store_name' => 'Accept Store',
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
            'customer_first_name' => 'Accept',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01013131313',
            'status' => OrderStatus::Ready,
            'delivery_status' => 'searching',
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

        $captain = DeliveryUser::create([
            'name' => 'Accept Captain',
            'phone' => '01014141414',
            'email' => 'accept-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front6.png',
            'back_ident' => 'delivaries/back6.png',
            'personal_deriving_license' => 'delivaries/personal6.png',
            'machine_license' => 'delivaries/machine6.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 1,
        ]);

        $this->actingAs($captain, 'sanctum')
            ->postJson("/delivery/orders/{$order->id}/accept")
            ->assertStatus(200)
            ->assertJsonPath('data.delivery_id', $captain->id)
            ->assertJsonPath('data.order_status', 'on_the_way')
            ->assertJsonPath('data.delivery_status', 'assigned');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'delivery_id' => $captain->id,
            'status' => 'on_the_way',
            'delivery_status' => 'assigned',
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $notification = $user->notifications()->latest()->first();
        $this->assertNotNull($notification);
        $this->assertSame('order_status_update', $notification->data['type']);
        $this->assertSame($order->id, $notification->data['order_id']);
        $this->assertSame('on_the_way', $notification->data['status']);
    }

    public function test_available_orders_endpoint_returns_open_ready_orders_for_delivery_user(): void
    {
        $storeType = StoreType::create(['name' => 'Bakery']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01015151515',
            'password' => Hash::make('password123'),
            'store_name' => 'Available Store',
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
            'customer_first_name' => 'Open',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01016161616',
            'status' => OrderStatus::Ready,
            'delivery_status' => 'searching',
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

        $captain = DeliveryUser::create([
            'name' => 'List Captain',
            'phone' => '01017171717',
            'email' => 'list-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front7.png',
            'back_ident' => 'delivaries/back7.png',
            'personal_deriving_license' => 'delivaries/personal7.png',
            'machine_license' => 'delivaries/machine7.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 1,
        ]);

        $this->actingAs($captain, 'sanctum')
            ->getJson('/delivery/orders/available')
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath('data.0.delivery_status', 'searching')
            ->assertJsonPath('data.0.vendor.id', $vendor->id);
    }

    public function test_current_accepted_orders_endpoint_returns_only_current_orders_for_authenticated_delivery_user(): void
    {
        $storeType = StoreType::create(['name' => 'Groceries']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01018181818',
            'password' => Hash::make('password123'),
            'store_name' => 'Accepted Orders Store',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'is_active' => true,
            'approval_status' => 'approved',
            'ban' => false,
        ]);

        $captain = DeliveryUser::create([
            'name' => 'Accepted List Captain',
            'phone' => '01019191919',
            'email' => 'accepted-list-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front8.png',
            'back_ident' => 'delivaries/back8.png',
            'personal_deriving_license' => 'delivaries/personal8.png',
            'machine_license' => 'delivaries/machine8.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 2,
        ]);

        $otherCaptain = DeliveryUser::create([
            'name' => 'Other Captain',
            'phone' => '01020202020',
            'email' => 'other-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front9.png',
            'back_ident' => 'delivaries/back9.png',
            'personal_deriving_license' => 'delivaries/personal9.png',
            'machine_license' => 'delivaries/machine9.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0460000,
            'lng' => 31.2370000,
            'max_active_orders' => 2,
        ]);

        $acceptedOrderUser = User::factory()->create();
        $otherOrderUser = User::factory()->create();

        $acceptedOrder = Order::create([
            'user_id' => $acceptedOrderUser->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $captain->id,
            'customer_first_name' => 'Accepted',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01021212121',
            'status' => OrderStatus::OnTheWay,
            'delivery_status' => 'assigned',
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

        Order::create([
            'user_id' => User::factory()->create()->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $captain->id,
            'customer_first_name' => 'Completed',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01023232323',
            'status' => OrderStatus::Delivered,
            'delivery_status' => 'delivered',
            'payment_method' => PaymentMethod::Cash,
            'payment_status' => PaymentStatus::Pending,
            'delivery_address' => 'Nasr City',
            'delivery_latitude' => 30.0700000,
            'delivery_longitude' => 31.2600000,
            'subtotal' => 120,
            'discount_amount' => 0,
            'delivery_fee' => 20,
            'total' => 140,
        ]);

        Order::create([
            'user_id' => $otherOrderUser->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $otherCaptain->id,
            'customer_first_name' => 'Other',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01022222222',
            'status' => OrderStatus::OnTheWay,
            'delivery_status' => 'assigned',
            'payment_method' => PaymentMethod::Cash,
            'payment_status' => PaymentStatus::Pending,
            'delivery_address' => 'Giza',
            'delivery_latitude' => 30.0600000,
            'delivery_longitude' => 31.2500000,
            'subtotal' => 80,
            'discount_amount' => 0,
            'delivery_fee' => 10,
            'total' => 90,
        ]);

        $this->actingAs($captain, 'sanctum')
            ->getJson('/delivery/orders/current-accepted')
            ->assertStatus(200)
            ->assertJsonPath('data.delivery_user.id', $captain->id)
            ->assertJsonPath('data.delivery_user.max_active_orders', 2)
            ->assertJsonCount(1, 'data.orders')
            ->assertJsonPath('data.orders.0.id', $acceptedOrder->id)
            ->assertJsonPath('data.orders.0.customer_name', 'Accepted Customer')
            ->assertJsonPath('data.orders.0.delivery_status', 'assigned')
            ->assertJsonPath('data.orders.0.status', 'on_the_way')
            ->assertJsonPath('data.orders.0.pricing.total', 115);
    }

    public function test_completed_orders_endpoint_returns_only_completed_orders_for_authenticated_delivery_user(): void
    {
        $storeType = StoreType::create(['name' => 'Completed Store']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01024242424',
            'password' => Hash::make('password123'),
            'store_name' => 'Completed Orders Store',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'is_active' => true,
            'approval_status' => 'approved',
            'ban' => false,
        ]);

        $captain = DeliveryUser::create([
            'name' => 'Completed Captain',
            'phone' => '01025252525',
            'email' => 'completed-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front10.png',
            'back_ident' => 'delivaries/back10.png',
            'personal_deriving_license' => 'delivaries/personal10.png',
            'machine_license' => 'delivaries/machine10.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 2,
        ]);

        $completedOrder = Order::create([
            'user_id' => User::factory()->create()->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $captain->id,
            'customer_first_name' => 'Delivered',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01026262626',
            'status' => OrderStatus::Delivered,
            'delivery_status' => 'delivered',
            'payment_method' => PaymentMethod::Cash,
            'payment_status' => PaymentStatus::Pending,
            'delivery_address' => 'Heliopolis',
            'delivery_latitude' => 30.0800000,
            'delivery_longitude' => 31.2700000,
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 15,
            'total' => 115,
        ]);

        Order::create([
            'user_id' => User::factory()->create()->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $captain->id,
            'customer_first_name' => 'Current',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01027272727',
            'status' => OrderStatus::OnTheWay,
            'delivery_status' => 'assigned',
            'payment_method' => PaymentMethod::Cash,
            'payment_status' => PaymentStatus::Pending,
            'delivery_address' => 'Maadi',
            'delivery_latitude' => 30.0900000,
            'delivery_longitude' => 31.2800000,
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 15,
            'total' => 115,
        ]);

        $this->actingAs($captain, 'sanctum')
            ->getJson('/delivery/orders/completed')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $completedOrder->id)
            ->assertJsonPath('data.0.delivery_status', 'delivered')
            ->assertJsonPath('data.0.status', 'delivered');
    }

    public function test_delivery_user_can_complete_assigned_order_and_response_data_is_empty(): void
    {
        $storeType = StoreType::create(['name' => 'Completion Store']);

        $vendor = Vendor::create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01028282828',
            'password' => Hash::make('password123'),
            'store_name' => 'Completion Vendor',
            'store_type_id' => $storeType->id,
            'latitude' => 30.0444200,
            'longitude' => 31.2357000,
            'is_active' => true,
            'approval_status' => 'approved',
            'ban' => false,
        ]);

        $captain = DeliveryUser::create([
            'name' => 'Completion Captain',
            'phone' => '01029292929',
            'email' => 'completion-captain@example.com',
            'password' => Hash::make('password123'),
            'front_ident' => 'delivaries/front11.png',
            'back_ident' => 'delivaries/back11.png',
            'personal_deriving_license' => 'delivaries/personal11.png',
            'machine_license' => 'delivaries/machine11.png',
            'approval_status' => 'approved',
            'active_status' => true,
            'ban' => false,
            'lat' => 30.0450000,
            'lng' => 31.2360000,
            'max_active_orders' => 1,
        ]);

        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'vendor_id' => $vendor->id,
            'delivery_id' => $captain->id,
            'customer_first_name' => 'Completion',
            'customer_last_name' => 'Customer',
            'customer_phone' => '01030303030',
            'status' => OrderStatus::OnTheWay,
            'delivery_status' => 'assigned',
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

        $this->actingAs($captain, 'sanctum')
            ->postJson("/delivery/orders/{$order->id}/complete")
            ->assertStatus(200)
            ->assertJsonPath('data', []);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'delivery_id' => $captain->id,
            'status' => 'delivered',
            'delivery_status' => 'delivered',
            'payment_status' => 'paid',
        ]);

        $user = User::findOrFail($order->user_id);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $notification = $user->notifications()->latest()->first();
        $this->assertNotNull($notification);
        $this->assertSame('order_status_update', $notification->data['type']);
        $this->assertSame($order->id, $notification->data['order_id']);
        $this->assertSame('delivered', $notification->data['status']);
    }
}

<?php

namespace App\Services;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected PaymobService $paymobService
    ) {}

    public function placeOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cartItems = $user->cartItems()->with('product.vendor')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $vendor = $cartItems->first()->product?->vendor;

            if (! $vendor || ! $vendor->is_active) {
                throw new \Exception('Vendor is not available');
            }

            $unavailable = [];
            $subtotal = 0;
            $discountAmount = 0;

            foreach ($cartItems as $item) {
                $product = $item->product;

                if (! $product || ! $product->is_available || $item->quantity > $product->remaining_quantity) {
                    $unavailable[] = $product?->name ?? "Product #{$item->product_id}";
                    continue;
                }

                $finalPrice = (float) $product->price_after_discount;
                $itemTotal = $finalPrice * (int) $item->quantity;

                $subtotal += $itemTotal;
                $discountAmount += ((float) $product->price - $finalPrice) * (int) $item->quantity;
            }

            if ($unavailable !== []) {
                throw new \Exception('These products are unavailable: ' . implode(', ', $unavailable));
            }

            $deliveryFee = (float) $vendor->delivery_fee;
            $total = $subtotal + $deliveryFee;

            $order = Order::create([
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
                'customer_first_name' => $data['first_name'],
                'customer_last_name' => $data['last_name'],
                'customer_phone' => $data['phone'],
                'status' => OrderStatus::Pending,
                'payment_method' => PaymentMethod::from($data['payment_method']),
                'payment_status' => PaymentStatus::Pending,
                'delivery_address' => $data['delivery_address'],
                'delivery_latitude' => $data['delivery_latitude'] ?? null,
                'delivery_longitude' => $data['delivery_longitude'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $item) {
                $product = $item->product;
                $finalPrice = (float) $product->price_after_discount;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_unit' => $product->unit,
                    'unit_price' => $product->price,
                    'discount' => $product->discount,
                    'final_price' => $finalPrice,
                    'quantity' => $item->quantity,
                    'total_price' => $finalPrice * (int) $item->quantity,
                ]);
            }

            foreach ($cartItems as $item) {
                $item->product->decrement('remaining_quantity', $item->quantity);
            }

            $order->delivery()->create([
                'status' => DeliveryStatus::Searching,
                'pickup_address' => $vendor->address_description,
                'pickup_latitude' => $vendor->latitude,
                'pickup_longitude' => $vendor->longitude,
                'dropoff_address' => $data['delivery_address'],
                'dropoff_latitude' => $data['delivery_latitude'] ?? null,
                'dropoff_longitude' => $data['delivery_longitude'] ?? null,
            ]);

            if ($order->payment_method === PaymentMethod::Paymob) {
                $paymentData = $this->paymobService->createOrder($order->load('user'), $data);

                $order->update([
                    'paymob_order_id' => (string) $paymentData['paymob_order_id'],
                ]);

                $order->setAttribute('payment_url', $paymentData['payment_url']);
            }

            $user->cartItems()->delete();

            return $order->load(['items.product.mainImage', 'vendor']);
        });
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected PaymobService $paymobService,
        protected FcmService $fcmService,
        protected DeliveryAutoAssignService $deliveryAutoAssignService
    ) {}

    public function placeOrder(User $user, array $data): Order
    {
        $order = DB::transaction(function () use ($user, $data) {
            $cartItems = $user->cartItems()->with('product.vendor')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('السلة فارغة');
            }

            $vendor = $cartItems->first()->product?->vendor;

            if (! $vendor || ! $vendor->is_active) {
                throw new \Exception('المتجر غير متاح حالياً');
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
                throw new \Exception('هذه المنتجات غير متوفرة حالياً: ' . implode(', ', $unavailable));
            }

            $selectedAddress = null;

            if (! empty($data['address_id'])) {
                $selectedAddress = $user->addresses()->find($data['address_id']);

                if (! $selectedAddress) {
                    throw new \Exception('العنوان المحدد غير موجود');
                }
            }

            $deliveryAddress = $selectedAddress?->address ?? ($data['delivery_address'] ?? null);
            $deliveryLatitude = $selectedAddress?->latitude ?? ($data['delivery_latitude'] ?? null);
            $deliveryLongitude = $selectedAddress?->longitude ?? ($data['delivery_longitude'] ?? null);

            // Calculate distance and delivery fee using DeliveryHelper
            $deliveryCalculation = \App\Helpers\DeliveryHelper::calculateFee(
                $vendor->latitude !== null ? (float) $vendor->latitude : null,
                $vendor->longitude !== null ? (float) $vendor->longitude : null,
                $deliveryLatitude !== null ? (float) $deliveryLatitude : null,
                $deliveryLongitude !== null ? (float) $deliveryLongitude : null
            );

            $distanceInKm = $deliveryCalculation['distance_km'];
            $deliveryFee = $deliveryCalculation['delivery_fee'];
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
                'delivery_address' => $deliveryAddress,
                'delivery_latitude' => $deliveryLatitude,
                'delivery_longitude' => $deliveryLongitude,
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

        try {
            $order->loadMissing('vendor');
            $vendor = $order->vendor;

            if ($order && $vendor) {
                $vendorFcmToken = trim((string) ($vendor->fcm_token ?? ''));

                \Illuminate\Support\Facades\Log::info("Vendor FCM token check before notification for order #{$order->id} and vendor ID {$vendor->id}. " . ($vendorFcmToken === '' ? 'No FCM token found for this vendor.' : "FCM token is present: {$vendorFcmToken}"), [
                    'order_id' => $order->id,
                    'vendor_id' => $vendor->id,
                    'has_fcm_token' => $vendorFcmToken !== '',
                    'raw_fcm_token' => $vendor->fcm_token,
                ]);

                if ($vendorFcmToken !== '') {
                    \Illuminate\Support\Facades\Log::info("FCM token found for vendor ID {$vendor->id}: {$vendorFcmToken}");

                    $customerName = trim(($order->customer_first_name ?? '') . ' ' . ($order->customer_last_name ?? ''));
                    if (empty($customerName)) {
                        $customerName = $user->name ?? 'User';
                    }

                    $locale = app()->getLocale();
                    $title = ($locale === 'ar') ? 'طلب جديد' : 'New Order Received';
                    $body = ($locale === 'ar')
                        ? "لقد تلقيت طلباً جديداً رقم #{$order->id} من {$customerName}"
                        : "You have received a new order #{$order->id} from {$customerName}";

                    $sent = $this->fcmService->sendNotification($vendorFcmToken, $title, $body, [
                        'order_id' => (string) $order->id,
                        'customer_name' => $customerName,
                        'type' => 'new_order'
                    ]);

                    if ($sent) {
                        \Illuminate\Support\Facades\Log::info("FCM push notification sent successfully to vendor ID {$vendor->id} for order #{$order->id}");
                    } else {
                        \Illuminate\Support\Facades\Log::error("Failed to send FCM push notification to vendor ID {$vendor->id} for order #{$order->id}");
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning("Vendor ID {$vendor->id} does not have an FCM token. Notification was skipped for order #{$order->id}");
                }
            } else {
                \Illuminate\Support\Facades\Log::warning("Vendor not found for order #{$order->id}");
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('FCM Notification dispatch failed for order #' . ($order->id ?? 'unknown') . ': ' . $e->getMessage());
        }

        return $order;
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\DeliveryRefusedOrder;
use App\Models\DeliveryUser;
use App\Models\Order;
use App\Notifications\DeliveryOrderOfferNotification;
use App\Notifications\UserOrderStatusUpdatedNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliveryAutoAssignService
{
    private int $maxRefusalsPerDay = 5;

    private float $maxDistanceKm = 50.0;

    private int $distanceMatrixBatchSize = 25;

    public function notifyCandidates(Order $order, array $excludedDeliveryIds = []): int
    {
        Log::info("[DELIVERY_AUTO_ASSIGN] Starting candidates notification for Order #{$order->id}. Excluded drivers: " . json_encode($excludedDeliveryIds));

        $candidates = $this->eligibleCandidates($order, $excludedDeliveryIds);

        Log::info("[DELIVERY_AUTO_ASSIGN] Found " . $candidates->count() . " eligible candidate(s) for Order #{$order->id}. Candidates: " . json_encode($candidates->pluck('id')->toArray()));

        $order->update([
            'delivery_status' => $candidates->isNotEmpty() ? 'searching' : 'no_candidates',
        ]);

        $sentCount = 0;
        $title = app()->getLocale() === 'ar' ? 'طلب توصيل جديد' : 'New delivery order';
        $body = app()->getLocale() === 'ar'
            ? "يوجد طلب جديد جاهز للاستلام رقم #{$order->id}"
            : "A new order #{$order->id} is ready for pickup";

        foreach ($candidates as $candidate) {
            $candidate->notify(new DeliveryOrderOfferNotification($order));

            $token = trim((string) $candidate->fcm_token);

            if ($token === '') {
                Log::warning("[DELIVERY_AUTO_ASSIGN] Candidate ID {$candidate->id} has no FCM token. Notification skipped.");
                continue;
            }

            Log::info("[DELIVERY_AUTO_ASSIGN] Sending push notification to Candidate ID {$candidate->id} using token: " . substr($token, 0, 15) . "...");
            $sent = app(FcmService::class)->sendNotification($token, $title, $body, [
                'type' => 'delivery_order_offer',
                'order_id' => (string) $order->id,
                'vendor_id' => (string) $order->vendor_id,
            ]);

            if ($sent) {
                Log::info("[DELIVERY_AUTO_ASSIGN] Notification sent successfully to Candidate ID {$candidate->id} for Order #{$order->id}.");
                $sentCount++;
            } else {
                Log::error("[DELIVERY_AUTO_ASSIGN] Failed to send notification to Candidate ID {$candidate->id} for Order #{$order->id}.");
            }
        }

        Log::info("[DELIVERY_AUTO_ASSIGN] Finished notifying candidates for Order #{$order->id}. Total notified: {$sentCount}");
        return $sentCount;
    }

    public function accept(Order $order, DeliveryUser $deliveryUser): bool
    {
        Log::info("[DELIVERY_AUTO_ASSIGN] Driver ID {$deliveryUser->id} is attempting to accept Order #{$order->id}.");

        return DB::transaction(function () use ($order, $deliveryUser) {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! empty($lockedOrder->delivery_id)) {
                $isAlreadyAssignedToMe = (int) $lockedOrder->delivery_id === (int) $deliveryUser->id;
                if ($isAlreadyAssignedToMe) {
                    Log::info("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} is already assigned to Driver ID {$deliveryUser->id}. Succeeded.");
                } else {
                    Log::warning("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} acceptance failed. Already assigned to another driver (Driver ID: {$lockedOrder->delivery_id}).");
                }
                return $isAlreadyAssignedToMe;
            }

            if ($lockedOrder->status?->value !== \App\Enums\OrderStatus::Ready->value) {
                Log::warning("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} acceptance failed. Status is not Ready (Current status: {$lockedOrder->status?->value}).");
                return false;
            }

            if (! $this->eligibleCandidates($lockedOrder)->contains('id', $deliveryUser->id)) {
                Log::warning("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} acceptance failed. Driver ID {$deliveryUser->id} is not in the eligible candidates list.");
                return false;
            }

            $lockedOrder->update([
                'delivery_id' => $deliveryUser->id,
                'status' => OrderStatus::OnTheWay->value,
                'delivery_status' => 'assigned',
            ]);

            $this->notifyUserAboutOrderStatusChange($lockedOrder);

            Log::info("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} accepted successfully by Driver ID {$deliveryUser->id}. Status updated to 'on_the_way' and delivery status to 'assigned'.");
            return true;
        }, 3);
    }

    public function reject(Order $order, DeliveryUser $deliveryUser): void
    {
        Log::info("[DELIVERY_AUTO_ASSIGN] Driver ID {$deliveryUser->id} rejected Order #{$order->id}. Creating refusal record.");

        DeliveryRefusedOrder::firstOrCreate([
            'delivery_id' => $deliveryUser->id,
            'order_id' => $order->id,
        ]);
    }

    public function complete(Order $order, DeliveryUser $deliveryUser): bool
    {
        Log::info("[DELIVERY_AUTO_ASSIGN] Driver ID {$deliveryUser->id} is attempting to complete Order #{$order->id}.");

        return DB::transaction(function () use ($order, $deliveryUser) {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ((int) $lockedOrder->delivery_id !== (int) $deliveryUser->id) {
                Log::warning("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} completion failed. It is not assigned to Driver ID {$deliveryUser->id}.");
                return false;
            }

            if ($lockedOrder->status?->value === OrderStatus::Delivered->value) {
                Log::info("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} is already completed.");
                return true;
            }

            if ($lockedOrder->status?->value === OrderStatus::Cancelled->value) {
                Log::warning("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} completion failed. Order is cancelled.");
                return false;
            }

            $updates = [
                'status' => OrderStatus::Delivered->value,
                'delivery_status' => 'delivered',
            ];

            if ($lockedOrder->payment_method?->value === PaymentMethod::Cash->value) {
                $updates['payment_status'] = PaymentStatus::Paid->value;
            }

            $lockedOrder->update($updates);

            // Update delivery wallet if paid online (Paymob)
            if ($lockedOrder->payment_method?->value === PaymentMethod::Paymob->value) {
                $lockedDeliveryUser = DeliveryUser::query()->whereKey($deliveryUser->id)->lockForUpdate()->firstOrFail();
                $lockedDeliveryUser->increment('balance', $lockedOrder->delivery_fee);

                DB::table('delivery_wallet_transactions')->insert([
                    'delivery_id' => $lockedDeliveryUser->id,
                    'order_id' => $lockedOrder->id,
                    'type' => 'delivered_online',
                    'amount' => $lockedOrder->delivery_fee,
                    'note' => 'Delivery fee for order #' . $lockedOrder->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->notifyUserAboutOrderStatusChange($lockedOrder);

            Log::info("[DELIVERY_AUTO_ASSIGN] Order #{$order->id} completed successfully by Driver ID {$deliveryUser->id}.");
            return true;
        }, 3);
    }

    public function availableOrdersFor(DeliveryUser $deliveryUser): SupportCollection
    {
        if (
            $deliveryUser->approval_status !== 'approved' ||
            ! $deliveryUser->active_status ||
            $deliveryUser->ban ||
            $deliveryUser->lat === null ||
            $deliveryUser->lng === null
        ) {
            return collect();
        }

        return Order::query()
            ->whereNull('delivery_id')
            ->where('status', \App\Enums\OrderStatus::Ready->value)
            ->where(function ($query) {
                $query->whereNull('delivery_status')
                    ->orWhereIn('delivery_status', ['pending', 'searching']);
            })
            ->with('vendor')
            ->withCount('items')
            ->latest()
            ->get()
            ->filter(fn (Order $order) => $this->isEligibleForOrder($order, $deliveryUser))
            ->map(function (Order $order) use ($deliveryUser) {
                $order->distance_km = $this->resolveDistanceForDriverAndOrder($deliveryUser, $order);

                return $order;
            })
            ->sortBy('distance_km')
            ->values();
    }

    public function eligibleCandidates(Order $order, array $excludedDeliveryIds = []): Collection
    {
        $order->loadMissing('vendor');

        $targetLat = $order->vendor?->latitude !== null ? (float) $order->vendor->latitude : null;
        $targetLng = $order->vendor?->longitude !== null ? (float) $order->vendor->longitude : null;

        if ($targetLat === null || $targetLng === null) {
            Log::warning('[DELIVERY_AUTO_ASSIGN] Missing vendor coordinates.', ['order_id' => $order->id]);

            return collect();
        }

        $eligibleCandidates = new Collection();

        foreach ($this->candidateQuery($order)->get() as $deliveryUser) {
            $eligibility = $this->evaluateCandidateEligibility($order, $deliveryUser, $excludedDeliveryIds);

            if (! $eligibility['eligible']) {
                $this->logCandidateExclusion($order, $deliveryUser, $eligibility['reason'], $eligibility['context']);
                continue;
            }

            $deliveryUser->distance_km = $eligibility['distance_km'];
            $eligibleCandidates->push($deliveryUser);
        }

        return new Collection(
            $eligibleCandidates
                ->sortBy('distance_km')
                ->values()
                ->all()
        );
    }

    private function candidateQuery(Order $order)
    {
        return DeliveryUser::query()
            ->withCount([
                'activeOrders',
                'refusedOrders as refused_today_count' => fn ($query) => $query->whereDate('created_at', today()),
                'refusedOrders as refused_for_order_count' => fn ($query) => $query->where('order_id', $order->id),
            ])
            ->orderBy('id');
    }

    private function drivingDistances(Collection $candidates, float $targetLat, float $targetLng): array
    {
        $apiKey = trim((string) env('GOOGLE_MAPS_KEY', ''));

        if ($apiKey === '') {
            return [];
        }

        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
        $destination = "{$targetLat},{$targetLng}";
        $distances = [];

        foreach ($candidates->chunk($this->distanceMatrixBatchSize) as $chunk) {
            try {
                $response = Http::timeout(8)->get($url, [
                    'origins' => $chunk->map(fn (DeliveryUser $deliveryUser) => "{$deliveryUser->lat},{$deliveryUser->lng}")->implode('|'),
                    'destinations' => $destination,
                    'key' => $apiKey,
                    'mode' => 'driving',
                    'units' => 'metric',
                ]);
            } catch (\Throwable $exception) {
                Log::warning('[DELIVERY_AUTO_ASSIGN] Distance Matrix request failed.', [
                    'message' => $exception->getMessage(),
                ]);

                return [];
            }

            if (! $response->successful()) {
                return [];
            }

            $rows = $response->json('rows', []);

            foreach ($chunk->values() as $index => $deliveryUser) {
                $element = data_get($rows, "{$index}.elements.0");

                if (($element['status'] ?? null) !== 'OK') {
                    continue;
                }

                $meters = $element['distance']['value'] ?? null;

                if ($meters !== null) {
                    $distances[$deliveryUser->id] = $meters / 1000;
                }
            }
        }

        return $distances;
    }

    private function squaredDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        return (($fromLat - $toLat) ** 2) + (($fromLng - $toLng) ** 2);
    }

    private function isEligibleForOrder(Order $order, DeliveryUser $deliveryUser): bool
    {
        return $this->evaluateCandidateEligibility($order, $deliveryUser)['eligible'];
    }

    private function resolveDistanceForDriverAndOrder(DeliveryUser $deliveryUser, Order $order): float
    {
        $driverLat = (float) $deliveryUser->lat;
        $driverLng = (float) $deliveryUser->lng;
        $targetLat = (float) $order->vendor->latitude;
        $targetLng = (float) $order->vendor->longitude;

        $distances = $this->drivingDistances(new Collection([$deliveryUser]), $targetLat, $targetLng);

        if (isset($distances[$deliveryUser->id])) {
            return (float) $distances[$deliveryUser->id];
        }

        return sqrt($this->squaredDistance($driverLat, $driverLng, $targetLat, $targetLng)) * 111;
    }

    private function evaluateCandidateEligibility(Order $order, DeliveryUser $deliveryUser, array $excludedDeliveryIds = []): array
    {
        if ($order->vendor?->latitude === null || $order->vendor?->longitude === null) {
            return [
                'eligible' => false,
                'reason' => 'missing vendor location',
                'context' => [],
            ];
        }

        if ($deliveryUser->approval_status !== 'approved') {
            return [
                'eligible' => false,
                'reason' => 'not approved',
                'context' => ['approval_status' => $deliveryUser->approval_status],
            ];
        }

        if (! $deliveryUser->active_status) {
            return [
                'eligible' => false,
                'reason' => 'inactive',
                'context' => [],
            ];
        }

        if ($deliveryUser->ban) {
            return [
                'eligible' => false,
                'reason' => 'banned',
                'context' => [],
            ];
        }

        if ($deliveryUser->lat === null || $deliveryUser->lng === null) {
            return [
                'eligible' => false,
                'reason' => 'missing location',
                'context' => [],
            ];
        }

        if (in_array($deliveryUser->id, $excludedDeliveryIds, true)) {
            return [
                'eligible' => false,
                'reason' => 'excluded from current notification round',
                'context' => [],
            ];
        }

        $activeOrdersCount = $deliveryUser->active_orders_count ?? $deliveryUser->activeOrders()->count();
        $maxActiveOrders = max((int) ($deliveryUser->max_active_orders ?? 1), 1);

        if ($activeOrdersCount >= $maxActiveOrders) {
            return [
                'eligible' => false,
                'reason' => 'max active orders reached',
                'context' => [
                    'active_orders_count' => $activeOrdersCount,
                    'max_active_orders' => $maxActiveOrders,
                ],
            ];
        }

        $refusedForOrderCount = $deliveryUser->refused_for_order_count ?? $deliveryUser->refusedOrders()
            ->where('order_id', $order->id)
            ->count();

        if ($refusedForOrderCount > 0) {
            return [
                'eligible' => false,
                'reason' => 'refused this order',
                'context' => ['refused_for_order_count' => $refusedForOrderCount],
            ];
        }

        $refusedTodayCount = $deliveryUser->refused_today_count ?? $deliveryUser->refusedOrders()
            ->whereDate('created_at', today())
            ->count();

        if ($refusedTodayCount >= $this->maxRefusalsPerDay) {
            return [
                'eligible' => false,
                'reason' => 'refused today limit reached',
                'context' => [
                    'refused_today_count' => $refusedTodayCount,
                    'max_refusals_per_day' => $this->maxRefusalsPerDay,
                ],
            ];
        }

        $distanceKm = $this->resolveDistanceForDriverAndOrder($deliveryUser, $order);

        if ($distanceKm > $this->maxDistanceKm) {
            return [
                'eligible' => false,
                'reason' => 'too far',
                'context' => [
                    'distance_km' => round($distanceKm, 2),
                    'max_distance_km' => $this->maxDistanceKm,
                ],
            ];
        }

        return [
            'eligible' => true,
            'reason' => null,
            'distance_km' => $distanceKm,
            'context' => [
                'active_orders_count' => $activeOrdersCount,
                'max_active_orders' => $maxActiveOrders,
                'refused_today_count' => $refusedTodayCount,
            ],
        ];
    }

    private function logCandidateExclusion(Order $order, DeliveryUser $deliveryUser, string $reason, array $context = []): void
    {
        Log::info(
            "[DELIVERY_AUTO_ASSIGN] Excluding driver ID {$deliveryUser->id} for Order #{$order->id}: {$reason}",
            array_merge([
                'driver_id' => $deliveryUser->id,
                'order_id' => $order->id,
            ], $context)
        );
    }

    private function notifyUserAboutOrderStatusChange(Order $order): void
    {
        try {
            $user = $order->user;

            if (! $user) {
                Log::warning("No user model associated with order #{$order->id}. Notification skipped.");
                return;
            }

            $user->notify(new UserOrderStatusUpdatedNotification($order));

            $userFcmToken = trim((string) ($user->fcm_token ?? ''));

            if ($userFcmToken === '') {
                Log::warning("User ID {$user->id} does not have an FCM token. Notification skipped for order #{$order->id}.");
                return;
            }

            $locale = app()->getLocale();
            $statusLabelAr = $order->status->label();
            $statusLabelEn = match ($order->status) {
                OrderStatus::Pending => 'Pending',
                OrderStatus::Accepted => 'Accepted',
                OrderStatus::Preparing => 'Preparing',
                OrderStatus::Ready => 'Ready',
                OrderStatus::OnTheWay => 'On the way',
                OrderStatus::Delivered => 'Delivered',
                OrderStatus::Cancelled => 'Cancelled',
            };

            $title = ($locale === 'ar') ? 'تحديث حالة الطلب' : 'Order Status Update';
            $body = ($locale === 'ar')
                ? "تم تحديث حالة طلبك رقم #{$order->id} إلى {$statusLabelAr}"
                : "Your order #{$order->id} status has been updated to {$statusLabelEn}";

            $sent = app(FcmService::class)->sendNotification($userFcmToken, $title, $body, [
                'order_id' => (string) $order->id,
                'status' => $order->status->value,
                'type' => 'order_status_update',
            ]);

            if ($sent) {
                Log::info("FCM push notification sent successfully to user ID {$user->id} for status update of order #{$order->id}");
            } else {
                Log::error("Failed to send FCM push notification to user ID {$user->id} for order #{$order->id}");
            }
        } catch (\Throwable $e) {
            Log::error('FCM Notification dispatch failed for user order status update #' . $order->id . ': ' . $e->getMessage());
        }
    }
}

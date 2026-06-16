<?php

namespace App\Services\Api\Vendor;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Vendor;
use App\Repositories\Api\Vendor\VendorOrderRepository;
use App\Services\FcmService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class VendorOrderService
{
    public function __construct(
        protected VendorOrderRepository $repository,
        protected FcmService $fcmService
    ) {}

    public function index(Vendor $vendor, ?string $status, int $perPage): LengthAwarePaginator
    {
        return $this->repository->getPaginatedForVendor($vendor->id, $perPage, $status);
    }

    public function show(Vendor $vendor, int $orderId): Order
    {
        return $this->repository->findForVendor($orderId, $vendor->id);
    }

    public function updateStatus(Vendor $vendor, Order $order, array $data): Order
    {
        $newStatus = OrderStatus::from($data['status']);

        // Validate cancellation rules if status is Cancelled
        if ($newStatus === OrderStatus::Cancelled) {
            if (!$order->status || !$order->status->canBeCancelledByVendor()) {
                abort(422, __('validation.order_cannot_be_cancelled') ?: 'Order cannot be cancelled at this stage');
            }

            $updates = [
                'status' => OrderStatus::Cancelled,
                'cancelled_by' => 'vendor',
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
            ];

            if (
                $order->payment_method === PaymentMethod::Paymob
                && $order->payment_status === PaymentStatus::Paid
            ) {
                $updates['payment_status'] = PaymentStatus::Refunded;
            }

            $order->update($updates);
        } else {
            $order->update([
                'status' => $newStatus,
            ]);
        }

        // Send FCM push notification to the user (customer)
        try {
            $user = $order->user;
            if ($user) {
                $userFcmToken = trim((string) ($user->fcm_token ?? ''));
                if ($userFcmToken !== '') {
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

                    $sent = $this->fcmService->sendNotification($userFcmToken, $title, $body, [
                        'order_id' => (string) $order->id,
                        'status' => $order->status->value,
                        'type' => 'order_status_update'
                    ]);

                    if ($sent) {
                        Log::info("FCM push notification sent successfully to user ID {$user->id} for status update of order #{$order->id}");
                    } else {
                        Log::error("Failed to send FCM push notification to user ID {$user->id} for order #{$order->id}");
                    }
                } else {
                    Log::warning("User ID {$user->id} does not have an FCM token. Notification skipped for order #{$order->id}");
                }
            } else {
                Log::warning("No user model associated with order #{$order->id}. Notification skipped.");
            }
        } catch (\Throwable $e) {
            Log::error('FCM Notification dispatch failed for user order status update #' . $order->id . ': ' . $e->getMessage());
        }

        return $order->load(['vendor', 'items.product.images']);
    }

    public function getTodayStats(Vendor $vendor): array
    {
        return $this->repository->getTodayStatsForVendor($vendor->id);
    }
}

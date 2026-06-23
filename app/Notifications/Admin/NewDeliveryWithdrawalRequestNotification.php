<?php

namespace App\Notifications\Admin;

use App\Models\DeliveryWithdrawalRequest;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewDeliveryWithdrawalRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected DeliveryWithdrawalRequest $withdrawalRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $delivery = $this->withdrawalRequest->delivery;
        $title = 'طلب سحب جديد من المندوب';
        $message = "قام المندوب {$delivery?->name} بإرسال طلب سحب بقيمة {$this->withdrawalRequest->amount}.";

        if (! empty($notifiable->fcm_token)) {
            try {
                $fcm = new FcmService();

                $fcm->sendNotification($notifiable->fcm_token, $title, $message, [
                    'delivery_id' => $this->withdrawalRequest->delivery_id,
                    'delivery_withdrawal_request_id' => $this->withdrawalRequest->id,
                    'entity_id' => $this->withdrawalRequest->id,
                    'entity_type' => 'delivery_withdrawal_request',
                    'target_type' => 'dashboard',
                    'target_id' => '',
                    'type' => 'new_delivery_withdraw_request',
                ]);
            } catch (\Throwable $e) {
                Log::error('FCM Send to Admin Failed: ' . $e->getMessage());
            }
        }

        return [
            'title' => $title,
            'message' => $message,
            'target_type' => 'dashboard',
            'target_id' => null,
            'entity_type' => 'delivery_withdrawal_request',
            'entity_id' => $this->withdrawalRequest->id,
            'type' => 'new_delivery_withdraw_request',
            'sent_via' => ['database', 'fcm'],
            'delivery_id' => $this->withdrawalRequest->delivery_id,
            'delivery_withdrawal_request_id' => $this->withdrawalRequest->id,
            'amount' => (float) $this->withdrawalRequest->amount,
            'method' => $this->withdrawalRequest->method,
        ];
    }
}

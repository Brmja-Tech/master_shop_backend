<?php

namespace App\Notifications\Admin;

use App\Models\DeliveryUser;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewDeliveryRegistrationNotification extends Notification
{
    use Queueable;

    protected DeliveryUser $deliveryUser;

    public function __construct(DeliveryUser $deliveryUser)
    {
        $this->deliveryUser = $deliveryUser;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = 'طلب تسجيل مندوب جديد';
        $message = "قام المندوب {$this->deliveryUser->name} بالتسجيل وهو بانتظار الموافقة.";

        if (! empty($notifiable->fcm_token)) {
            try {
                $fcm = new FcmService();

                $fcm->sendNotification($notifiable->fcm_token, $title, $message, [
                    'delivery_user_id' => $this->deliveryUser->id,
                    'entity_id' => $this->deliveryUser->id,
                    'entity_type' => 'delivery_user',
                    'target_type' => 'dashboard',
                    'target_id' => '',
                    'type' => 'new_delivery_register',
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
            'entity_type' => 'delivery_user',
            'entity_id' => $this->deliveryUser->id,
            'type' => 'new_delivery_register',
            'sent_via' => ['database', 'fcm'],
            'delivery_user_id' => $this->deliveryUser->id,
            'phone' => $this->deliveryUser->phone,
        ];
    }
}

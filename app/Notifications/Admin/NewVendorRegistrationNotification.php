<?php

namespace App\Notifications\Admin;

use App\Models\Vendor;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewVendorRegistrationNotification extends Notification
{
    use Queueable;

    protected Vendor $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = 'طلب تسجيل متجر جديد';
        $message = "قام المتجر {$this->vendor->store_name} بالتسجيل وهو بانتظار الموافقة.";

        if (! empty($notifiable->fcm_token)) {
            try {
                $fcm = new FcmService();

                $fcm->sendNotification($notifiable->fcm_token, $title, $message, [
                    'vendor_id' => $this->vendor->id,
                    'entity_id' => $this->vendor->id,
                    'entity_type' => 'vendor',
                    'target_type' => 'dashboard',
                    'target_id' => '',
                    'type' => 'new_vendor_register',
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
            'entity_type' => 'vendor',
            'entity_id' => $this->vendor->id,
            'type' => 'new_vendor_register',
            'sent_via' => ['database', 'fcm'],
            'vendor_id' => $this->vendor->id,
            'phone' => $this->vendor->phone,
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryStatusNotify extends Notification
{
    use Queueable;

    protected string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return [\App\Channels\WhatsappChannel::class];
    }

    public function toBeon(object $notifiable): array
    {
        $siteName = \App\Models\Setting::first()->site_name ?? 'Master Shop';
        
        if ($this->status === 'approved') {
            $message = "أهلاً {$notifiable->name}، لقد تم قبول طلب انضمامك كمندوب توصيل في تطبيق {$siteName} بنجاح! يمكنك الآن تسجيل الدخول إلى التطبيق والبدء بالعمل.";
        } else {
            $message = "أهلاً {$notifiable->name}، نأسف لإبلاغك بأنه قد تم رفض طلب انضمامك كمندوب توصيل في تطبيق {$siteName}. لمزيد من التفاصيل يرجى التواصل مع الدعم الفني.";
        }

        return [
            'phone'   => $notifiable->phone,
            'message' => $message,
            'name'    => $notifiable->name ?? 'Delivery User',
            'type'    => 'sms',
        ];
    }
}

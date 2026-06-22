<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UserOrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage|array
    {
        return new DatabaseMessage($this->payload());
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    private function payload(): array
    {
        return [
            'type' => 'order_status_update',
            'order_id' => $this->order->id,
            'status' => $this->order->status->value,
            'title' => 'تحديث حالة الطلب',
            'message' => "تم تحديث حالة طلبك رقم #{$this->order->id} إلى {$this->order->status->label()}",
        ];
    }
}

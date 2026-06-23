<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class DeliveryOrderOfferNotification extends Notification
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
            'type' => 'delivery_order_offer',
            'order_id' => $this->order->id,
            'vendor_id' => $this->order->vendor_id,
            'status' => $this->order->status->value,
            'title' => 'طلب توصيل جديد',
            'message' => "يوجد طلب جديد جاهز للاستلام رقم #{$this->order->id}",
        ];
    }
}

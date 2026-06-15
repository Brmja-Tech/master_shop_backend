<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case PickedUp = 'picked_up';
    case OnTheWay = 'on_the_way';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Accepted => 'تم القبول',
            self::Preparing => 'قيد التحضير',
            self::Ready => 'جاهز للاستلام',
            self::PickedUp => 'تم الاستلام من السائق',
            self::OnTheWay => 'في الطريق',
            self::Delivered => 'تم التسليم',
            self::Cancelled => 'تم الإلغاء',
        };
    }

    public function canBeCancelledByUser(): bool
    {
        return in_array($this, [self::Pending, self::Accepted], true);
    }

    public function canBeCancelledByVendor(): bool
    {
        return in_array($this, [self::Pending, self::Accepted, self::Preparing], true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }
}

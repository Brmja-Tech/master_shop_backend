<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case Searching = 'searching';
    case Assigned = 'assigned';
    case HeadingToVendor = 'heading_to_vendor';
    case ArrivedAtVendor = 'arrived_at_vendor';
    case PickedUp = 'picked_up';
    case HeadingToUser = 'heading_to_user';
    case Arrived = 'arrived';
    case Delivered = 'delivered';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Searching => 'جاري البحث عن سائق',
            self::Assigned => 'تم تعيين السائق',
            self::HeadingToVendor => 'السائق في الطريق إلى المتجر',
            self::ArrivedAtVendor => 'السائق وصل إلى المتجر',
            self::PickedUp => 'تم استلام الطلب',
            self::HeadingToUser => 'السائق في الطريق إلى العميل',
            self::Arrived => 'السائق وصل',
            self::Delivered => 'تم التسليم',
            self::Failed => 'فشل التوصيل',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Failed], true);
    }

    public function isActive(): bool
    {
        return ! $this->isTerminal();
    }
}

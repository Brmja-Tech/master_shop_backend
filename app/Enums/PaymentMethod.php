<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Paymob = 'paymob';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'كاش',
            self::Paymob => 'بايموب',
        };
    }

    public function requiresOnlinePayment(): bool
    {
        return $this === self::Paymob;
    }
}

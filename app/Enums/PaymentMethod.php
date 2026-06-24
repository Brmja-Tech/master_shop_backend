<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Paymob = 'paymob';

    public function label(): string
    {
        return __('dashboard.' . $this->value);
    }

    public function requiresOnlinePayment(): bool
    {
        return $this === self::Paymob;
    }
}

<?php

namespace App\Enums;

enum DeliveryWithdrawalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}

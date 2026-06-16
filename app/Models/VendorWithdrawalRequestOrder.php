<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorWithdrawalRequestOrder extends Model
{
    protected $fillable = [
        'vendor_withdrawal_request_id',
        'order_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function withdrawalRequest(): BelongsTo
    {
        return $this->belongsTo(VendorWithdrawalRequest::class, 'vendor_withdrawal_request_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

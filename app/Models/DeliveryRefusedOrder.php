<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRefusedOrder extends Model
{
    protected $fillable = [
        'delivery_id',
        'order_id',
    ];

    public function deliveryUser(): BelongsTo
    {
        return $this->belongsTo(DeliveryUser::class, 'delivery_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'customer_first_name',
        'customer_last_name',
        'customer_phone',
        'status',
        'payment_method',
        'payment_status',
        'paymob_order_id',
        'paymob_transaction_id',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'subtotal',
        'discount_amount',
        'delivery_fee',
        'total',
        'notes',
        'delivered_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            OrderStatus::Delivered->value,
            OrderStatus::Cancelled->value,
        ]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending->value);
    }

    public function isCancellable(): bool
    {
        return $this->status?->canBeCancelledByUser() ?? false;
    }

    public function isPaid(): bool
    {
        return $this->payment_status->isPaid();
    }
}

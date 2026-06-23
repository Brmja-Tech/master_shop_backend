<?php

namespace App\Models;

use App\Enums\DeliveryWithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryWithdrawalRequest extends Model
{
    protected $fillable = [
        'delivery_id',
        'method',
        'transfer_details',
        'amount',
        'status',
        'admin_note',
        'processed_by_admin_id',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => DeliveryWithdrawalStatus::class,
        'processed_at' => 'datetime',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(DeliveryUser::class, 'delivery_id');
    }

    public function processedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by_admin_id');
    }

    public function orderAllocations(): HasMany
    {
        return $this->hasMany(DeliveryWithdrawalRequestOrder::class, 'delivery_withdrawal_request_id');
    }
}

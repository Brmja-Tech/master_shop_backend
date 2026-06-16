<?php

namespace App\Models;

use App\Enums\VendorWithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorWithdrawalRequest extends Model
{
    protected $fillable = [
        'vendor_id',
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
        'status' => VendorWithdrawalStatus::class,
        'processed_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function processedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by_admin_id');
    }

    public function orderAllocations(): HasMany
    {
        return $this->hasMany(VendorWithdrawalRequestOrder::class, 'vendor_withdrawal_request_id');
    }
}

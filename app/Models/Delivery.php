<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'driver_id',
        'status',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_address',
        'dropoff_latitude',
        'dropoff_longitude',
        'distance_km',
        'estimated_minutes',
        'driver_earnings',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'driver_rating',
        'driver_rating_comment',
        'failure_reason',
    ];

    protected $casts = [
        'status' => DeliveryStatus::class,
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'dropoff_latitude' => 'decimal:7',
        'dropoff_longitude' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'driver_earnings' => 'decimal:2',
        'estimated_minutes' => 'integer',
        'driver_rating' => 'integer',
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function assignDriver(Driver $driver): void
    {
        $this->update([
            'driver_id' => $driver->id,
            'status' => DeliveryStatus::Assigned,
            'assigned_at' => now(),
        ]);

        $driver->update([
            'is_busy' => true,
        ]);
    }
}

<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DeliveryUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'delivery_users';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'img',
        'front_ident',
        'back_ident',
        'personal_deriving_license',
        'machine_license',
        'active_status',
        'ban',
        'approval_status',
        'lat',
        'lng',
        'balance',
        'max_active_orders',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'fcm_token',
        'remember_token',
    ];

    protected $casts = [
        'active_status' => 'boolean',
        'ban' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'balance' => 'decimal:2',
        'max_active_orders' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }

    public function activeOrders(): HasMany
    {
        return $this->orders()
            ->where(function ($query) {
                $query->whereIn('delivery_status', [
                    'assigned',
                    'picked_up',
                ])->orWhereIn('status', [
                    OrderStatus::Accepted->value,
                    OrderStatus::OnTheWay->value,
                ]);
            });
    }

    public function refusedOrders(): HasMany
    {
        return $this->hasMany(DeliveryRefusedOrder::class, 'delivery_id');
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(DeliveryWithdrawalRequest::class, 'delivery_id');
    }
}

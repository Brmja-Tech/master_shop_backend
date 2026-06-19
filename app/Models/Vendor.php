<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;

    protected $fillable = [
        'owner_name',
        'phone',
        'password',
        'store_name',
        'description',
        'store_type_id',
        'latitude',
        'longitude',
        'address_description',
        'logo',
        'banner',
        'rate',
        'is_active',
        'is_store_open',
        'is_accepting_orders',
        'working_hours',
        'work_from',
        'work_to',
        'is_verified',
        'temp_token',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'temp_token',
        'fcm_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_store_open' => 'boolean',
        'is_accepting_orders' => 'boolean',
        'is_verified' => 'boolean',
        'rate' => 'decimal:2',
    ];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'store_type_id', 'store_type_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(VendorWithdrawalRequest::class);
    }
}

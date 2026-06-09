<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Vendor extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;

    protected $fillable = [
        'owner_name',
        'phone',
        'password',
        'store_name',
        'store_type_id',
        'latitude',
        'longitude',
        'address_description',
        'logo',
        'banner',
        'delivery_fee',
        'is_active',
        'working_hours',
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
        'is_verified' => 'boolean',
        'delivery_fee' => 'decimal:2',
    ];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'store_type_id', 'store_type_id');
    }
}

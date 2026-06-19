<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySetting extends Model
{
    protected $table = 'delivery_settings';

    protected $fillable = [
        'price_per_km',
        'min_delivery_fee',
    ];
}

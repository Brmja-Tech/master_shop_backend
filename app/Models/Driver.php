<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'is_busy',
    ];

    protected $casts = [
        'is_busy' => 'boolean',
    ];
}

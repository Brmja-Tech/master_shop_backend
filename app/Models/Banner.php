<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $table = 'banners';

    protected $fillable = [
        'banner',
        'status'
    ];
}

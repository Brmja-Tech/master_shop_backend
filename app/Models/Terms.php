<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Terms extends Model
{
    use HasTranslations;
    public $table = 'terms';
    public $translatable = [
        'title',
        'desc',
    ];

    protected $fillable = [
        'banner',
        'title',
        'desc',
        'image',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class About extends Model
{
    use HasTranslations;
    public $table = 'abouts';
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

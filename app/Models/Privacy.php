<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Privacy extends Model
{
    use HasTranslations;
    public $table = 'privacies';
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

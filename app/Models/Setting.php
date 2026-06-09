<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasTranslations;

    protected $table = 'settings';

    /**
     * الحقول المترجمة (تتخزن كـ JSON في نفس الأعمدة)
     */
    public $translatable = [
        'site_name',
        'site_desc',
        'site_title',
        'site_address',
        'meta_key',
        'meta_desc',
    ];

    /**
     * الحقول القابلة للملء
     */
    protected $fillable = [
        'site_name',
        'site_desc',
        'site_title',
        'site_phone',
        'site_address',
        'site_email',
        'email_support',

        'facebook',
        'x_url',
        'youtube',
        'instagram',
        'tiktok',
        'linkedin',
        'whatsapp',

        'meta_key',
        'meta_desc',

        'logo',
        'favicon',

        'site_copyright',
        'promotion_url',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Role extends Model
{
    use HasTranslations;

    public $translatable = ['role'];

    protected $guarded = [];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }
}

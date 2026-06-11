<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class StoreType extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'image',
    ];

    public array $translatable = [
        'name',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
}

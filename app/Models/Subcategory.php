<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Subcategory extends Model
{
    use HasTranslations;

    protected $fillable = [
        'store_type_id',
        'name',
    ];

    public array $translatable = [
        'name',
    ];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

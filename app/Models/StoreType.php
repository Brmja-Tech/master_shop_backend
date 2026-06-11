<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreType extends Model
{
    protected $fillable = [
        'name',
        'image',
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

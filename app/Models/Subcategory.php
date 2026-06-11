<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $fillable = [
        'store_type_id',
        'vendor_id',
        'name',
    ];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

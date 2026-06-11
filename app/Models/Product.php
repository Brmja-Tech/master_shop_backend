<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'subcategory_id',
        'name',
        'description',
        'quantity',
        'remaining_quantity',
        'discount',
        'is_available',
        'unit',
        'price',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'discount' => 'decimal:2',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderByDesc('is_main')
            ->orderBy('id');
    }

    public function mainImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->where('is_main', true);
    }

    protected function priceAfterDiscount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $price = (float) $this->price;
                $discount = (float) $this->discount;

                return max($price - (($price * $discount) / 100), 0);
            }
        );
    }
}

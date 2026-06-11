<?php

namespace App\Repositories\Api\Vendor;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository
{
    private array $relations = ['subcategory', 'images'];

    public function getPublic()
    {
        return Product::query()
            ->with($this->relations)
            ->latest()
            ->get();
    }

    public function search(?string $search)
    {
        return Product::query()
            ->with(['subcategory', 'images', 'vendor.storeType'])
            ->when($search, function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name->ar', 'like', '%' . $search . '%')
                        ->orWhere('name->en', 'like', '%' . $search . '%')
                        ->orWhereHas('vendor', function (Builder $vendorQuery) use ($search) {
                            $vendorQuery->where('store_name', 'like', '%' . $search . '%')
                                ->orWhereHas('storeType', function (Builder $storeTypeQuery) use ($search) {
                                    $storeTypeQuery->where('name->ar', 'like', '%' . $search . '%')
                                        ->orWhere('name->en', 'like', '%' . $search . '%');
                                });
                        })
                        ->orWhereHas('subcategory', function (Builder $subcategoryQuery) use ($search) {
                            $subcategoryQuery->where('name->ar', 'like', '%' . $search . '%')
                                ->orWhere('name->en', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->get();
    }

    public function getAvailable(): Collection
    {
        return Product::query()
            ->with($this->relations)
            ->where('is_available', true)
            ->where('remaining_quantity', '>', 0)
            ->latest()
            ->get();
    }

    public function getAllForVendor(int $vendorId): Collection
    {
        return Product::query()
            ->with($this->relations)
            ->where('vendor_id', $vendorId)
            ->latest()
            ->get();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function duplicateExists(
        int $vendorId,
        array $name,
        ?int $ignoreId = null
    ): bool {
        return Product::query()
            ->where('vendor_id', $vendorId)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where(function (Builder $query) use ($name) {
                if (! empty($name['ar'])) {
                    $query->orWhere('name->ar', $name['ar']);
                }

                if (! empty($name['en'])) {
                    $query->orWhere('name->en', $name['en']);
                }
            })
            ->exists();
    }

    public function createImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $product->images()->create([
                'image' => $image,
                'is_main' => false,
            ]);
        }
    }

    public function createMainImage(Product $product, string $image): void
    {
        $product->images()->create([
            'image' => $image,
            'is_main' => true,
        ]);
    }

    public function findVendorProduct(int $id, int $vendorId): Product
    {
        return Product::query()
            ->with($this->relations)
            ->where('vendor_id', $vendorId)
            ->findOrFail($id);
    }

    public function findVendorSubcategory(int $subcategoryId, int $storeTypeId): Subcategory
    {
        return Subcategory::query()
            ->where('store_type_id', $storeTypeId)
            ->findOrFail($subcategoryId);
    }

    public function findStoreTypeSubcategoryByName(int $storeTypeId, array $name): ?Subcategory
    {
        return Subcategory::query()
            ->where('store_type_id', $storeTypeId)
            ->where(function (Builder $query) use ($name) {
                if (! empty($name['ar'])) {
                    $query->orWhere('name->ar', $name['ar']);
                }

                if (! empty($name['en'])) {
                    $query->orWhere('name->en', $name['en']);
                }
            })
            ->first();
    }

    public function createSubcategory(array $data): Subcategory
    {
        return Subcategory::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh($this->relations);
    }

    public function deleteImages(Product $product): void
    {
        $product->images()->delete();
    }

    public function deleteMainImage(Product $product): void
    {
        $product->images()->where('is_main', true)->delete();
    }

    public function deleteGalleryImages(Product $product): void
    {
        $product->images()->where('is_main', false)->delete();
    }

    public function deleteSelectedGalleryImages(Product $product, array $imageIds): void
    {
        $product->images()
            ->where('is_main', false)
            ->whereIn('id', $imageIds)
            ->delete();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}

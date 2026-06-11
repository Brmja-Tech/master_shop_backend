<?php

namespace App\Services\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Resources\Api\Vendor\AvailableProductResource;
use App\Http\Resources\Api\Vendor\ProductResource;
use App\Models\Vendor;
use App\Repositories\Api\Vendor\ProductRepository;
use App\Utils\ImageManger;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository,
        protected ImageManger $imageManger
    ) {}

    public function index(Vendor $vendor)
    {
        return ProductResource::collection(
            $this->repository->getAllForVendor($vendor->id)
        );
    }

    public function publicIndex()
    {
        return AvailableProductResource::collection(
            $this->repository->getPublic()
        );
    }

    public function available()
    {
        return AvailableProductResource::collection(
            $this->repository->getAvailable()
        );
    }

    public function store(Vendor $vendor, array $data)
    {
        $data['subcategory_id'] = $this->resolveSubcategoryId($vendor, $data);

        if ($this->repository->duplicateExists($vendor->id, $data['name'])) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(422, __('product.already_exists'), [])
            );
        }

        $data['vendor_id'] = $vendor->id;
        $data['price'] = (float) $data['price'];
        $data['discount'] = isset($data['discount']) ? (float) $data['discount'] : 0;
        $data['quantity'] = (int) $data['quantity'];
        $data['remaining_quantity'] ??= $data['quantity'];
        $data['remaining_quantity'] = (int) $data['remaining_quantity'];
        $data['is_available'] = isset($data['is_available']) ? (bool) $data['is_available'] : true;

        $mainImage = $data['main_image'];
        $images = $data['images'] ?? [];
        unset($data['images'], $data['main_image']);

        $product = $this->repository->create($data);

        $storedMainImage = $this->imageManger->uploadImage('/uploads/products', $mainImage);
        $this->repository->createMainImage($product, $storedMainImage);

        if (! empty($images)) {
            $storedImages = $this->imageManger->uploadMultiImage('/uploads/products', $images);
            $this->repository->createImages($product, $storedImages);
            $product->load('images');
        }

        return new ProductResource($product->fresh(['subcategory', 'images']));
    }

    public function show(Vendor $vendor, int $id)
    {
        return new ProductResource(
            $this->repository->findVendorProduct($id, $vendor->id)
        );
    }

    public function update(Vendor $vendor, int $id, array $data)
    {
        $product = $this->repository->findVendorProduct($id, $vendor->id);

        $subcategoryId = $this->resolveSubcategoryId($vendor, $data, $product->subcategory_id);
        $name = $data['name'] ?? $product->getTranslations('name');
        $currentName = $product->getTranslations('name');
        $subcategoryChanged = $subcategoryId !== $product->subcategory_id;
        $nameChanged = ($name['ar'] ?? null) !== ($currentName['ar'] ?? null)
            || ($name['en'] ?? null) !== ($currentName['en'] ?? null);
        $data['subcategory_id'] = $subcategoryId;

        if (($subcategoryChanged || $nameChanged)
            && $this->repository->duplicateExists($vendor->id, $name, $product->id)) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(422, __('product.already_exists'), [])
            );
        }

        if (isset($data['quantity']) && ! isset($data['remaining_quantity'])) {
            $data['remaining_quantity'] = $data['quantity'];
        }

        if (isset($data['price'])) {
            $data['price'] = (float) $data['price'];
        }

        if (array_key_exists('discount', $data)) {
            $data['discount'] = $data['discount'] === null ? 0 : (float) $data['discount'];
        }

        if (isset($data['quantity'])) {
            $data['quantity'] = (int) $data['quantity'];
        }

        if (isset($data['remaining_quantity'])) {
            $data['remaining_quantity'] = (int) $data['remaining_quantity'];
        }

        if (isset($data['is_available'])) {
            $data['is_available'] = (bool) $data['is_available'];
        }

        $mainImage = $data['main_image'] ?? null;
        $images = $data['images'] ?? null;
        $deleteImageIds = $data['delete_image_ids'] ?? [];
        unset($data['images'], $data['main_image'], $data['delete_image_ids']);

        $product = $this->repository->update($product, $data);

        if ($mainImage) {
            $oldMainImage = $product->images->firstWhere('is_main', true);

            if ($oldMainImage) {
                $this->imageManger->deleteImage($oldMainImage->image);
            }

            $this->repository->deleteMainImage($product);

            $storedMainImage = $this->imageManger->uploadImage('/uploads/products', $mainImage);
            $this->repository->createMainImage($product, $storedMainImage);
        }

        if (is_array($images)) {
            foreach ($product->images->where('is_main', false) as $image) {
                $this->imageManger->deleteImage($image->image);
            }

            $this->repository->deleteGalleryImages($product);

            $storedImages = $this->imageManger->uploadMultiImage('/uploads/products', $images);
            $this->repository->createImages($product, $storedImages);
        }

        if (! empty($deleteImageIds)) {
            $imagesToDelete = $product->images
                ->where('is_main', false)
                ->whereIn('id', $deleteImageIds);

            foreach ($imagesToDelete as $image) {
                $this->imageManger->deleteImage($image->image);
            }

            $this->repository->deleteSelectedGalleryImages($product, $deleteImageIds);
        }

        return new ProductResource($product->fresh(['subcategory', 'images']));
    }

    public function destroy(Vendor $vendor, int $id): void
    {
        $product = $this->repository->findVendorProduct($id, $vendor->id);

        foreach ($product->images as $image) {
            $this->imageManger->deleteImage($image->image);
        }

        $this->repository->deleteImages($product);
        $this->repository->delete($product);
    }

    private function resolveSubcategoryId(Vendor $vendor, array &$data, ?int $fallbackId = null): int
    {
        if (! empty($data['subcategory_id'])) {
            $subcategory = $this->repository->findVendorSubcategory(
                (int) $data['subcategory_id'],
                $vendor->store_type_id
            );

            unset($data['subcategory_name']);

            return $subcategory->id;
        }

        if (! empty($data['subcategory_name'])) {
            $subcategory = $this->repository->findStoreTypeSubcategoryByName(
                $vendor->store_type_id,
                $data['subcategory_name']
            );

            if (! $subcategory) {
                $subcategory = $this->repository->createSubcategory([
                    'store_type_id' => $vendor->store_type_id,
                    'vendor_id' => $vendor->id,
                    'name' => $data['subcategory_name'],
                ]);
            }

            unset($data['subcategory_name']);

            return $subcategory->id;
        }

        return $fallbackId ?? 0;
    }
}

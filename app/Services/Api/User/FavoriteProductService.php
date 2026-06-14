<?php

namespace App\Services\Api\User;

use App\Http\Resources\Api\Vendor\AvailableProductResource;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Api\Vendor\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteProductService
{
    public function __construct(
        protected ProductRepository $productRepository
    ) {}

    public function index(User $user, int $perPage): array
    {
        $favorites = $this->productRepository->getFavoriteProductsPaginated($user, $perPage);

        return [
            'favorites' => AvailableProductResource::collection($favorites),
            'pagination' => $this->formatPaginatedResponse($favorites),
        ];
    }

    public function store(User $user, int $productId): array
    {
        $product = $this->findAvailableProduct($productId);
        $user->favoriteProducts()->syncWithoutDetaching([$product->id]);

        return [
            'message' => __('front.add-to-wishlist'),
            'product' => new AvailableProductResource(
                $this->productRepository->findPublicProductForUser($product->id, $user)
            ),
        ];
    }

    public function destroy(User $user, int $productId): array
    {
        $product = $this->findAvailableProduct($productId);
        $user->favoriteProducts()->detach($product->id);

        return [
            'message' => __('front.remove-from-wishlist'),
            'product' => new AvailableProductResource(
                $this->productRepository->findPublicProductForUser($product->id, $user)
            ),
        ];
    }

    private function findAvailableProduct(int $productId): Product
    {
        return Product::query()
            ->where('is_available', true)
            ->where('remaining_quantity', '>', 0)
            ->findOrFail($productId);
    }

    private function formatPaginatedResponse(LengthAwarePaginator $favorites): array
    {
        return [
            'total' => $favorites->total(),
            'current_page' => $favorites->currentPage(),
            'last_page' => $favorites->lastPage(),
            'per_page' => $favorites->perPage(),
        ];
    }
}

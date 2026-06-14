<?php

namespace App\Services\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Resources\Api\User\CartItemResource;
use App\Models\User;
use App\Repositories\Api\User\CartRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartService
{
    public function __construct(
        protected CartRepository $repository
    ) {}

    public function index(User $user, int $perPage): array
    {
        $paginatedItems = $this->repository->getUserCartItemsPaginated($user, $perPage);
        $allItems = $this->repository->getUserCartItems($user);

        return [
            'items' => CartItemResource::collection($paginatedItems),
            'summary' => $this->buildSummary($allItems),
            'pagination' => $this->formatPaginatedResponse($paginatedItems),
        ];
    }

    public function store(User $user, int $productId, int $quantity): CartItemResource
    {
        $product = $this->repository->findAvailableProduct($productId);
        $existingItem = $this->repository->findUserProductCartItem($user, $productId);

        $targetQuantity = $existingItem
            ? $existingItem->quantity + $quantity
            : $quantity;

        $this->ensureQuantityAvailable($product->remaining_quantity, $targetQuantity);

        $item = $existingItem
            ? $this->repository->updateQuantity($existingItem, $targetQuantity)
            : $this->repository->create($user, $product, $quantity);

        return new CartItemResource($item);
    }

    public function update(User $user, int $cartItemId, int $quantity): CartItemResource
    {
        $cartItem = $this->repository->findUserCartItem($user, $cartItemId);
        $product = $cartItem->product;

        $this->ensureQuantityAvailable($product->remaining_quantity, $quantity);

        return new CartItemResource(
            $this->repository->updateQuantity($cartItem, $quantity)
        );
    }

    public function destroy(User $user, int $cartItemId): void
    {
        $cartItem = $this->repository->findUserCartItem($user, $cartItemId);
        $this->repository->delete($cartItem);
    }

    public function clear(User $user): void
    {
        $this->repository->clear($user);
    }

    private function buildSummary($items): array
    {
        $subtotal = $items->sum(function ($item) {
            return (float) $item->product->price_after_discount * (int) $item->quantity;
        });

        return [
            'items_count' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'subtotal' => $subtotal,
        ];
    }

    private function formatPaginatedResponse(LengthAwarePaginator $items): array
    {
        return [
            'total' => $items->total(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'per_page' => $items->perPage(),
        ];
    }

    private function ensureQuantityAvailable(int $remainingQuantity, int $requestedQuantity): void
    {
        if ($requestedQuantity > $remainingQuantity) {
            throw new HttpResponseException(
                ApiResponse::sendResponse(422, 'الكمية المطلوبة غير متاحة', [
                    'quantity' => ['الكمية المطلوبة غير متاحة'],
                ])
            );
        }
    }
}

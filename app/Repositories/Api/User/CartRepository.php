<?php

namespace App\Repositories\Api\User;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CartRepository
{
    private array $relations = ['product.images', 'product.subcategory'];

    public function getUserCartItems(User $user): Collection
    {
        return CartItem::query()
            ->with($this->relations)
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function getUserCartItemsPaginated(User $user, int $perPage): LengthAwarePaginator
    {
        return CartItem::query()
            ->with($this->relations)
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function findUserCartItem(User $user, int $cartItemId): CartItem
    {
        return CartItem::query()
            ->with($this->relations)
            ->where('user_id', $user->id)
            ->findOrFail($cartItemId);
    }

    public function findUserProductCartItem(User $user, int $productId): ?CartItem
    {
        return CartItem::query()
            ->with($this->relations)
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();
    }

    public function create(User $user, Product $product, int $quantity): CartItem
    {
        return CartItem::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ])->load($this->relations);
    }

    public function updateQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh($this->relations);
    }

    public function delete(CartItem $cartItem): bool
    {
        return $cartItem->delete();
    }

    public function clear(User $user): void
    {
        CartItem::query()
            ->where('user_id', $user->id)
            ->delete();
    }

    public function findAvailableProduct(int $productId): Product
    {
        return Product::query()
            ->with(['images'])
            ->where('is_available', true)
            ->where('remaining_quantity', '>', 0)
            ->findOrFail($productId);
    }
}

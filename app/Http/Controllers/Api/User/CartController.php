<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\CartItemStoreRequest;
use App\Http\Requests\Api\User\CartItemUpdateRequest;
use App\Services\Api\User\CartService;

class CartController extends Controller
{
    public function __construct(
        protected CartService $service
    ) {}

    public function index()
    {
        $result = $this->service->index(
            auth('sanctum')->user(),
            request()->integer('per_page')
                ?: request()->integer('limit')
                ?: 10
        );

        return ApiResponse::sendResponse(
            200,
            'Cart retrieved successfully',
            [
                'items' => $result['items'],
                'summary' => $result['summary'],
            ],
            $result['pagination']
        );
    }

    public function store(CartItemStoreRequest $request)
    {
        $user = auth('sanctum')->user();
        $product = \App\Models\Product::findOrFail($request->integer('product_id'));

        $incomingVendorId = $product->vendor_id;

        $existingVendorId = $user
            ->cartItems()
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->value('products.vendor_id');

        if ($existingVendorId && $existingVendorId !== $incomingVendorId) {
            return response()->json([
                'message' => 'Your cart contains items from another vendor. Please clear your cart first.',
                'current_vendor_id' => $existingVendorId,
            ], 422);
        }

        return ApiResponse::sendResponse(
            200,
            'Product added to cart successfully',
            $this->service->store(
                $user,
                $request->integer('product_id'),
                $request->integer('quantity') ?: 1
            )
        );
    }

    public function update(CartItemUpdateRequest $request, int $id)
    {
        return ApiResponse::sendResponse(
            200,
            'Cart item updated successfully',
            $this->service->update(
                auth('sanctum')->user(),
                $id,
                $request->integer('quantity')
            )
        );
    }

    public function destroy(int $id)
    {
        $this->service->destroy(auth('sanctum')->user(), $id);

        return ApiResponse::sendResponse(
            200,
            'Cart item removed successfully'
        );
    }

    public function clear()
    {
        $this->service->clear(auth('sanctum')->user());

        return ApiResponse::sendResponse(
            200,
            'Cart cleared successfully'
        );
    }
}

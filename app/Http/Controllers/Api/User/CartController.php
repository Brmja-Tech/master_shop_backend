<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\CartItemStoreRequest;
use App\Http\Requests\Api\User\CartItemUpdateRequest;
use App\Http\Requests\Api\User\CheckoutSummaryRequest;
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
            __('front.cart-retrieved-successfully'),
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
                'message' => __('front.cart-vendor-conflict'),
                'current_vendor_id' => $existingVendorId,
            ], 422);
        }

        return ApiResponse::sendResponse(
            200,
            __('front.product-add-to-cart'),
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
            __('front.cart-item-updated-successfully'),
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
            __('front.product-removed-from-cart')
        );
    }

    public function clear()
    {
        $this->service->clear(auth('sanctum')->user());

        return ApiResponse::sendResponse(
            200,
            __('front.cart-cleared-successfully')
        );
    }

    public function checkoutSummary(CheckoutSummaryRequest $request)
    {
        try {
            $result = $this->service->checkoutSummary(
                auth('sanctum')->user(),
                $request->validated()
            );

            return ApiResponse::sendResponse(
                200,
                __('front.order-summary-calculated-successfully'),
                $result
            );
        } catch (\Exception $e) {
            return ApiResponse::sendResponse(422, $e->getMessage());
        }
    }
}

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
        return ApiResponse::sendResponse(
            200,
            'Product added to cart successfully',
            $this->service->store(
                auth('sanctum')->user(),
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

<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Api\User\FavoriteProductService;

class FavoriteProductController extends Controller
{
    public function __construct(
        protected FavoriteProductService $service
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
            __('front.wishlist'),
            $result['favorites'],
            $result['pagination']
        );
    }

    public function store(int $id)
    {
        $result = $this->service->store(auth('sanctum')->user(), $id);

        return ApiResponse::sendResponse(
            200,
            $result['message'],
            $result['product']
        );
    }

    public function destroy(int $id)
    {
        $result = $this->service->destroy(auth('sanctum')->user(), $id);

        return ApiResponse::sendResponse(
            200,
            $result['message'],
        );
    }
}

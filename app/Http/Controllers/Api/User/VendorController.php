<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\TopRatedVendorRequest;
use App\Http\Resources\Api\User\TopVendorResource;
use App\Services\Api\User\VendorService;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $service
    ) {}

    public function topRated(TopRatedVendorRequest $request)
    {
        $result = $this->service->topRated(
            $request->integer('per_page')
                ?: $request->integer('limit')
                ?: 10
        );

        return ApiResponse::sendResponse(
            200,
            __('vendor.top_rated_retrieved'),
            TopVendorResource::collection($result['vendors']),
            $result['pagination']
        );
    }
}

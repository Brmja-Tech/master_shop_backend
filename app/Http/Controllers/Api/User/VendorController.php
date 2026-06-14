<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\VendorListingRequest;
use App\Http\Requests\Api\User\TopRatedVendorRequest;
use App\Http\Resources\Api\User\VendorListResource;
use App\Http\Resources\Api\User\TopVendorResource;
use App\Services\Api\User\VendorService;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $service
    ) {}

    public function index(VendorListingRequest $request)
    {
        $result = $this->service->nearby(
            user: auth('sanctum')->user(),
            perPage: $request->integer('per_page')
                ?: $request->integer('limit')
                ?: 10,
            search: $request->input('search'),
            latitude: $request->input('latitude'),
            longitude: $request->input('longitude'),
            storeTypeId: $request->integer('store_type_id') ?: null,
            sortDirection: $request->input('sort_direction', 'asc')
        );

        return ApiResponse::sendResponse(
            200,
            __('vendor.list_retrieved'),
            VendorListResource::collection($result['vendors']),
            $result['pagination']
        );
    }

    public function byStoreType(int $id, VendorListingRequest $request)
    {
        $result = $this->service->nearby(
            user: auth('sanctum')->user(),
            perPage: $request->integer('per_page')
                ?: $request->integer('limit')
                ?: 10,
            search: $request->input('search'),
            latitude: $request->input('latitude'),
            longitude: $request->input('longitude'),
            storeTypeId: $id,
            sortDirection: $request->input('sort_direction', 'asc')
        );

        return ApiResponse::sendResponse(
            200,
            __('vendor.list_retrieved'),
            VendorListResource::collection($result['vendors']),
            $result['pagination']
        );
    }

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

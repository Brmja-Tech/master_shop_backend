<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\RateVendorRequest;
use App\Http\Resources\Api\User\VendorRateResource;
use App\Services\Api\User\VendorRateService;

class VendorRateController extends Controller
{
    public function __construct(
        protected VendorRateService $service
    ) {}

    /**
     * Submit a rating.
     */
    public function store(int $id, RateVendorRequest $request)
    {
        $result = $this->service->store(
            auth('sanctum')->user(),
            $id,
            $request->float('rate')
        );

        return ApiResponse::sendResponse(
            200,
            $result['message'],
            new VendorRateResource($result['rate'])
        );
    }

    /**
     * Update a rating.
     */
    public function update(int $id, RateVendorRequest $request)
    {
        $result = $this->service->update(
            auth('sanctum')->user(),
            $id,
            $request->float('rate')
        );

        return ApiResponse::sendResponse(
            200,
            $result['message'],
            new VendorRateResource($result['rate'])
        );
    }
}

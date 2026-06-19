<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorRequest;
use App\Http\Resources\Api\Admin\VendorResource;
use App\Services\Api\Admin\VendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $service
    ) {}

    public function index(Request $request)
    {
        $vendors = $this->service->index($request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('vendor.list_retrieved'),
            VendorResource::collection($vendors->items()),
            [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
            ]
        );
    }

    public function store(VendorRequest $request)
    {
        $vendor = $this->service->store($request->validated());

        return ApiResponse::sendResponse(
            201,
            __('vendor.created'),
            new VendorResource($vendor)
        );
    }

    public function show(int $id)
    {
        $vendor = $this->service->show($id);

        return ApiResponse::sendResponse(
            200,
            __('vendor.show'),
            new VendorResource($vendor)
        );
    }

    public function update(VendorRequest $request, int $id)
    {
        $vendor = $this->service->update($id, $request->validated());

        return ApiResponse::sendResponse(
            200,
            __('vendor.updated'),
            new VendorResource($vendor)
        );
    }
}

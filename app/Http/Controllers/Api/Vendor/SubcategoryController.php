<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\SubcategoryRequest;
use App\Services\Api\Vendor\SubcategoryService;

class SubcategoryController extends Controller
{
    public function __construct(
        protected SubcategoryService $service
    ) {}

    public function index()
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.list'),
            $this->service->index(auth('sanctum')->user())
        );
    }

    public function lookup()
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.list'),
            $this->service->lookup(auth('sanctum')->user())
        );
    }

    public function store(SubcategoryRequest $request)
    {
        return ApiResponse::sendResponse(
            201,
            __('subcategory.created'),
            $this->service->store(auth('sanctum')->user(), $request->validated())
        );
    }

    public function show(int $id)
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.show'),
            $this->service->show(auth('sanctum')->user(), $id)
        );
    }

    public function update(SubcategoryRequest $request, int $id)
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.updated'),
            $this->service->update(auth('sanctum')->user(), $id, $request->validated())
        );
    }

    public function destroy(int $id)
    {
        $this->service->destroy(auth('sanctum')->user(), $id);

        return ApiResponse::sendResponse(
            200,
            __('subcategory.deleted')
        );
    }
}

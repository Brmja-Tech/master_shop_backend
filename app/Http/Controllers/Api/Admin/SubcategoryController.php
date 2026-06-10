<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubcategoryRequest;
use App\Services\Api\Admin\SubcategoryService;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function __construct(
        protected SubcategoryService $service
    ) {}

    public function index(Request $request)
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.list'),
            $this->service->index($request->integer('store_type_id') ?: null)
        );
    }

    public function lookup(Request $request)
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.list'),
            $this->service->lookup($request->integer('store_type_id') ?: null)
        );
    }

    public function store(SubcategoryRequest $request)
    {
        return ApiResponse::sendResponse(
            201,
            __('subcategory.created'),
            $this->service->store($request->validated())
        );
    }

    public function show(int $id)
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.show'),
            $this->service->show($id)
        );
    }

    public function update(SubcategoryRequest $request, int $id)
    {
        return ApiResponse::sendResponse(
            200,
            __('subcategory.updated'),
            $this->service->update($id, $request->validated())
        );
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);

        return ApiResponse::sendResponse(
            200,
            __('subcategory.deleted')
        );
    }
}

<?php

namespace App\Http\Controllers\Dashboard\Settings;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeRequest;
use App\Services\Dashboard\StoreTypeService;
class StoreTypeController extends Controller
{
    public function __construct(
        protected StoreTypeService $service
    ) {}

    public function index()
    {
        return ApiResponse::sendResponse(
            200,
            __('store_type.list'),
            $this->service->index()
        );
    }

    public function store(StoreTypeRequest $request)
    {
        $storeType = $this->service->store(
            $request->validated()
        );

        return ApiResponse::sendResponse(
            201,
            __('store_type.created'),
            $storeType
        );
    }

    public function show(int $id)
    {
        return ApiResponse::sendResponse(
            200,
            __('store_type.show'),
            $this->service->show($id)
        );
    }

    public function update(StoreTypeRequest $request, int $id)
    {
        $storeType = $this->service->update(
            $id,
            $request->validated()
        );

        return ApiResponse::sendResponse(
            200,
            __('store_type.updated'),
            $storeType
        );
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);

        return ApiResponse::sendResponse(
            200,
            __('store_type.deleted')
        );
    }
    public function lookup()
{
    return ApiResponse::sendResponse(
        200,
        __('store_type.list'),
        $this->service->lookup()
    );
}
}

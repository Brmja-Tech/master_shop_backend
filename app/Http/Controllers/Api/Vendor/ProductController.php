<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ProductRequest;
use App\Services\Api\Vendor\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index()
    {
        return ApiResponse::sendResponse(
            200,
            __('product.list'),
            $this->service->index(auth('sanctum')->user())
        );
    }

    public function publicIndex()
    {
        return ApiResponse::sendResponse(
            200,
            __('product.public_list'),
            $this->service->publicIndex(request('search'))
        );
    }

    public function available()
    {
        return ApiResponse::sendResponse(
            200,
            __('product.available_list'),
            $this->service->available()
        );
    }

    public function store(ProductRequest $request)
    {
        $data = $this->payload($request);

        return ApiResponse::sendResponse(
            201,
            __('product.created'),
            $this->service->store(auth('sanctum')->user(), $data)
        );
    }

    public function show(int $id)
    {
        return ApiResponse::sendResponse(
            200,
            __('product.show'),
            $this->service->show(auth('sanctum')->user(), $id)
        );
    }

    public function update(ProductRequest $request, int $id)
    {
        $data = $this->payload($request);

        return ApiResponse::sendResponse(
            200,
            __('product.updated'),
            $this->service->update(auth('sanctum')->user(), $id, $data)
        );
    }

    public function destroy(int $id)
    {
        $this->service->destroy(auth('sanctum')->user(), $id);

        return ApiResponse::sendResponse(
            200,
            __('product.deleted')
        );
    }

    private function payload(ProductRequest $request): array
    {
        $request->validated();

        $data = [];

        foreach ([
            'subcategory_id',
            'quantity',
            'remaining_quantity',
            'discount',
            'is_available',
            'unit',
            'price',
            'expiry_date',
        ] as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($request->has('delete_image_ids') && is_array($request->input('delete_image_ids'))) {
            $data['delete_image_ids'] = $request->input('delete_image_ids');
        }

        foreach (['name', 'description'] as $field) {
            if ($request->has($field) && is_array($request->input($field))) {
                $data[$field] = $request->input($field);
            }
        }

        if ($request->has('subcategory_name') && is_array($request->input('subcategory_name'))) {
            $data['subcategory_name'] = $request->input('subcategory_name');
        }

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image');
        }

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        return $data;
    }
}

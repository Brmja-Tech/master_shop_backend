<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateOrderStatusRequest;
use App\Http\Resources\Api\Vendor\VendorOrderResource;
use App\Models\Order;
use App\Services\Api\Vendor\VendorOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected VendorOrderService $service
    ) {}

    public function index(Request $request)
    {
        $vendor = auth('sanctum')->user();

        $orders = $this->service->index(
            $vendor,
            $request->input('status'),
            $request->integer('per_page', 10)
        );

        return ApiResponse::sendResponse(
            200,
            __('vendor.orders_retrieved'),
            VendorOrderResource::collection($orders->items()),
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        );
    }

    public function show(Order $order)
    {
        $vendor = auth('sanctum')->user();

        abort_if($order->vendor_id !== $vendor->id, 403);

        $order = $this->service->show($vendor, $order->id);

        return ApiResponse::sendResponse(
            200,
            __('vendor.order_retrieved'),
            new VendorOrderResource($order)
        );
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $vendor = auth('sanctum')->user();

        abort_if($order->vendor_id !== $vendor->id, 403);

        $order = $this->service->updateStatus($vendor, $order, $request->validated());

        return ApiResponse::sendResponse(
            200,
            __('vendor.order_status_updated'),
            new VendorOrderResource($order)
        );
    }
}

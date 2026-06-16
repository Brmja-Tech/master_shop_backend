<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Api\Vendor\VendorOrderService;

class OrderController extends Controller
{
    public function __construct(
        protected VendorOrderService $service
    ) {}

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $vendor = auth('sanctum')->user();

        abort_if($order->vendor_id !== $vendor->id, 403);

        $order = $this->service->updateStatus($vendor, $order, $request->validated());

        return ApiResponse::sendResponse(
            200,
            __('vendor.order_status_updated'),
            OrderResource::make($order)
        );
    }
}

<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Delivery\AvailableDeliveryOrderResource;
use App\Models\Order;
use App\Services\DeliveryAutoAssignService;

class OrderController extends Controller
{
    public function __construct(
        protected DeliveryAutoAssignService $deliveryAutoAssignService
    ) {}

    public function index()
    {
        $deliveryUser = auth('sanctum')->user();
        $orders = $this->deliveryAutoAssignService->availableOrdersFor($deliveryUser);

        return ApiResponse::sendResponse(
            200,
            'Available orders retrieved successfully',
            AvailableDeliveryOrderResource::collection($orders)
        );
    }

    public function accept(Order $order)
    {
        $deliveryUser = auth('sanctum')->user();

        $accepted = $this->deliveryAutoAssignService->accept($order, $deliveryUser);

        if (! $accepted) {
            return ApiResponse::sendResponse(422, 'Order is no longer available for acceptance', []);
        }

        return ApiResponse::sendResponse(200, 'Order accepted successfully', [
            'order_id' => $order->id,
            'delivery_id' => $deliveryUser->id,
            'order_status' => 'on_the_way',
            'delivery_status' => 'assigned',
        ]);
    }

    public function reject(Order $order)
    {
        $deliveryUser = auth('sanctum')->user();

        $this->deliveryAutoAssignService->reject($order, $deliveryUser);

        return ApiResponse::sendResponse(200, 'Order rejected successfully', []);
    }
}

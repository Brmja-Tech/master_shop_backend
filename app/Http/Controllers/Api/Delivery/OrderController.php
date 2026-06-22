<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Enums\OrderStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Delivery\AvailableDeliveryOrderResource;
use App\Http\Resources\Api\Delivery\Auth\DeliveryUserResource;
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

    public function currentAccepted()
    {
        $deliveryUser = auth('sanctum')->user();
        $orders = $deliveryUser->orders()
            ->where(function ($query) {
                $query->whereIn('delivery_status', [
                    'assigned',
                    'picked_up',
                ])->orWhereIn('status', [
                    OrderStatus::Accepted->value,
                    OrderStatus::OnTheWay->value,
                ]);
            })
            ->with(['vendor', 'items.product.images'])
            ->withCount('items')
            ->latest()
            ->get();

        return ApiResponse::sendResponse(
            200,
            'Current accepted orders retrieved successfully',
            [
                'delivery_user' => (new DeliveryUserResource($deliveryUser))->resolve(),
                'orders' => AvailableDeliveryOrderResource::collection($orders)->resolve(),
            ]
        );
    }

    public function completed()
    {
        $deliveryUser = auth('sanctum')->user();
        $orders = $deliveryUser->orders()
            ->where('status', OrderStatus::Delivered->value)
            ->with('vendor')
            ->withCount('items')
            ->latest()
            ->get();

        return ApiResponse::sendResponse(
            200,
            'Completed orders retrieved successfully',
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

    public function complete(Order $order)
    {
        $deliveryUser = auth('sanctum')->user();

        $completed = $this->deliveryAutoAssignService->complete($order, $deliveryUser);

        if (! $completed) {
            return ApiResponse::sendResponse(422, 'Order cannot be completed', []);
        }

        return ApiResponse::sendResponse(200, 'Order completed successfully', []);
    }

    public function reject(Order $order)
    {
        $deliveryUser = auth('sanctum')->user();

        $this->deliveryAutoAssignService->reject($order, $deliveryUser);

        return ApiResponse::sendResponse(200, 'Order rejected successfully', []);
    }
}

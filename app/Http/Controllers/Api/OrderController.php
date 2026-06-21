<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CancelOrderRequest;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymobService;
use Illuminate\Http\Client\RequestException;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function store(PlaceOrderRequest $request)
    {
        try {
            $order = $this->orderService->placeOrder(
                auth('sanctum')->user(),
                $request->validated()
            );
        } catch (\Exception $e) {
            return ApiResponse::sendResponse(422, $e->getMessage());
        }

        $resource = OrderResource::make($order);

        if ($order->payment_method === PaymentMethod::Paymob) {
            $resource->additional([
                'meta' => [
                    'payment_url' => $order->payment_url,
                ],
            ]);
        }

        return ApiResponse::sendResponse(
            201,
            'Order placed successfully',
            $resource
        );
    }

    public function index()
    {
        $orders = Order::query()
            ->forUser(auth('sanctum')->id())
            ->latest()
            ->with([
                'vendor:id,store_name,logo',
            ])
            ->withCount('items')
            ->paginate(10);

        return ApiResponse::sendResponse(
            200,
            'Orders retrieved successfully',
            OrderResource::collection($orders->items()),
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
        abort_if($order->user_id != auth('sanctum')->id(), 403);

        $order->load([
            'vendor:id,store_name,logo',
            'items.product.images',
        ]);

        return ApiResponse::sendResponse(
            200,
            'Order retrieved successfully',
            OrderResource::make($order)
        );
    }

    public function cancel(CancelOrderRequest $request, Order $order, PaymobService $paymobService)
    {
        abort_if($order->user_id !== auth('sanctum')->id(), 403);

        if (! $order->status || ! $order->isCancellable()) {
            return ApiResponse::sendResponse(422, 'Order cannot be cancelled at this stage');
        }

        $updates = [
            'status' => OrderStatus::Cancelled,
            'cancelled_by' => 'user',
            'cancellation_reason' => $request->validated('cancellation_reason'),
        ];

        if ($order->payment_method === PaymentMethod::Paymob && $order->payment_status === PaymentStatus::Paid) {
            try {
                $paymobService->refundOrVoid($order);
            } catch (RequestException|RuntimeException $exception) {
                $message = $exception instanceof RequestException
                    ? (data_get($exception->response?->json(), 'message') ?? 'Unable to refund this Paymob transaction right now.')
                    : $exception->getMessage();

                return ApiResponse::sendResponse(422, $message);
            }

            $updates['payment_status'] = PaymentStatus::Refunded;
        }

        $order->update($updates);
        $order->load([
            'vendor:id,store_name,logo',
        ]);

        return ApiResponse::sendResponse(
            200,
            'Order cancelled successfully',
            OrderResource::make($order)
        );
    }
}

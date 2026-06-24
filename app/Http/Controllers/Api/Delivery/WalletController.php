<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Delivery\DeliveryOrderWithdrawStatusResource;
use App\Http\Resources\Api\Delivery\DeliveryWithdrawableOrderResource;
use App\Http\Resources\Api\Delivery\DeliveryWithdrawalRequestResource;
use App\Models\Admin;
use App\Notifications\Admin\NewDeliveryWithdrawalRequestNotification;
use App\Services\DeliveryWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function __construct(
        protected DeliveryWalletService $walletService
    ) {}

    public function withdrawableOrders(Request $request)
    {
        $delivery = auth('sanctum')->user();
        $orders = $this->walletService->getWithdrawableOrders($delivery, $request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('delivery.withdrawable_orders_retrieved'),
            DeliveryWithdrawableOrderResource::collection($orders->items()),
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        );
    }

    public function ordersWithWithdrawStatus(Request $request)
    {
        $delivery = auth('sanctum')->user();
        $request->validate([
            'withdraw_status' => ['nullable', 'in:available,pending,approved,rejected'],
        ]);

        $orders = $this->walletService->getOrdersWithWithdrawStatus(
            $delivery,
            $request->integer('per_page', 15),
            $request->input('withdraw_status')
        );
        $availableWithdrawableAmount = $this->walletService->getAvailableWithdrawableAmount($delivery);

        return ApiResponse::sendResponse(
            200,
            __('delivery.orders_with_withdraw_status_retrieved'),
            DeliveryOrderWithdrawStatusResource::collection($orders->items()),
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            [
                'available_withdrawable_amount' => round($availableWithdrawableAmount, 2),
            ]
        );
    }

    public function withdrawalRequests(Request $request)
    {
        $delivery = auth('sanctum')->user();
        $requests = $delivery->withdrawalRequests()
            ->with('orderAllocations.order')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('delivery.withdraw_requests_retrieved'),
            DeliveryWithdrawalRequestResource::collection($requests->items()),
            [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ]
        );
    }

    public function storeWithdrawalRequest(Request $request)
    {
        $delivery = auth('sanctum')->user();
        $data = $request->validate([
            'method' => ['required', 'in:instapay,vodafone_cash,bank_account'],
            'transfer_details' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $withdrawalRequest = $this->walletService->createWithdrawalRequest($delivery, $data);

        try {
            $admins = Admin::query()->get()->filter(function ($admin) {
                return $admin->hasAccess('withdraw_requests');
            });

            foreach ($admins as $admin) {
                $admin->notify(new NewDeliveryWithdrawalRequestNotification($withdrawalRequest));
            }
        } catch (\Throwable $e) {
            Log::error('فشل إرسال إشعار طلب سحب مندوب للمشرفين: ' . $e->getMessage());
        }

        return ApiResponse::sendResponse(
            201,
            __('delivery.withdraw_request_created'),
            new DeliveryWithdrawalRequestResource($withdrawalRequest)
        );
    }
}

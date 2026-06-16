<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Vendor\VendorWithdrawableOrderResource;
use App\Http\Resources\Api\Vendor\VendorWithdrawalRequestResource;
use App\Services\VendorWalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected VendorWalletService $walletService
    ) {}

    public function withdrawableOrders(Request $request)
    {
        $vendor = auth('sanctum')->user();
        $orders = $this->walletService->getWithdrawableOrders($vendor, $request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('vendor.withdrawable_orders_retrieved'),
            VendorWithdrawableOrderResource::collection($orders->items()),
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        );
    }

    public function withdrawalRequests(Request $request)
    {
        $vendor = auth('sanctum')->user();
        $requests = $vendor->withdrawalRequests()
            ->with('orderAllocations.order')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('vendor.withdraw_requests_retrieved'),
            VendorWithdrawalRequestResource::collection($requests->items()),
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
        $vendor = auth('sanctum')->user();
        $data = $request->validate([
            'method' => ['required', 'in:instapay,vodafone_cash,bank_account'],
            'transfer_details' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $withdrawalRequest = $this->walletService->createWithdrawalRequest($vendor, $data);

        return ApiResponse::sendResponse(
            201,
            __('vendor.withdraw_request_created'),
            new VendorWithdrawalRequestResource($withdrawalRequest)
        );
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Delivery\DeliveryWithdrawalRequestResource;
use App\Models\DeliveryWithdrawalRequest;
use App\Services\DeliveryWalletService;
use Illuminate\Http\Request;

class DeliveryWithdrawalRequestController extends Controller
{
    public function __construct(
        protected DeliveryWalletService $walletService
    ) {}

    public function index(Request $request)
    {
        $withdrawalRequests = DeliveryWithdrawalRequest::query()
            ->with(['delivery', 'orderAllocations.order'])
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('delivery.admin_withdraw_requests_retrieved'),
            DeliveryWithdrawalRequestResource::collection($withdrawalRequests->items()),
            [
                'current_page' => $withdrawalRequests->currentPage(),
                'last_page' => $withdrawalRequests->lastPage(),
                'per_page' => $withdrawalRequests->perPage(),
                'total' => $withdrawalRequests->total(),
            ]
        );
    }

    public function approve(Request $request, DeliveryWithdrawalRequest $deliveryWithdrawalRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $processed = $this->walletService->approveWithdrawalRequest(
            $deliveryWithdrawalRequest,
            auth('admin')->id(),
            $data['admin_note'] ?? null
        );

        return ApiResponse::sendResponse(
            200,
            __('delivery.withdraw_request_approved'),
            new DeliveryWithdrawalRequestResource($processed)
        );
    }

    public function reject(Request $request, DeliveryWithdrawalRequest $deliveryWithdrawalRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $processed = $this->walletService->rejectWithdrawalRequest(
            $deliveryWithdrawalRequest,
            auth('admin')->id(),
            $data['admin_note'] ?? null
        );

        return ApiResponse::sendResponse(
            200,
            __('delivery.withdraw_request_rejected'),
            new DeliveryWithdrawalRequestResource($processed)
        );
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Vendor\VendorWithdrawalRequestResource;
use App\Models\VendorWithdrawalRequest;
use App\Services\VendorWalletService;
use Illuminate\Http\Request;

class VendorWithdrawalRequestController extends Controller
{
    public function __construct(
        protected VendorWalletService $walletService
    ) {}

    public function index(Request $request)
    {
        $withdrawalRequests = VendorWithdrawalRequest::query()
            ->with(['vendor', 'orderAllocations.order'])
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::sendResponse(
            200,
            __('vendor.admin_withdraw_requests_retrieved'),
            VendorWithdrawalRequestResource::collection($withdrawalRequests->items()),
            [
                'current_page' => $withdrawalRequests->currentPage(),
                'last_page' => $withdrawalRequests->lastPage(),
                'per_page' => $withdrawalRequests->perPage(),
                'total' => $withdrawalRequests->total(),
            ]
        );
    }

    public function approve(Request $request, VendorWithdrawalRequest $vendorWithdrawalRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $processed = $this->walletService->approveWithdrawalRequest(
            $vendorWithdrawalRequest,
            auth('admin')->id(),
            $data['admin_note'] ?? null
        );

        return ApiResponse::sendResponse(
            200,
            __('vendor.withdraw_request_approved'),
            new VendorWithdrawalRequestResource($processed)
        );
    }

    public function reject(Request $request, VendorWithdrawalRequest $vendorWithdrawalRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $processed = $this->walletService->rejectWithdrawalRequest(
            $vendorWithdrawalRequest,
            auth('admin')->id(),
            $data['admin_note'] ?? null
        );

        return ApiResponse::sendResponse(
            200,
            __('vendor.withdraw_request_rejected'),
            new VendorWithdrawalRequestResource($processed)
        );
    }
}

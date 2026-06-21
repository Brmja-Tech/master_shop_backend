<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'type' => ['nullable', 'string', Rule::in(['dashboard', 'store', 'branch', 'admin'])],
            'target_id' => ['nullable'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'unread_only' => ['nullable', 'boolean'],
        ]);

        return response()->json([
            'status' => true,
            'data' => $this->notificationService->getNotifications($request->user('admin'), $filters),
        ]);
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $marked = $this->notificationService->markAsRead($request->user('admin'), $notificationId);

        if (! $marked) {
            return response()->json([
                'status' => false,
                'message' => __('validation.something-valid'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('validation.successfully'),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'type' => ['nullable', 'string', Rule::in(['dashboard', 'store', 'branch', 'admin'])],
            'target_id' => ['nullable'],
        ]);

        return response()->json([
            'status' => true,
            'message' => __('validation.successfully'),
            'updated_count' => $this->notificationService->markAllAsRead($request->user('admin'), $filters),
        ]);
    }
}

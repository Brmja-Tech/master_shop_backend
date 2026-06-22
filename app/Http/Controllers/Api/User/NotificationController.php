<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Api\User\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        $filters = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'unread_only' => ['nullable', 'boolean'],
        ]);

        return ApiResponse::sendResponse(
            200,
            'Notifications retrieved successfully',
            $this->notificationService->getNotifications($user, $filters)
        );
    }

    public function markAsRead(Request $request, string $notificationId)
    {
        $user = auth('sanctum')->user();
        $marked = $this->notificationService->markAsRead($user, $notificationId);

        if (! $marked) {
            return ApiResponse::sendResponse(404, __('validation.something-valid'));
        }

        return ApiResponse::sendResponse(200, __('validation.successfully'));
    }

    public function markAllAsRead(Request $request)
    {
        $user = auth('sanctum')->user();

        return ApiResponse::sendResponse(
            200,
            __('validation.successfully'),
            [
                'updated_count' => $this->notificationService->markAllAsRead($user),
            ]
        );
    }
}

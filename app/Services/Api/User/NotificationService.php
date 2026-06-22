<?php

namespace App\Services\Api\User;

use App\Models\User;
use App\Repositories\Api\User\NotificationRepository;

class NotificationService
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    public function getNotifications(User $user, array $filters = []): array
    {
        $notifications = $this->notificationRepository->getNotifications($user, $filters);

        return [
            'items' => $notifications->getCollection()->map(function ($notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'New notification',
                    'message' => $data['message'] ?? null,
                    'type' => $data['type'] ?? 'general',
                    'order_id' => $data['order_id'] ?? null,
                    'status' => $data['status'] ?? null,
                    'is_read' => $notification->read_at !== null,
                    'read_at' => $notification->read_at?->toISOString(),
                    'created_at' => $notification->created_at?->toISOString(),
                    'created_at_human' => $notification->created_at?->diffForHumans(),
                    'data' => $data,
                ];
            })->values(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
            'unread_count' => $this->notificationRepository->getUnreadCount($user),
        ];
    }

    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $this->notificationRepository->findForUser($user, $notificationId);

        if (! $notification) {
            return false;
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return true;
    }

    public function markAllAsRead(User $user): int
    {
        return $this->notificationRepository->markAllAsRead($user);
    }
}

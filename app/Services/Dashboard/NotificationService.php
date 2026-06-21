<?php

namespace App\Services\Dashboard;

use App\Models\Admin;
use App\Repositories\Dashboard\NotificationRepository;

class NotificationService
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    public function getNotifications(Admin $admin, array $filters = []): array
    {
        $notifications = $this->notificationRepository->getNotifications($admin, $filters);

        return [
            'items' => $notifications->getCollection()->map(function ($notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'New notification',
                    'message' => $data['message'] ?? null,
                    'type' => $data['target_type'] ?? 'dashboard',
                    'target_id' => $data['target_id'] ?? null,
                    'entity_type' => $data['entity_type'] ?? null,
                    'entity_id' => $data['entity_id'] ?? null,
                    'action_url' => $data['action_url'] ?? null,
                    'sent_via' => $data['sent_via'] ?? ['database'],
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
            'unread_count' => $this->notificationRepository->getUnreadCount($admin),
        ];
    }

    public function markAsRead(Admin $admin, string $notificationId): bool
    {
        $notification = $this->notificationRepository->findForAdmin($admin, $notificationId);

        if (! $notification) {
            return false;
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return true;
    }

    public function markAllAsRead(Admin $admin, array $filters = []): int
    {
        return $this->notificationRepository->markAllAsRead($admin, $filters);
    }
}

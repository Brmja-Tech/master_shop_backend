<?php

namespace App\Repositories\Api\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

class NotificationRepository
{
    public function getNotifications(User $user, array $filters = []): LengthAwarePaginator
    {
        return $user->notifications()
            ->when(! empty($filters['unread_only']), fn ($query) => $query->whereNull('read_at'))
            ->latest()
            ->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function findForUser(User $user, string $notificationId): ?DatabaseNotification
    {
        return $user->notifications()->find($notificationId);
    }

    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update([
            'read_at' => Carbon::now(),
        ]);
    }
}

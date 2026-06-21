<?php

namespace App\Repositories\Dashboard;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationRepository
{
    public function getNotifications(Admin $admin, array $filters = []): LengthAwarePaginator
    {
        $query = $admin->notifications()->latest();

        $this->applyFilters($query, $filters);

        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function getUnreadCount(Admin $admin): int
    {
        return $admin->unreadNotifications()->count();
    }

    public function findForAdmin(Admin $admin, string $notificationId): ?DatabaseNotification
    {
        return $admin->notifications()->find($notificationId);
    }

    public function markAllAsRead(Admin $admin, array $filters = []): int
    {
        $query = $admin->unreadNotifications();

        $this->applyFilters($query, $filters);

        return $query->update([
            'read_at' => Carbon::now(),
        ]);
    }

    protected function applyFilters(Builder|MorphMany $query, array $filters): void
    {
        if (! empty($filters['unread_only'])) {
            $query->whereNull('read_at');
        }

        if (! empty($filters['type'])) {
            $type = (string) $filters['type'];

            $query->where(function (Builder $builder) use ($type) {
                if ($type === 'dashboard') {
                    $builder
                        ->whereRaw($this->jsonValueExpression('target_type') . ' = ?', ['dashboard'])
                        ->orWhereRaw($this->jsonValueExpression('target_type') . ' IS NULL');

                    return;
                }

                $builder->whereRaw($this->jsonValueExpression('target_type') . ' = ?', [$type]);
            });
        }

        if (array_key_exists('target_id', $filters) && $filters['target_id'] !== null) {
            $query->whereRaw(
                $this->jsonValueExpression('target_id') . ' = ?',
                [(string) $filters['target_id']]
            );
        }
    }

    protected function jsonValueExpression(string $key): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "json_extract(data, '$.\"{$key}\"')",
            default => "JSON_UNQUOTE(JSON_EXTRACT(data, '$.\"{$key}\"'))",
        };
    }
}

<?php

namespace App\Livewire\Dashboard\Notifications;

use Livewire\Component;

class NavbarNotifications extends Component
{
    public function getNotificationsProperty()
    {
        if (!auth('admin')->check()) {
            return collect();
        }
        return auth('admin')->user()->notifications()->latest()->take(5)->get();
    }

    public function getUnreadCountProperty()
    {
        if (!auth('admin')->check()) {
            return 0;
        }
        return auth('admin')->user()->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        if (!auth('admin')->check()) {
            return;
        }
        $notification = auth('admin')->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        if (!auth('admin')->check()) {
            return;
        }
        auth('admin')->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.dashboard.notifications.navbar-notifications');
    }
}

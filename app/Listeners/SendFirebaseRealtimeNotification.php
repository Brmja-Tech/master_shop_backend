<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use App\Services\FirebaseRealtimeService;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;

class SendFirebaseRealtimeNotification
{
    protected FirebaseRealtimeService $firebaseService;

    public function __construct(FirebaseRealtimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function handle(NotificationSent $event): void
    {
        // We only want to push to Firebase when saving to the database channel
        // to avoid duplicate pushes if the notification has multiple channels.
        if ($event->channel !== 'database') {
            return;
        }

        $notifiable = $event->notifiable;
        $notification = $event->notification;

        // Check if the recipient is an Admin
        if ($notifiable instanceof Admin) {
            try {
                // Get notification data
                $data = [];
                if (method_exists($notification, 'toDatabase')) {
                    $data = $notification->toDatabase($notifiable);
                } elseif (method_exists($notification, 'toArray')) {
                    $data = $notification->toArray($notifiable);
                }

                $title = $data['title'] ?? 'إشعار جديد';
                $message = $data['message'] ?? '';

                $notifiableType = strtolower(class_basename($notifiable));
                $path = 'notifications/' . $notifiableType . '_' . $notifiable->id;

                $payload = [
                    'id' => $notification->id,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'created_at' => now()->toISOString(),
                ];

                \App\Jobs\FirebasePushJob::dispatch($path, $payload);
            } catch (\Throwable $e) {
                Log::error('Failed to push notification to Firebase Realtime Database: ' . $e->getMessage());
            }
        }
    }
}

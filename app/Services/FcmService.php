<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = config('services.firebase.credentials');
            if ($credentialsPath && file_exists($credentialsPath)) {
                $this->messaging = (new Factory)
                    ->withServiceAccount($credentialsPath)
                    ->createMessaging();
            }
        } catch (\Throwable $e) {
            Log::error('FCM Service Initialization Failed: ' . $e->getMessage());
        }
    }

    /**
     * Send a push notification to a device token.
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->messaging) {
            Log::warning('FCM Service not initialized. Cannot send notification.');
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            if (!empty($data)) {
                // Firebase custom data values must be strings
                $stringData = [];
                foreach ($data as $key => $value) {
                    $stringData[(string) $key] = (string) $value;
                }
                $message = $message->withData($stringData);
            }

            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to send FCM notification: ' . $e->getMessage());
            return false;
        }
    }
}

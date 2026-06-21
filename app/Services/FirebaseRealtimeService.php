<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class FirebaseRealtimeService
{
    protected $database;

    public function __construct()
    {
        try {
            $credentialsPath = config('services.firebase.credentials');
            $databaseUrl = config('services.firebase.database_url');

            if ($credentialsPath && file_exists($credentialsPath) && $databaseUrl) {
                $this->database = (new Factory)
                    ->withServiceAccount($credentialsPath)
                    ->withDatabaseUri($databaseUrl)
                    ->createDatabase();
            } else {
                Log::warning('Firebase Realtime Service: credentials file not found or database URL is empty.', [
                    'credentialsPath' => $credentialsPath,
                    'databaseUrl' => $databaseUrl,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Firebase Realtime Service Initialization Failed: ' . $e->getMessage());
        }
    }

    /**
     * Push data to a specific reference path in the Firebase Realtime Database.
     *
     * @param string $path
     * @param array $data
     * @return bool
     */
    public function pushNotification(string $path, array $data): bool
    {
        if (!$this->database) {
            Log::warning('Firebase Realtime Database not initialized. Cannot push notification.');
            return false;
        }

        try {
            $this->database->getReference($path)->push($data);
            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to push data to Firebase Realtime Database: ' . $e->getMessage());
            return false;
        }
    }
}

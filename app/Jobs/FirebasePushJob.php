<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FirebaseRealtimeService;

class FirebasePushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;
    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path, array $data)
    {
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseRealtimeService $firebaseService): void
    {
        $firebaseService->pushNotification($this->path, $this->data);
    }
}

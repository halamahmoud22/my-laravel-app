<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\ActivityLog;
class LogPaymentActivity implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $action,
        public string $description
    ) {}
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ActivityLog::create([
            'user_id' => $this->userId,
            'action' => $this->action,
            'description' => $this->description,
        ]);
    }
}

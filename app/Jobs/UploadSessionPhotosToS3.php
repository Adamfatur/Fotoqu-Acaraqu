<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UploadSessionPhotosToS3 implements ShouldQueue
{
    use Queueable;

    protected $sessionId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $session = \App\Models\PhotoSession::find($this->sessionId);
        if (!$session) {
            \Log::warning("UploadSessionPhotosToS3: Session {$this->sessionId} not found");
            return;
        }

        try {
            \Log::info("Starting background S3 upload for session {$session->session_code}");
            app(\App\Services\PhotoService::class)->uploadSessionPhotosToS3($session);
            \Log::info("Completed background S3 upload for session {$session->session_code}");
        } catch (\Exception $e) {
            \Log::error("Failed background S3 upload for session {$session->session_code}", [
                'error' => $e->getMessage()
            ]);
            // Re-throw to allow retry if configured
            throw $e;
        }
    }
}

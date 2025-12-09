<?php

namespace App\Jobs;

use App\Models\Frame;
use App\Models\ActivityLog;
use App\Mail\FrameReadyMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendFrameEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Frame $frame
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $photoSession = $this->frame->photoSession;
            
            // Generate presigned URL regardless of email (for admin access)
            if (!$this->frame->isPresignedUrlValid()) {
                $this->frame->generatePresignedUrl();
            }

            // Only send email if customer_email is provided
            if ($photoSession->customer_email) {
                // Send email
                Mail::to($photoSession->customer_email)
                    ->send(new FrameReadyMail($this->frame));

                // Update frame to mark email as sent
                $this->frame->update([
                    'email_sent_at' => now()
                ]);
                
                // Log activity
                ActivityLog::log(
                    'email_sent',
                    "Frame email sent to {$photoSession->customer_email}",
                    ['frame_id' => $this->frame->id],
                    $photoSession
                );
            } else {
                // Log that email wasn't sent (customer didn't provide email)
                \Log::info("Frame ready but no email sent - customer email not provided", [
                    'session_id' => $photoSession->id,
                    'frame_id' => $this->frame->id
                ]);
            }

            // Log activity
            ActivityLog::log(
                'email_sent',
                "Frame email sent to {$photoSession->customer_email}",
                [
                    'frame_id' => $this->frame->id,
                    'email' => $photoSession->customer_email,
                    'presigned_url_expires' => $this->frame->presigned_expires_at,
                ],
                $photoSession
            );
            
        } catch (\Exception $e) {
            // Log error
            logger()->error("Failed to send frame email for frame {$this->frame->id}: {$e->getMessage()}");
            
            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        ActivityLog::log(
            'email_failed',
            "Failed to send frame email: {$exception->getMessage()}",
            [
                'frame_id' => $this->frame->id,
                'error' => $exception->getMessage(),
            ],
            $this->frame->photoSession
        );
    }
}

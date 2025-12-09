<?php

namespace App\Jobs;

use App\Models\PhotoSession;
use App\Services\GifService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSessionGif implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public PhotoSession $photoSession)
    {
    }

    public function handle(GifService $gifService): void
    {
        $session = $this->photoSession;
        if (!$session)
            return;

        // Only skip if already completed
        $existing = $session->sessionGif()->first();
        if ($existing && $existing->status === 'completed')
            return;

        $gifService->generateSessionGif($session);
    }
}

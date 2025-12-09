<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PhotoboothEvent;
use Carbon\Carbon;

class CheckExpiredEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired events and mark them as completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $expiredEvents = PhotoboothEvent::where('status', 'active')
            ->whereNotNull('active_until')
            ->where('active_until', '<=', $now)
            ->get();

        if ($expiredEvents->isEmpty()) {
            $this->info('No expired events found.');
            return;
        }

        foreach ($expiredEvents as $event) {
            $this->info("Processing expired event: {$event->name} (ID: {$event->id})");

            // Mark event as completed
            $event->update(['status' => 'completed']);

            // Cancel any pending/approved sessions for this event
            $cancelledSessions = $event->photoSessions()
                ->where('session_status', 'approved')
                ->update(['session_status' => 'cancelled']);

            $this->info("Event marked as completed. {$cancelledSessions} sessions cancelled.");
        }

        $this->info('Expired events check completed.');
    }
}

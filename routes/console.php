<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler is defined here for Laravel 11
if (file_exists(__DIR__ . '/../app/Console/Commands/CheckExpiredEvents.php')) {
    \Illuminate\Support\Facades\Schedule::command('events:check-expired')->everyMinute();
}

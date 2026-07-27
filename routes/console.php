<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily cleanup of temporary WhatsApp files
Schedule::command('whatsapp:clean-temp-files')->daily();

// Waitlist scheduling
Schedule::command('waitlist:recalculate-priorities')->dailyAt('00:00');
Schedule::command('waitlist:expire-old')->dailyAt('01:00');

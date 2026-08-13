<?php


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\orderUpdateStatus;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('update:carrier-status')->everyThirtyMinutes();
Schedule::command('whatsapp:followup')->daily();
Schedule::command('queue:work --queue=bulk-create-order --timeout=300 --tries=1 --stop-when-empty')->everyMinute();
// Schedule::command('queue:work --timeout=300 --tries=1 --stop-when-empty')->everySecond();

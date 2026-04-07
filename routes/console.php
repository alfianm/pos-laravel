<?php

use App\Jobs\GenerateDailySalesSummaryJob;
use App\Jobs\RetryFailedMarketplaceSyncJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Basic scheduled report job (Daily at 00:01)
Schedule::job(new GenerateDailySalesSummaryJob)->dailyAt('00:01');

// Basic scheduled sync retry (Hourly)
Schedule::job(new RetryFailedMarketplaceSyncJob)->hourly();

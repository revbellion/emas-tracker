<?php

use App\Console\Commands\SyncGoldPrices;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SyncGoldPrices::class, ['antam'])->dailyAt('08:00');
Schedule::command(SyncGoldPrices::class, ['antam'])->dailyAt('12:00');
Schedule::command(SyncGoldPrices::class, ['pegadaian'])->dailyAt('16:00');

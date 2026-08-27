<?php

use App\Services\ConsignacionesPollService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    if (config('services.consignaciones.enabled')) {
        app(ConsignacionesPollService::class)->poll();
    }
})->everyTwoMinutes()->name('poll-consignaciones')->withoutOverlapping();

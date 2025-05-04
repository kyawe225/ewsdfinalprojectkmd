<?php

use App\Jobs\StudentInactive;
use App\Models\Allocation;
use App\Models\Arranging;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('send-inactive-email', function () {
    StudentInactive::dispatch();
});

Schedule::job(new StudentInactive())->dailyAt("00:00");
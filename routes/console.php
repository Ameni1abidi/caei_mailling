<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('campaigns:auto-retry')->everyFifteenMinutes();

// Traitement automatique de la file d'attente des emails (s'arrête quand la file est vide)
Schedule::command('queue:work database --queue=emails,default --stop-when-empty --tries=3 --timeout=60')->everyMinute();



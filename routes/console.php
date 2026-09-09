<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Relances automatiques des emails en échec (toutes les 15 minutes)
Schedule::command('campaigns:auto-retry')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

// Déclenchement des campagnes programmées (chaque minute)
Schedule::command('campaigns:dispatch-scheduled')
    ->everyMinute()
    ->withoutOverlapping();

// Traitement automatique de la file d'attente des emails
Schedule::command('queue:work database --queue=emails,default --stop-when-empty --tries=3 --timeout=55 --max-jobs=50 --memory=128')
    ->everyMinute()
    ->withoutOverlapping();

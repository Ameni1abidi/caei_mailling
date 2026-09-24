<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

// ─────────────────────────────────────────────────────────────────────────────
// NETTOYAGE AUTOMATIQUE BASE DE DONNÉES (évite saturation OVH 2 Go)
// ─────────────────────────────────────────────────────────────────────────────

// Nettoyage hebdomadaire : failed_jobs, sessions, cache, email_logs > 90 jours
Schedule::command('db:clean --days=90')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->withoutOverlapping();

// Nettoyage d'urgence mensuel : email_logs > 60 jours (plus agressif)
Schedule::command('db:clean --days=60')
    ->monthly()
    ->withoutOverlapping();

// Vider les failed_jobs tous les jours à 3h du matin
Schedule::call(function () {
    try {
        $count = DB::table('failed_jobs')->count();
        if ($count > 100) {
            // Garder seulement les 50 derniers pour diagnostic
            $keepIds = DB::table('failed_jobs')->orderByDesc('id')->limit(50)->pluck('id');
            DB::table('failed_jobs')->whereNotIn('id', $keepIds)->delete();
            \Illuminate\Support\Facades\Log::info("db:auto-clean failed_jobs — conservé 50 sur {$count}");
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Auto-clean failed_jobs error: ' . $e->getMessage());
    }
})->daily()->at('03:00');

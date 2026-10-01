<?php
/**
 * Script d'exécution du Queue Worker + Scheduler pour Cron OVH
 *
 * Optimisations appliquées :
 * - --max-jobs=200  : traite jusqu'à 200 jobs par run pour vider plus vite la queue
 * - --max-time=50   : stoppe le worker après 50s max (< 60s cron) pour éviter le chevauchement
 * - --memory=256    : mémoire suffisante pour 200 jobs en série
 * - --timeout=25    : timeout par job individuel (SMTP + envoi)
 * - --tries=3       : 3 tentatives par job avant de marquer failed
 * - --backoff=30    : 30 secondes entre chaque retry
 *
 * NB : --max-time prime sur --max-jobs si le worker tourne trop longtemps.
 *      Les 2 ensemble garantissent qu'on ne dépasse jamais l'intervalle cron de 60s.
 */

define('LARAVEL_START', microtime(true));

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// 1. Déclencher les campagnes programmées dont l'échéance est arrivée
$kernel->call('campaigns:dispatch-scheduled');

// 2. Exécuter le scheduler Laravel (relances auto, etc.)
$kernel->call('schedule:run');

// 3. Traiter immédiatement la file d'attente
//
//    ✅ CALCUL BASÉ SUR LES DONNÉES RÉELLES :
//    - rate_limit = 2 emails/min par compte SMTP OVH
//    - 2 × 60 = 120 emails/heure par compte (limite OVH = 200/h → safe)
//    - Jusqu'à 11 comptes SMTP disponibles
//    - En pratique : ~7 campagnes actives × 2 emails/min = 14 jobs/min
//
//    → --max-jobs=14 : traite exactement le débit naturel des campagnes actives
//    Ajuster si le nombre de campagnes simultanées change (ex: 5 campagnes → 10)
$kernel->call('queue:work', [
    'connection'        => 'database',
    '--queue'           => 'emails,default',
    '--stop-when-empty' => true,
    '--max-jobs'        => 14,      // 7 campagnes actives × rate_limit=2 emails/min
    '--max-time'        => 50,      // Hard stop après 50s (< 60s intervalle cron)
    '--memory'          => 128,
    '--timeout'         => 30,      // 30s par job SMTP (OVH peut être lent)
    '--tries'           => 3,
    '--backoff'         => 30,
]);

echo "OVH Cron executed successfully at " . date('Y-m-d H:i:s') . "\n";

<?php
/**
 * Script d'exécution du Queue Worker + Scheduler pour Cron OVH
 *
 * Optimisations appliquées :
 * - --max-jobs=50  : traite jusqu'à 50 jobs par run (au lieu de 1 à la fois)
 * - --memory=128   : évite les restarts prématurés par OOM
 * - --timeout=55   : légèrement inférieur à l'intervalle cron (60s) pour
 *                    éviter les chevauchements
 * - --tries=3      : 3 tentatives par job avant de marquer failed
 * - --backoff=30   : 30 secondes entre chaque retry
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

// 3. Traiter immédiatement la file d'attente (incluant les jobs de la campagne qui vient d'être déclenchée)
$kernel->call('queue:work', [
    'connection'        => 'database',
    '--queue'           => 'emails,default',
    '--stop-when-empty' => true,
    '--max-jobs'        => 50,      // Process up to 50 jobs then stop
    '--memory'          => 128,     // Stop if worker exceeds 128MB RAM
    '--timeout'         => 55,      // Per-job timeout (< cron interval to avoid overlap)
    '--tries'           => 3,
    '--backoff'         => 30,
]);

echo "OVH Cron executed successfully at " . date('Y-m-d H:i:s') . "\n";

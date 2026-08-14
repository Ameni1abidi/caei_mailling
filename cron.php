<?php
/**
 * Script d'exécution du Scheduler / Queue Worker pour Cron OVH
 */

define('LARAVEL_START', microtime(true));

// Se placer dans le dossier racine
chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// 1. Exécuter la commande queue:work (arrête la tâche une fois la queue vide)
$status = $kernel->call('queue:work', [
    '--queue' => 'emails,default',
    '--stop-when-empty' => true,
    '--tries' => 3,
    '--timeout' => 60,
]);

// 2. Exécuter le scheduler Laravel pour les tâches planifiées (relances auto, etc.)
$statusSchedule = $kernel->call('schedule:run');

echo "OVH Cron executed successfully at " . date('Y-m-d H:i:s') . "\n";

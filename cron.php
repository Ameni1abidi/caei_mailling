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

// 1. Force l'utilisation du driver database (car OVH mutualisé n'a pas Redis)
$status = $kernel->call('queue:work', [
    'connection' => 'database',
    '--queue' => 'emails,default',
    '--stop-when-empty' => true,
    '--tries' => 3,
    '--timeout' => 60,
]);

// 2. Exécuter le scheduler Laravel
$statusSchedule = $kernel->call('schedule:run');

echo "OVH Cron executed successfully at " . date('Y-m-d H:i:s') . "\n";

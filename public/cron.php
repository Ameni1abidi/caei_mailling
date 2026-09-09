<?php
// Protection par jeton secret
$token = $_GET['token'] ?? '';
if ($token !== 'caei-cron-secret-2026') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

@set_time_limit(120);
@ignore_user_abort(true);

define('LARAVEL_START', microtime(true));

// Se placer dans la racine Laravel
chdir(__DIR__ . '/..');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// 1. Vider la file d'attente des emails
$kernel->call('queue:work', [
    'connection' => 'database',
    '--queue' => 'emails,default',
    '--stop-when-empty' => true,
    '--tries' => 3,
    '--timeout' => 60,
]);

// 2. Exécuter le scheduler Laravel
$kernel->call('schedule:run');

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Queue worker exécuté avec succès',
    'date' => date('Y-m-d H:i:s'),
]);

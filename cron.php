<?php
/**
 * Script d'exécution du Scheduler + Batch Sender + Queue Worker pour Cron OVH
 *
 * Nouvelle architecture (batch-dispatch) :
 *
 *  1. campaigns:dispatch-scheduled  → crée les email_logs pour les campagnes programmées
 *  2. schedule:run                  → relances auto, nettoyage, etc.
 *  3. campaigns:send-batch          → cœur du système :
 *       Pour chaque campagne "en_cours" :
 *         - Prend rate_limit (=2) email_logs 'pending'
 *         - Les marque 'queued' (atomique → pas de doublon)
 *         - Dispatche les jobs SANS délai
 *       → Débit naturel : 2/min × 11 comptes = 1 320 emails/heure
 *       → Queue reste toujours petite (≤ 30 jobs)
 *  4. queue:work                    → traite les ~22 jobs dispatchés cette minute
 *
 * Avantages vs ancien système :
 *  ✅ Plus de 48 000 jobs en file avec des délais de 7 jours
 *  ✅ Plus de jobs orphelins si le serveur redémarre
 *  ✅ Toutes les campagnes progressent en parallèle sans se bloquer
 *  ✅ Rate limit OVH garanti par conception
 */

define('LARAVEL_START', microtime(true));

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// 1. Déclencher les campagnes programmées (crée les email_logs uniquement)
$kernel->call('campaigns:dispatch-scheduled');

// 2. Scheduler Laravel (relances auto, nettoyage, etc.)
$kernel->call('schedule:run');

// 3. Dispatcher le prochain batch pour chaque campagne active
//    → rate_limit emails par campagne, marqués 'queued' atomiquement
$kernel->call('campaigns:send-batch');

// 4. Traiter les jobs dispatchés à l'étape 3
//    --max-jobs=30  : largement suffisant (22 jobs/min max avec 11 comptes)
//    --max-time=45  : hard stop avant la prochaine minute de cron
//    --timeout=30   : timeout par job SMTP individuel (OVH peut être lent)
$kernel->call('queue:work', [
    'connection'        => 'database',
    '--queue'           => 'emails,default',
    '--stop-when-empty' => true,
    '--max-jobs'        => 30,   // 11 comptes × 2/min + marge pour les retries
    '--max-time'        => 45,   // Hard stop avant le prochain cron (60s)
    '--memory'          => 128,
    '--timeout'         => 30,   // Timeout SMTP par job
    '--tries'           => 3,
    '--backoff'         => 30,
]);

echo "OVH Cron executed successfully at " . date('Y-m-d H:i:s') . "\n";

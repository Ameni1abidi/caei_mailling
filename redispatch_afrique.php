<?php
/**
 * Script one-shot : re-dispatcher les jobs orphelins des campagnes "Afrique fin"
 *
 * Contexte : les email_logs sont en status='pending' mais les jobs correspondants
 * ont disparu de la table `jobs` (redémarrage serveur / vidage queue).
 * Ce script recrée les jobs manquants SANS modifier les email_logs existants.
 *
 * Usage : php redispatch_afrique.php
 *
 * ⚠️  À exécuter UNE SEULE FOIS après avoir déployé le fix SMTP (git pull).
 */

define('LARAVEL_START', microtime(true));
chdir(__DIR__);
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Contact;
use App\Jobs\SendCampaignEmailJob;

// IDs des campagnes Afrique fin bloquées
$campaignIds = [40, 41, 42, 43, 44];

$grandTotal = 0;

foreach ($campaignIds as $campaignId) {
    $campaign = Campaign::with(['creator.smtpSetting', 'smtpSetting'])->find($campaignId);
    if (! $campaign) {
        echo "⚠️  Campagne ID={$campaignId} introuvable, ignorée.\n";
        continue;
    }

    // Calculer le délai entre emails selon le rate_limit du compte SMTP
    $smtp               = $campaign->resolveSmtpSetting();
    $rateLimit          = max(1, (int) ($smtp?->rate_limit ?? 2)); // 2 emails/min par défaut OVH
    $delayBetweenEmails = (int) ceil(60 / $rateLimit);             // 30 secondes

    echo "\n📧 Campagne : {$campaign->nom} (ID:{$campaignId})\n";
    echo "   SMTP rate_limit={$rateLimit}/min → délai={$delayBetweenEmails}s entre chaque job\n";

    // Récupérer uniquement les email_logs 'pending' (pas envoyés, pas échoués définitivement)
    $pendingLogs = EmailLog::where('campaign_id', $campaignId)
        ->where('status', EmailLog::STATUS_PENDING)
        ->with('contact:id,email,nom,prenom,entreprise,fonction,pays,prospect_status,import_log_id')
        ->get();

    $totalPending  = $pendingLogs->count();
    $dispatched    = 0;
    $skipped       = 0;
    $jobIndex      = 0;

    foreach ($pendingLogs as $log) {
        // Ignorer si le contact est manquant ou l'email invalide
        if (! $log->contact || ! filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
            $skipped++;
            continue;
        }

        // Ignorer si le contact est désinscrit
        if ($log->contact->unsubscribed_at !== null) {
            // Marquer directement comme failed pour ne pas reprocesser
            $log->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => 'Contact désinscrit (détecté au re-dispatch)',
            ]);
            $skipped++;
            continue;
        }

        // Dispatcher avec délai échelonné pour respecter le rate_limit OVH
        SendCampaignEmailJob::dispatch($campaign, $log->contact, $log->id)
            ->delay(now()->addSeconds($jobIndex * $delayBetweenEmails))
            ->onQueue('emails')
            ->onConnection('database');

        $jobIndex++;
        $dispatched++;
    }

    $dureeEstimeeHeures = round(($jobIndex * $delayBetweenEmails) / 3600, 1);
    echo "   Total pending      : {$totalPending}\n";
    echo "   Jobs dispatched    : {$dispatched}\n";
    echo "   Ignorés (invalides): {$skipped}\n";
    echo "   Durée estimée      : ~{$dureeEstimeeHeures} heures\n";

    $grandTotal += $dispatched;
}

echo "\n✅ Terminé. Total jobs re-dispatchés : {$grandTotal}\n";
echo "   Les campagnes Afrique fin vont reprendre progressivement.\n";
echo "   Chaque campagne utilise son propre compte SMTP en parallèle.\n\n";

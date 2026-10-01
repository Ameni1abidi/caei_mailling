<?php

namespace App\Console\Commands;

use App\Jobs\SendCampaignEmailJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Commande exécutée chaque minute par le cron.
 *
 * Pour chaque campagne active, elle prend les N prochains email_logs
 * en status='pending' (N = rate_limit du compte SMTP), les marque
 * 'queued' de façon atomique, puis dispatche les jobs correspondants.
 *
 * ✅ Avantages vs l'ancienne approche (dispatch tout d'un coup avec délais) :
 *  - Queue toujours petite (≤ 30 jobs à la fois, jamais 48 000)
 *  - Aucun job orphelin : si le serveur redémarre, le prochain cron reprend
 *  - Rate limit OVH garanti par conception (2 emails/min par compte SMTP)
 *  - Toutes les campagnes progressent en parallèle sans se bloquer
 */
class SendCampaignBatchCommand extends Command
{
    protected $signature   = 'campaigns:send-batch {--dry-run : Afficher ce qui serait dispatché sans envoyer}';
    protected $description = 'Dispatcher le prochain batch d\'emails pour chaque campagne active (1 batch = rate_limit emails/min)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $totalDispatched = 0;

        Campaign::where('statut', 'en_cours')
            ->with(['creator.smtpSetting', 'smtpSetting'])
            ->each(function (Campaign $campaign) use ($dryRun, &$totalDispatched) {
                $dispatched = $this->processCampaign($campaign, $dryRun);
                $totalDispatched += $dispatched;
            });

        if ($this->getOutput()->isVerbose()) {
            $this->info("campaigns:send-batch — {$totalDispatched} job(s) dispatchés.");
        }

        return Command::SUCCESS;
    }

    private function processCampaign(Campaign $campaign, bool $dryRun): int
    {
        $smtp      = $campaign->resolveSmtpSetting();
        $batchSize = max(1, (int) ($smtp?->rate_limit ?? 2));

        // ── Étape 1 : Réinitialiser les 'queued' bloqués (timeout) ────────────────
        // Si un job a été dispatché mais n'a jamais tourné (ex : worker mort),
        // le log reste 'queued' indéfiniment. On le remet en 'pending' après 30 min.
        DB::table('email_logs')
            ->where('campaign_id', $campaign->id)
            ->where('status', EmailLog::STATUS_QUEUED)
            ->where('updated_at', '<', now()->subMinutes(30))
            ->update(['status' => EmailLog::STATUS_PENDING, 'updated_at' => now()]);

        // ── Étape 2 : Marquer atomiquement les N prochains pending → queued ───────
        // LIMIT + UPDATE est atomique en MySQL → pas de double-dispatch
        // même si deux processus tournent en parallèle (ce qui ne devrait pas
        // arriver grâce à --max-time=50 dans cron.php, mais on se protège quand même).
        $batchTime = now();
        $affected  = DB::table('email_logs')
            ->where('campaign_id', $campaign->id)
            ->where('status', EmailLog::STATUS_PENDING)
            ->limit($batchSize)
            ->update([
                'status'     => EmailLog::STATUS_QUEUED,
                'updated_at' => $batchTime,
            ]);

        if ($affected === 0) {
            // Plus de pending → vérifier si la campagne est terminée
            $campaign->markAsSentIfAllEmailsAreSent();
            return 0;
        }

        // ── Étape 3 : Charger les logs qu'on vient de marquer ────────────────────
        // On utilise updated_at pour identifier précisément ce batch.
        $logs = EmailLog::where('campaign_id', $campaign->id)
            ->where('status', EmailLog::STATUS_QUEUED)
            ->whereBetween('updated_at', [$batchTime, $batchTime->copy()->addSeconds(2)])
            ->with('contact:id,email,nom,prenom,entreprise,fonction,pays,prospect_status,import_log_id,unsubscribed_at')
            ->limit($batchSize * 2) // marge de sécurité
            ->get();

        $dispatched = 0;

        foreach ($logs as $log) {
            // ── Validation rapide avant dispatch ─────────────────────────────
            if (! $log->contact) {
                $log->update(['status' => EmailLog::STATUS_INVALID, 'error_message' => 'Contact introuvable']);
                continue;
            }

            if (! filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
                $log->update(['status' => EmailLog::STATUS_INVALID, 'error_message' => 'Adresse email invalide']);
                continue;
            }

            if ($log->contact->unsubscribed_at !== null) {
                $log->update(['status' => EmailLog::STATUS_FAILED, 'error_message' => 'Contact désinscrit']);
                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] Campagne #{$campaign->id} → {$log->contact->email}");
                $dispatched++;
                continue;
            }

            // ── Dispatch sans délai — le cron lui-même est le throttle ───────
            // Cadence naturelle : 2 jobs par minute → 120 emails/heure → ✅ OVH
            try {
                SendCampaignEmailJob::dispatch($campaign, $log->contact, $log->id)
                    ->onQueue('emails')
                    ->onConnection('database');

                $dispatched++;
            } catch (\Throwable $e) {
                Log::error("campaigns:send-batch — impossible de dispatcher log #{$log->id} : " . $e->getMessage());
                // Remettre en pending pour la prochaine minute
                $log->update(['status' => EmailLog::STATUS_PENDING]);
            }
        }

        return $dispatched;
    }
}

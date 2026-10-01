<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Déclenche les campagnes programmées dont la date_envoi est atteinte.
 *
 * RÔLE UNIQUE : créer les email_logs en status='pending'.
 * Le dispatch des jobs est entièrement géré par campaigns:send-batch
 * qui tourne chaque minute et envoie rate_limit emails par campagne.
 *
 * ✅ Plus de pre-scheduling avec délais → plus de queue à 48k jobs
 * ✅ Plus de jobs orphelins si le serveur redémarre
 */
class DispatchScheduledCampaignsCommand extends Command
{
    protected $signature   = 'campaigns:dispatch-scheduled';
    protected $description = 'Déclencher les campagnes programmées (crée les email_logs, le batch se charge de l\'envoi)';

    public function handle(): int
    {
        $campaigns = Campaign::where('statut', 'programmee')
            ->where('date_envoi', '<=', now())
            ->get();

        if ($campaigns->isEmpty()) {
            return Command::SUCCESS;
        }

        $this->info("Campagnes à déclencher : {$campaigns->count()}");

        foreach ($campaigns as $campaign) {
            try {
                $this->initializeCampaign($campaign);
                $this->info("✅ Campagne #{$campaign->id} ({$campaign->nom}) initialisée.");
            } catch (\Throwable $e) {
                Log::error("Erreur initialisation campagne #{$campaign->id}: " . $e->getMessage());
                $this->error("❌ Erreur campagne #{$campaign->id}: " . $e->getMessage());
                $campaign->update(['statut' => 'brouillon']);
            }
        }

        return Command::SUCCESS;
    }

    private function initializeCampaign(Campaign $campaign): void
    {
        if (! EmailTemplate::hasValidContent($campaign->contenu)) {
            $this->warn("Campagne #{$campaign->id}: contenu invalide, ignorée.");
            return;
        }

        // Verrou atomique — empêche le double-déclenchement si deux crons se chevauchent
        $locked = Campaign::lockForUpdate()->find($campaign->id);
        if (! $locked || $locked->statut !== 'programmee') {
            $this->warn("Campagne #{$campaign->id}: statut changé entre-temps, ignorée.");
            return;
        }

        $locked->update(['statut' => 'en_cours']);

        // ── Construire la requête contacts ────────────────────────────────────────
        $contactQuery = \App\Models\Contact::query()
            ->whereNull('unsubscribed_at')
            ->select(['id', 'email', 'nom', 'prenom', 'entreprise', 'fonction', 'pays', 'prospect_status', 'import_log_id']);

        if ($campaign->import_log_id) {
            $contactQuery->where('import_log_id', $campaign->import_log_id);
        } else {
            $categoryIds = $campaign->categoryIds();
            if ($categoryIds !== []) {
                $contactQuery->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));
            }
        }

        // ── Éviter les doublons (contacts déjà en pending/queued/sent) ────────────
        $existingContactIds = EmailLog::where('campaign_id', $campaign->id)
            ->whereIn('status', [
                EmailLog::STATUS_PENDING,
                EmailLog::STATUS_QUEUED,
                EmailLog::STATUS_SENT,
                EmailLog::STATUS_DELIVERED,
            ])
            ->pluck('contact_id')
            ->flip();

        $totalCreated = 0;
        $now          = now();

        // ── Créer les email_logs en batch (pas de jobs ici) ───────────────────────
        $contactQuery->chunkById(500, function ($contacts) use (
            $campaign,
            $existingContactIds,
            $now,
            &$totalCreated
        ) {
            $newContacts = $contacts->filter(fn ($c) => ! isset($existingContactIds[$c->id]));

            if ($newContacts->isEmpty()) {
                return;
            }

            $logsToInsert = [];
            foreach ($newContacts as $contact) {
                $logsToInsert[] = [
                    'campaign_id' => $campaign->id,
                    'contact_id'  => $contact->id,
                    'status'      => EmailLog::STATUS_PENDING,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            DB::table('email_logs')->insert($logsToInsert);
            $totalCreated += count($logsToInsert);
        });

        if ($totalCreated === 0) {
            $campaign->update(['statut' => 'brouillon']);
            $this->warn("Campagne #{$campaign->id}: aucun contact actif, remise en brouillon.");
        } else {
            $this->info("  → {$totalCreated} email_logs créés. campaigns:send-batch enverra à {$totalCreated} contacts.");
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\SendCampaignEmailJob;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\SmtpSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchScheduledCampaignsCommand extends Command
{
    protected $signature = 'campaigns:dispatch-scheduled';
    protected $description = 'Déclencher les campagnes programmées dont la date_envoi est atteinte';

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
                $this->dispatchCampaign($campaign);
                $this->info("✅ Campagne #{$campaign->id} ({$campaign->nom}) déclenchée.");
            } catch (\Throwable $e) {
                Log::error("Erreur déclenchement campagne programmée #{$campaign->id}: " . $e->getMessage());
                $this->error("❌ Erreur campagne #{$campaign->id}: " . $e->getMessage());
                // Remettre en brouillon pour éviter de rester bloquée en "programmee"
                $campaign->update(['statut' => 'brouillon']);
            }
        }

        return Command::SUCCESS;
    }

    private function dispatchCampaign(Campaign $campaign): void
    {
        if (! EmailTemplate::hasValidContent($campaign->contenu)) {
            $this->warn("Campagne #{$campaign->id}: contenu invalide, ignorée.");
            return;
        }

        // Atomic lock — prevent double-dispatch if two cron overlap
        $locked = Campaign::lockForUpdate()->find($campaign->id);
        if (! $locked || $locked->statut !== 'programmee') {
            $this->warn("Campagne #{$campaign->id}: statut changé entre-temps, ignorée.");
            return;
        }

        $locked->update(['statut' => 'en_cours']);

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

        // Pre-load existing logs for duplicate prevention
        $existingContactIds = EmailLog::where('campaign_id', $campaign->id)
            ->whereIn('status', [EmailLog::STATUS_PENDING, EmailLog::STATUS_SENT])
            ->pluck('contact_id')
            ->flip();

        $smtp               = SmtpSetting::where('is_active', true)->first();
        $rateLimit          = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);
        $jobIndex           = 0;
        $totalDispatched    = 0;

        $contactQuery->chunkById(500, function ($contacts) use (
            $campaign,
            $existingContactIds,
            $delayBetweenEmails,
            &$jobIndex,
            &$totalDispatched
        ) {
            $now = now();
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

            $insertedLogs = EmailLog::where('campaign_id', $campaign->id)
                ->where('status', EmailLog::STATUS_PENDING)
                ->whereIn('contact_id', $newContacts->pluck('id'))
                ->get(['id', 'contact_id'])
                ->keyBy('contact_id');

            foreach ($newContacts as $contact) {
                $log = $insertedLogs->get($contact->id);
                if (! $log) {
                    continue;
                }

                SendCampaignEmailJob::dispatch($campaign, $contact, $log->id)
                    ->delay(now()->addSeconds($jobIndex * $delayBetweenEmails))
                    ->onQueue('emails')
                    ->onConnection('database');

                $jobIndex++;
                $totalDispatched++;
            }
        });

        if ($totalDispatched === 0) {
            $campaign->update(['statut' => 'brouillon']);
            $this->warn("Campagne #{$campaign->id}: aucun contact actif, remise en brouillon.");
        } else {
            $this->info("  → {$totalDispatched} jobs dispatchés.");
        }
    }
}

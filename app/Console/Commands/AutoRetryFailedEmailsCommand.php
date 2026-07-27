<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use App\Jobs\SendCampaignEmailJob;

class AutoRetryFailedEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:auto-retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Relancer automatiquement les emails en échec pour les campagnes avec la relance automatique activée';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Vérification des emails en échec à relancer automatiquement...');

        $smtp = SmtpSetting::where('is_active', true)->first();
        $rateLimit = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);

        // Récupérer les campagnes avec auto_retry activé
        $activeCampaigns = Campaign::where('auto_retry', true)->get();

        $totalRelances = 0;

        foreach ($activeCampaigns as $campaign) {
            $maxRetries = max(1, (int) ($campaign->max_auto_retries ?? 3));

            $failedLogs = EmailLog::where('campaign_id', $campaign->id)
                ->where('status', EmailLog::STATUS_FAILED)
                ->where('retry_count', '<', $maxRetries)
                ->with('contact')
                ->get();

            if ($failedLogs->isEmpty()) {
                continue;
            }

            $relancesCampagne = 0;
            foreach ($failedLogs as $log) {
                if (!$log->contact || !filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $log->increment('retry_count');
                $log->update([
                    'status' => EmailLog::STATUS_PENDING,
                    'error_message' => null,
                ]);

                SendCampaignEmailJob::dispatch($campaign, $log->contact, $log->id)
                    ->delay(now()->addSeconds($relancesCampagne * $delayBetweenEmails))
                    ->onQueue('emails');

                $relancesCampagne++;
                $totalRelances++;
            }

            if ($relancesCampagne > 0) {
                $campaign->update(['statut' => 'en_cours']);
                $this->info("Campagne #{$campaign->id} ({$campaign->nom}) : {$relancesCampagne} email(s) remis en file d'attente.");
            }
        }

        $this->info("Relance automatique terminée : {$totalRelances} email(s) relancé(s).");

        return Command::SUCCESS;
    }
}

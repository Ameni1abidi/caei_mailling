<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use App\Jobs\SendCampaignEmailJob;
use Illuminate\Support\Facades\DB;

class AutoRetryFailedEmailsCommand extends Command
{
    protected $signature = 'campaigns:auto-retry';

    protected $description = 'Relancer automatiquement les emails en échec pour les campagnes avec la relance automatique activée';

    public function handle(): int
    {
        $this->info('Vérification des emails en échec à relancer automatiquement...');

        $totalRelances = 0;

        // Process each active en_cours auto-retry campaign
        Campaign::where('auto_retry', true)
            ->where('statut', 'en_cours')
            ->with(['creator.smtpSetting', 'smtpSetting'])
            ->select(['id', 'nom', 'max_auto_retries', 'created_by', 'smtp_setting_id'])
            ->each(function (Campaign $campaign) use (&$totalRelances) {
                $smtp               = $campaign->resolveSmtpSetting();
                $rateLimit          = max(1, (int) ($smtp?->rate_limit ?? 3));
                $delayBetweenEmails = (int) ceil(60 / $rateLimit);
                $maxRetries         = max(1, (int) ($campaign->max_auto_retries ?? 3));

                // Collect IDs of failed logs eligible for retry (exclure les erreurs d'identifiants 535)
                $failedLogIds = EmailLog::where('campaign_id', $campaign->id)
                    ->where('status', EmailLog::STATUS_FAILED)
                    ->where('retry_count', '<', $maxRetries)
                    ->where(function ($q) {
                        $q->whereNull('error_message')
                          ->orWhere(function ($sub) {
                              $sub->where('error_message', 'not like', '%535%')
                                  ->where('error_message', 'not like', '%Authentication failed%')
                                  ->where('error_message', 'not like', '%Failed to authenticate%');
                          });
                    })
                    ->whereHas('contact', fn ($q) => $q->whereNotNull('email'))
                    ->pluck('id');

                if ($failedLogIds->isEmpty()) {
                    return;
                }

                // Bulk increment retry_count and reset status in one query
                DB::table('email_logs')
                    ->whereIn('id', $failedLogIds)
                    ->update([
                        'retry_count'   => DB::raw('retry_count + 1'),
                        'status'        => EmailLog::STATUS_PENDING,
                        'error_message' => null,
                        'updated_at'    => now(),
                    ]);

                // Reload logs with contact data for dispatch
                $logsToDispatch = EmailLog::whereIn('id', $failedLogIds)
                    ->with('contact:id,email,nom,prenom,entreprise,fonction,pays,prospect_status,import_log_id')
                    ->get();

                $relancesCampagne = 0;

                foreach ($logsToDispatch as $log) {
                    if (! $log->contact || ! filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }

                    SendCampaignEmailJob::dispatch($campaign, $log->contact, $log->id)
                        ->delay(now()->addSeconds($relancesCampagne * $delayBetweenEmails))
                        ->onQueue('emails');

                    $relancesCampagne++;
                    $totalRelances++;
                }

                if ($relancesCampagne > 0) {
                    $this->info("Campagne #{$campaign->id} ({$campaign->nom}) : {$relancesCampagne} email(s) remis en file d'attente.");
                }
            });

        $this->info("Relance automatique terminée : {$totalRelances} email(s) relancé(s).");

        return Command::SUCCESS;
    }
}

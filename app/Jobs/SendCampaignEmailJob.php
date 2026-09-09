<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use App\Mail\CampaignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;
    public bool $deleteWhenMissingModels = true;

    /**
     * SMTP config cached for the lifetime of the worker process.
     * Avoids a DB query on every single job execution.
     */
    private static ?array $cachedSmtpConfig = null;
    private static bool $smtpCacheLoaded = false;

    public function __construct(
        public Campaign $campaign,
        public Contact $contact,
        public int $emailLogId
    ) {}

    public function handle(): void
    {
        $emailLog = EmailLog::find($this->emailLogId);
        if (! $emailLog) {
            return;
        }

        // Single DB fetch for campaign — reuse for all checks below
        $campaign = Campaign::find($this->campaign->id);
        if (! $campaign || $campaign->statut === 'annulee') {
            $emailLog->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => 'Campagne annulée par l\'utilisateur',
            ]);
            return;
        }

        if (! filter_var($this->contact->email, FILTER_VALIDATE_EMAIL)) {
            $emailLog->update([
                'status'        => EmailLog::STATUS_INVALID,
                'error_message' => 'Adresse email invalide',
            ]);
            $campaign->markAsSentIfAllEmailsAreSent();
            return;
        }

        // Re-check unsubscribe status using the already-serialized contact
        // (avoid extra Contact::find() — use fresh() only when needed)
        if ($this->contact->unsubscribed_at !== null) {
            $emailLog->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => 'Contact désinscrit',
            ]);
            $campaign->markAsSentIfAllEmailsAreSent();
            return;
        }

        try {
            $mailable = new CampaignMail($campaign, $this->contact, $this->emailLogId);

            $smtpConfig = $this->resolveSmtpConfig();

            if ($smtpConfig !== null) {
                $mailerName = 'dynamic_smtp';
                Config::set("mail.mailers.{$mailerName}", $smtpConfig['mailer']);

                if ($smtpConfig['sender_email']) {
                    $mailable->from($smtpConfig['sender_email'], $smtpConfig['sender_name']);
                }
                if ($smtpConfig['reply_to']) {
                    $mailable->replyTo($smtpConfig['reply_to']);
                }

                Mail::mailer($mailerName)->to($this->contact->email)->send($mailable);
            } else {
                Mail::to($this->contact->email)->send($mailable);
            }

            // Single bulk-friendly update
            EmailLog::where('id', $this->emailLogId)->update([
                'status'  => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

            // Advance prospect status (uses a conditional update internally)
            $this->contact->advanceStatusTo(Contact::STATUS_EMAIL_ENVOYE);

            $campaign->markAsSentIfAllEmailsAreSent();

        } catch (\Throwable $e) {
            $status = $this->determineFailureStatus($e);

            EmailLog::where('id', $this->emailLogId)->update([
                'status'        => $status,
                'error_message' => substr($e->getMessage(), 0, 500),
            ]);

            Log::error("Échec envoi campagne #{$this->campaign->id} à {$this->contact->email} : " . $e->getMessage());

            $campaign->markAsSentIfAllEmailsAreSent();
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        EmailLog::where('id', $this->emailLogId)
            ->where('status', EmailLog::STATUS_PENDING)
            ->update(['status' => EmailLog::STATUS_FAILED]);
    }

    /**
     * Resolve SMTP config with process-level cache.
     * The config is fetched once per worker lifecycle, not once per job.
     * Cache is busted on worker restart (which is fine — settings rarely change).
     */
    private function resolveSmtpConfig(): ?array
    {
        if (! self::$smtpCacheLoaded) {
            $smtp = SmtpSetting::where('is_active', true)->first();
            self::$smtpCacheLoaded = true;

            if ($smtp) {
                self::$cachedSmtpConfig = [
                    'mailer' => [
                        'transport'  => $smtp->driver ?? 'smtp',
                        'host'       => $smtp->host,
                        'port'       => $smtp->port,
                        'username'   => $smtp->username,
                        'password'   => $smtp->password,
                        'encryption' => $smtp->encryption ?? null,
                        'timeout'    => 30,
                    ],
                    'sender_email' => $smtp->sender_email ? trim($smtp->sender_email) : config('mail.from.address', 'Contact@caei-afri.com'),
                    'sender_name'  => $smtp->sender_name ? trim($smtp->sender_name) : config('mail.from.name', 'CAEI'),
                    'reply_to'     => $smtp->reply_to_email ? trim($smtp->reply_to_email) : ($smtp->sender_email ?: config('mail.from.address', 'Contact@caei-afri.com')),
                ];
            } else {
                self::$cachedSmtpConfig = null;
            }
        }

        return self::$cachedSmtpConfig;
    }

    private function determineFailureStatus(\Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'invalid')
            || str_contains($message, 'recipient address rejected')
            || str_contains($message, 'invalid address')
            || str_contains($message, 'user unknown')
            || str_contains($message, 'mailbox unavailable')
            || str_contains($message, 'address rejected')
            || str_contains($message, 'format error')) {
            return EmailLog::STATUS_INVALID;
        }

        if (str_contains($message, 'bounce')
            || str_contains($message, '550')
            || str_contains($message, '5.1')
            || str_contains($message, '5.7')
            || str_contains($message, 'undeliverable')
            || str_contains($message, 'mailbox full')
            || str_contains($message, 'recipient not found')) {
            return EmailLog::STATUS_BOUNCED;
        }

        return EmailLog::STATUS_FAILED;
    }
}

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
     * SMTP config cached per setting ID for the lifetime of the worker process.
     * Avoids a DB query on every single job execution while supporting multi-SMTP.
     */
    private static array $cachedSmtpConfigs = [];

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

        // Single DB fetch for campaign with relationships — reuse for all checks below
        $campaign = Campaign::with(['creator.smtpSetting', 'smtpSetting'])->find($this->campaign->id);
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

            $smtpConfig = $this->resolveSmtpConfig($campaign);

            if ($smtpConfig !== null) {
                $mailerName = 'dynamic_smtp_' . ($campaign->resolveSmtpSetting()?->id ?? 'default');
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
            $errMsg = trim($e->getMessage());
            if (empty($errMsg)) {
                $errMsg = 'Erreur SMTP : Délai d\'attente réseau dépassé ou rejet du serveur de messagerie';
            }

            EmailLog::where('id', $this->emailLogId)->update([
                'status'        => $status,
                'error_message' => substr($errMsg, 0, 500),
            ]);

            Log::error("Échec envoi campagne #{$this->campaign->id} à {$this->contact->email} : " . $errMsg);

            $campaign->markAsSentIfAllEmailsAreSent();
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $errMsg = trim($exception->getMessage());
        if (empty($errMsg)) {
            $errMsg = 'Échec du Queue Worker : Tentatives épuisées ou rejet de connexion SMTP';
        }

        EmailLog::where('id', $this->emailLogId)
            ->whereIn('status', [EmailLog::STATUS_PENDING, EmailLog::STATUS_FAILED])
            ->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => substr($errMsg, 0, 500),
            ]);
    }

    /**
     * Resolve SMTP config with process-level cache per SMTP account.
     */
    private function resolveSmtpConfig(?Campaign $campaign): ?array
    {
        $smtp = $campaign?->resolveSmtpSetting();
        $cacheKey = $smtp ? (int) $smtp->id : 0;

        if (! array_key_exists($cacheKey, self::$cachedSmtpConfigs)) {
            if ($smtp) {
                self::$cachedSmtpConfigs[$cacheKey] = [
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
                self::$cachedSmtpConfigs[$cacheKey] = null;
            }
        }

        return self::$cachedSmtpConfigs[$cacheKey];
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

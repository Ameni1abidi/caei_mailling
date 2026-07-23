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

        if (! filter_var($this->contact->email, FILTER_VALIDATE_EMAIL)) {
            $emailLog->update([
                'status' => EmailLog::STATUS_INVALID,
                'error_message' => 'Adresse email invalide',
            ]);
            Campaign::find($this->campaign->id)?->markAsSentIfAllEmailsAreSent();
            return;
        }

        try {
            $smtp = SmtpSetting::where('is_active', true)->first();

            $mailable = new CampaignMail($this->campaign, $this->contact, $this->emailLogId);

            if ($smtp) {
                $mailerName = 'dynamic_smtp';
                Config::set("mail.mailers.{$mailerName}", [
                    'transport'  => $smtp->driver ?? 'smtp',
                    'host'       => $smtp->host,
                    'port'       => $smtp->port,
                    'username'   => $smtp->username,
                    'password'   => $smtp->password,
                    'encryption' => $smtp->encryption,
                ]);

                if ($smtp->sender_email) {
                    $mailable->from($smtp->sender_email, $smtp->sender_name);
                }
                if ($smtp->reply_to_email) {
                    $mailable->replyTo($smtp->reply_to_email);
                }

                Mail::mailer($mailerName)->to($this->contact->email)->send($mailable);
            } else {
                Mail::to($this->contact->email)->send($mailable);
            }

            $emailLog->update([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

            // Mettre à jour le statut du prospect à "Email envoyé" si statut initial
            $this->contact->advanceStatusTo(Contact::STATUS_EMAIL_ENVOYE);

            Campaign::find($this->campaign->id)?->markAsSentIfAllEmailsAreSent();
            sleep(1);
        } catch (\Throwable $e) {
            $status = $this->determineFailureStatus($e);
            $emailLog->update([
                'status' => $status,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Échec envoi campagne #{$this->campaign->id} à {$this->contact->email} : " . $e->getMessage());
            sleep(2);
            Campaign::find($this->campaign->id)?->markAsSentIfAllEmailsAreSent();
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        EmailLog::where('id', $this->emailLogId)
            ->where('status', EmailLog::STATUS_PENDING)
            ->update(['status' => EmailLog::STATUS_FAILED]);
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

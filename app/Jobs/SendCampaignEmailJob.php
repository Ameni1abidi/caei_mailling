<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
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

    /**
     * Backoff escalatoire : 10min → 20min → 30min
     * Si OVH bloque pour quota (200/h), réessayer après 30s heurte le même mur.
     * Avec 10 minutes, la fenêtre glissante d'1h s'est écoulée → quota libéré.
     */
    public array $backoff = [600, 1200, 1800];
    public bool $deleteWhenMissingModels = true;

    // NOTE: Pas de cache statique SMTP. Réutiliser le même transport Symfony Mailer
    // (et sa socket TCP) entre plusieurs jobs provoque l'erreur "354 vs 250" quand
    // OVH ferme la connexion. Chaque job crée un transport frais via forgetMailers().

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

        // ── Garde contre double-envoi ────────────────────────────────────────────
        // Si deux jobs tournent pour le même email_log (ex : ancien job + nouveau
        // batch), le deuxième voit le statut déjà 'sent' et s'arrête proprement.
        if (in_array($emailLog->status, [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])) {
            return;
        }

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

        if ($this->contact->unsubscribed_at !== null) {
            $emailLog->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => 'Contact désinscrit',
            ]);
            $campaign->markAsSentIfAllEmailsAreSent();
            return;
        }

        try {
            $mailable   = new CampaignMail($campaign, $this->contact, $this->emailLogId);
            $smtpConfig = $this->resolveSmtpConfig($campaign);

            if ($smtpConfig !== null) {
                // Nom unique par job pour forcer une nouvelle connexion TCP à chaque envoi.
                // Évite l'erreur "Expected 354 but got 250" causée par une socket OVH morte.
                $mailerName = 'dynamic_smtp_' . ($campaign->resolveSmtpSetting()?->id ?? 'default') . '_' . $this->emailLogId;
                Config::set("mail.mailers.{$mailerName}", $smtpConfig['mailer']);

                if ($smtpConfig['sender_email']) {
                    $mailable->from($smtpConfig['sender_email'], $smtpConfig['sender_name']);
                }
                if ($smtpConfig['reply_to']) {
                    $mailable->replyTo($smtpConfig['reply_to']);
                }

                Mail::mailer($mailerName)->to($this->contact->email)->send($mailable);
                Mail::forgetMailers(); // Ferme la socket TCP immédiatement
            } else {
                Mail::to($this->contact->email)->send($mailable);
                Mail::forgetMailers();
            }

            EmailLog::where('id', $this->emailLogId)->update([
                'status'  => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

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

            if ($status === EmailLog::STATUS_INVALID || $status === EmailLog::STATUS_BOUNCED) {
                Contact::where('id', $this->contact->id)
                    ->whereNull('unsubscribed_at')
                    ->update(['unsubscribed_at' => now()]);

                $this->delete();
                return;
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $errMsg = trim($exception->getMessage());
        if (empty($errMsg)) {
            $errMsg = 'Échec du Queue Worker : Tentatives épuisées ou rejet de connexion SMTP';
        }

        // Inclure 'queued' dans les statuts éligibles au passage en 'failed'
        // (un log 'queued' dont le job a échoué 3 fois doit être marqué 'failed').
        EmailLog::where('id', $this->emailLogId)
            ->whereIn('status', [
                EmailLog::STATUS_PENDING,
                EmailLog::STATUS_QUEUED,
                EmailLog::STATUS_FAILED,
            ])
            ->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => substr($errMsg, 0, 500),
            ]);

        $campaign = Campaign::find($this->campaign->id);
        $campaign?->markAsSentIfAllEmailsAreSent();
    }

    /**
     * Résoudre la config SMTP fraîchement à chaque appel (pas de cache statique).
     * Les relations sont déjà eager-loaded → pas de requête DB supplémentaire.
     */
    private function resolveSmtpConfig(?Campaign $campaign): ?array
    {
        $smtp = $campaign?->resolveSmtpSetting();

        if (! $smtp) {
            return null;
        }

        return [
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
            'sender_name'  => $smtp->sender_name  ? trim($smtp->sender_name)  : config('mail.from.name', 'CAEI'),
            'reply_to'     => $smtp->reply_to_email ? trim($smtp->reply_to_email) : ($smtp->sender_email ?: config('mail.from.address', 'Contact@caei-afri.com')),
        ];
    }

    private function determineFailureStatus(\Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'quota exceeded')
            || str_contains($message, 'rate limit')
            || str_contains($message, 'too many')
            || str_contains($message, 'messages per hour')
            || str_contains($message, 'try again later')
            || str_contains($message, 'temporarily')
            || str_contains($message, 'try later')
            || str_contains($message, 'please retry')
            || str_contains($message, 'slow down')
            || str_contains($message, '4.7.')
            || str_contains($message, '452')
            || str_contains($message, 'connection timed out')
            || str_contains($message, 'timed out')) {
            return EmailLog::STATUS_FAILED;
        }

        if (str_contains($message, 'bounce')
            || str_contains($message, '5.1.0')
            || str_contains($message, '5.1.1')
            || str_contains($message, '5.1.2')
            || str_contains($message, '5.1.3')
            || str_contains($message, '5.1.6')
            || str_contains($message, '5.1.10')
            || str_contains($message, 'undeliverable')
            || str_contains($message, 'recipient not found')
            || str_contains($message, 'recipientnotfound')
            || str_contains($message, 'resolver.adr')
            || str_contains($message, 'no route to host')
            || str_contains($message, 'host or domain name not found')
            || str_contains($message, 'name or service not known')) {
            return EmailLog::STATUS_BOUNCED;
        }

        if (str_contains($message, 'recipient address rejected')
            || str_contains($message, 'invalid address')
            || str_contains($message, 'user unknown')
            || str_contains($message, 'mailbox unavailable')
            || str_contains($message, 'mailbox not found')
            || str_contains($message, 'address rejected')
            || str_contains($message, 'no such user')
            || str_contains($message, 'does not exist')
            || str_contains($message, 'user doesn\'t exist')
            || str_contains($message, 'account does not exist')
            || str_contains($message, 'bad destination')
            || str_contains($message, 'format error')
            || str_contains($message, '5.5.4')
            || str_contains($message, '550 5.4')) {
            return EmailLog::STATUS_INVALID;
        }

        if (str_contains($message, 'spam')
            || str_contains($message, 'blocked')
            || str_contains($message, 'blacklist')
            || str_contains($message, 'policy violation')
            || str_contains($message, '5.7.')) {
            return EmailLog::STATUS_FAILED;
        }

        return EmailLog::STATUS_FAILED;
    }
}

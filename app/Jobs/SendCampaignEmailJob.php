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

    /**
     * Backoff escalatoire : 10min → 20min → 30min
     * Raison : si OVH bloque pour quota (200/h), réessayer après 30s heurte
     * le même mur. Avec 10 minutes, la fenêtre glissante d'1h s'est écoulée
     * et le quota est libéré → les retries aboutissent.
     */
    public array $backoff = [600, 1200, 1800];
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

            // Ne pas retenter pour les adresses définitivement invalides
            // (domaine inexistant, adresse rejetée, etc.).
            // Désinscrire automatiquement le contact pour l'exclure des prochaines campagnes.
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

        // Ne pas écraser un statut `invalid` ou `bounced` déjà enregistré
        // par une tentative précédente — ces statuts sont définitifs.
        EmailLog::where('id', $this->emailLogId)
            ->whereIn('status', [EmailLog::STATUS_PENDING, EmailLog::STATUS_FAILED])
            ->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => substr($errMsg, 0, 500),
            ]);

        $campaign = Campaign::find($this->campaign->id);
        $campaign?->markAsSentIfAllEmailsAreSent();
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

        // ── Détection rate limit / quota AVANT les bounces ──────────────────
        // Ces erreurs sont TEMPORAIRES : OVH, Gmail, etc. limitent le débit.
        // Il ne faut PAS les marquer en bounced — l'adresse destinataire est valide.
        if (str_contains($message, 'quota exceeded')
            || str_contains($message, 'rate limit')
            || str_contains($message, 'too many')
            || str_contains($message, 'messages per hour')
            || str_contains($message, 'try again later')
            || str_contains($message, 'temporarily')
            || str_contains($message, 'try later')
            || str_contains($message, 'please retry')
            || str_contains($message, 'slow down')
            || str_contains($message, '4.7.') // codes 4xx = temporaires
            || str_contains($message, '452')  // boîte pleine temporaire
            || str_contains($message, 'connection timed out')
            || str_contains($message, 'timed out')) {
            return EmailLog::STATUS_FAILED; // temporaire → sera retenté
        }

        // ── Hard bounces (rejet permanent du destinataire) ───────────────────
        // Codes 5.1.x = problème de destinataire permanent
        if (str_contains($message, 'bounce')
            || str_contains($message, '5.1.0')
            || str_contains($message, '5.1.1')
            || str_contains($message, '5.1.2')
            || str_contains($message, '5.1.3')
            || str_contains($message, '5.1.6')
            || str_contains($message, '5.1.10')  // Microsoft: RecipientNotFound
            || str_contains($message, 'undeliverable')
            || str_contains($message, 'recipient not found')      // espaces
            || str_contains($message, 'recipientnotfound')        // camelcase/collé
            || str_contains($message, 'resolver.adr')             // Microsoft Exchange
            || str_contains($message, 'no route to host')
            || str_contains($message, 'host or domain name not found')
            || str_contains($message, 'name or service not known')) {
            return EmailLog::STATUS_BOUNCED;
        }

        // ── Adresses définitivement invalides ────────────────────────────────
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
            || str_contains($message, '5.5.4')   // invalid domain
            || str_contains($message, '550 5.4')  // routing error
        ) {
            return EmailLog::STATUS_INVALID;
        }

        // ── Rejet pour spam / politique ─────────────────────────────────────
        // Ces erreurs sont PERMANENTES mais liées à la réputation, pas à l'adresse.
        // On marque `failed` (pas bounce) pour ne pas désinscrire le contact.
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

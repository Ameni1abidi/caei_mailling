<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\ProspectInteraction;
use Illuminate\Support\Facades\Log;

/**
 * Service centralisé de traitement des bounces email.
 *
 * Utilisé par :
 * - ProcessBouncedEmailsCommand (IMAP polling pour OVH)
 * - BounceWebhookController (webhooks HTTP pour Mailgun/Brevo/SendGrid)
 */
class BounceHandlerService
{
    /**
     * Traite un hard bounce (adresse définitivement invalide).
     *
     * - 550 User unknown
     * - 551 User not local
     * - 553 Mailbox name invalid
     * - 5.1.1 The email account does not exist
     *
     * @param  string $email   Adresse email bouncée
     * @param  string $reason  Message d'erreur SMTP/NDR
     * @return bool  true si un contact a été mis à jour
     */
    public static function processHardBounce(string $email, string $reason = ''): bool
    {
        $email   = strtolower(trim($email));
        $contact = Contact::where('email', $email)->first();

        if (! $contact) {
            Log::info("[Bounce] Hard bounce pour email inconnu : {$email}");
            return false;
        }

        // Ne pas traiter deux fois un hard bounce
        if ($contact->hard_bounce) {
            return false;
        }

        $contact->update([
            'hard_bounce'        => true,
            'bounced_at'         => now(),
            'last_bounce_reason' => substr($reason, 0, 500),
        ]);

        // Marquer tous les logs pending/sent de ce contact comme bouncés
        EmailLog::where('contact_id', $contact->id)
            ->whereIn('status', [EmailLog::STATUS_PENDING, EmailLog::STATUS_SENT])
            ->update([
                'status'        => EmailLog::STATUS_BOUNCED,
                'error_message' => substr("Hard bounce: {$reason}", 0, 500),
            ]);

        // Journaliser dans les interactions prospect
        $contact->logInteraction(
            ProspectInteraction::TYPE_NOTE_ADDED,
            "Hard bounce détecté : {$reason}",
            metadata: ['bounce_type' => 'hard', 'reason' => $reason]
        );

        Log::info("[Bounce] Hard bounce traité pour {$email} : {$reason}");
        return true;
    }

    /**
     * Traite un soft bounce (erreur temporaire).
     *
     * - 421 Service temporarily unavailable
     * - 450 Mailbox unavailable
     * - 452 Mailbox full
     * - 4.x.x codes temporaires
     *
     * Après 3 soft bounces consécutifs → marqué comme hard bounce.
     *
     * @param  string $email   Adresse email bouncée
     * @param  string $reason  Message d'erreur SMTP/NDR
     * @return bool  true si un contact a été mis à jour
     */
    public static function processSoftBounce(string $email, string $reason = ''): bool
    {
        $email   = strtolower(trim($email));
        $contact = Contact::where('email', $email)->first();

        if (! $contact) {
            Log::info("[Bounce] Soft bounce pour email inconnu : {$email}");
            return false;
        }

        // Si déjà hard bounce, ignorer
        if ($contact->hard_bounce) {
            return false;
        }

        $newCount = ($contact->soft_bounce_count ?? 0) + 1;

        $contact->update([
            'soft_bounce_count'  => $newCount,
            'bounced_at'         => now(),
            'last_bounce_reason' => substr($reason, 0, 500),
        ]);

        // Marquer le dernier log en cours comme bounced
        EmailLog::where('contact_id', $contact->id)
            ->where('status', EmailLog::STATUS_SENT)
            ->latest()
            ->limit(1)
            ->update([
                'status'        => EmailLog::STATUS_BOUNCED,
                'error_message' => substr("Soft bounce #{$newCount}: {$reason}", 0, 500),
            ]);

        // 3 soft bounces → convertir en hard bounce automatiquement
        if ($newCount >= 3) {
            return self::processHardBounce($email, "3 soft bounces consécutifs — dernier : {$reason}");
        }

        Log::info("[Bounce] Soft bounce #{$newCount} pour {$email} : {$reason}");
        return true;
    }

    /**
     * Détermine si un code SMTP/message indique un hard ou soft bounce.
     *
     * Hard bounce → codes 5xx permanents
     * Soft bounce → codes 4xx temporaires
     *
     * @param  string $diagnosticCode  Le message de diagnostic SMTP
     * @return string 'hard' | 'soft' | 'unknown'
     */
    public static function classifyBounce(string $diagnosticCode): string
    {
        $code = trim($diagnosticCode);

        // Codes SMTP 5xx = erreurs permanentes (hard bounce)
        if (preg_match('/^5\d{2}\b/', $code)
            || str_contains(strtolower($code), 'user unknown')
            || str_contains(strtolower($code), 'does not exist')
            || str_contains(strtolower($code), 'invalid address')
            || str_contains(strtolower($code), 'no such user')
            || str_contains(strtolower($code), 'user not found')
            || str_contains(strtolower($code), 'account does not exist')
            || str_contains(strtolower($code), 'recipient rejected')
            || str_contains(strtolower($code), 'address rejected')
            || str_contains(strtolower($code), '5.1.1')
            || str_contains(strtolower($code), '5.1.2')
        ) {
            return 'hard';
        }

        // Codes SMTP 4xx = erreurs temporaires (soft bounce)
        if (preg_match('/^4\d{2}\b/', $code)
            || str_contains(strtolower($code), 'mailbox full')
            || str_contains(strtolower($code), 'temporarily unavailable')
            || str_contains(strtolower($code), 'try again later')
            || str_contains(strtolower($code), '4.2.2')
        ) {
            return 'soft';
        }

        return 'unknown';
    }
}

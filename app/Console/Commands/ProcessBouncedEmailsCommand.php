<?php

namespace App\Console\Commands;

use App\Services\BounceHandlerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Commande Artisan pour traiter les bounces OVH via IMAP.
 *
 * OVH SMTP ne propose pas de webhooks HTTP — les NDR (Non Delivery Reports)
 * sont envoyés par email à l'adresse Return-Path (ex: bounce@caei-afri.com).
 * Cette commande se connecte à la boîte IMAP, parse les NDR et met à jour les contacts.
 *
 * Usage :
 *   php artisan bounces:process
 *
 * Variables .env requises :
 *   BOUNCE_IMAP_HOST=ssl://imap.mail.ovh.net
 *   BOUNCE_IMAP_PORT=993
 *   BOUNCE_IMAP_USER=bounce@caei-afri.com
 *   BOUNCE_IMAP_PASS=votre-mot-de-passe
 */
class ProcessBouncedEmailsCommand extends Command
{
    protected $signature   = 'bounces:process {--dry-run : Simuler sans modifier la base de données}';
    protected $description = 'Traite les bounces email reçus dans la boîte IMAP bounce@';

    /** Nombre maximum d'emails traités par exécution (évite les timeouts) */
    private const MAX_EMAILS_PER_RUN = 100;

    public function handle(): int
    {
        $host    = config('services.bounce_imap.host');
        $port    = config('services.bounce_imap.port', 993);
        $user    = config('services.bounce_imap.username');
        $pass    = config('services.bounce_imap.password');
        $dryRun  = $this->option('dry-run');

        // Vérification de la configuration
        if (! $host || ! $user || ! $pass) {
            $this->error('Configuration IMAP de bounce manquante. Vérifiez BOUNCE_IMAP_HOST, BOUNCE_IMAP_USER, BOUNCE_IMAP_PASS dans .env');
            return self::FAILURE;
        }

        // Vérification que l'extension IMAP PHP est disponible
        if (! function_exists('imap_open')) {
            $this->error("L'extension PHP IMAP n'est pas activée. Activez-la dans php.ini : extension=imap");
            return self::FAILURE;
        }

        // Nettoyer le host : imap_open() n'accepte pas les préfixes "ssl://" ou "imap://"
        // Le SSL est spécifié via le flag /ssl dans la chaîne de mailbox
        $host = preg_replace('#^[a-z+]+://#i', '', $host);

        $mailbox = "{" . $host . ":" . $port . "/imap/ssl/novalidate-cert}INBOX";

        $this->info("Connexion IMAP : {$user}@{$host}:{$port}");

        $connection = @imap_open($mailbox, $user, $pass);

        if (! $connection) {
            $error = imap_last_error();
            $this->error("Impossible de se connecter à la boîte IMAP : {$error}");
            Log::error("[Bounce IMAP] Connexion échouée pour {$user} : {$error}");
            return self::FAILURE;
        }

        // Chercher les emails non lus dans la boîte
        $unseen = imap_search($connection, 'UNSEEN');

        if (! $unseen) {
            $this->info('Aucun email de bounce non lu.');
            imap_close($connection);
            return self::SUCCESS;
        }

        $total    = count($unseen);
        $toProcess = array_slice($unseen, 0, self::MAX_EMAILS_PER_RUN);
        $processed = 0;
        $hardCount = 0;
        $softCount = 0;
        $skipped   = 0;

        $this->info("📬 {$total} email(s) non lus — traitement de " . count($toProcess));
        $bar = $this->output->createProgressBar(count($toProcess));
        $bar->start();

        foreach ($toProcess as $msgNum) {
            $bar->advance();

            $header  = imap_headerinfo($connection, $msgNum);
            $rawBody = imap_fetchbody($connection, $msgNum, '');

            // Extraire l'adresse bouncée depuis les headers NDR
            $bouncedEmail = $this->extractBouncedEmail($rawBody, $header);

            if (! $bouncedEmail) {
                // Pas un NDR lisible — ignorer mais marquer comme lu
                if (! $dryRun) {
                    imap_setflag_full($connection, (string) $msgNum, '\\Seen');
                }
                $skipped++;
                continue;
            }

            // Extraire le code d'erreur SMTP (Diagnostic-Code)
            $reason       = $this->extractDiagnosticCode($rawBody);
            $bounceType   = BounceHandlerService::classifyBounce($reason);

            if ($dryRun) {
                $this->newLine();
                $this->line("  [DRY-RUN] {$bouncedEmail} → {$bounceType} bounce : {$reason}");
                $processed++;
                continue;
            }

            if ($bounceType === 'hard') {
                BounceHandlerService::processHardBounce($bouncedEmail, $reason);
                $hardCount++;
            } elseif ($bounceType === 'soft') {
                BounceHandlerService::processSoftBounce($bouncedEmail, $reason);
                $softCount++;
            } else {
                // Type inconnu → traiter comme soft bounce par sécurité
                BounceHandlerService::processSoftBounce($bouncedEmail, "Type inconnu : {$reason}");
                $softCount++;
            }

            // Marquer l'email comme lu et le déplacer dans un dossier "Traités"
            imap_setflag_full($connection, (string) $msgNum, '\\Seen');

            $processed++;
        }

        $bar->finish();
        $this->newLine(2);

        // Résumé
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Emails analysés',     $processed],
                ['Hard bounces',        $hardCount],
                ['Soft bounces',        $softCount],
                ['Ignorés (non-NDR)',   $skipped],
                ['Restants à traiter',  max(0, $total - count($toProcess))],
            ]
        );

        imap_close($connection, CL_EXPUNGE);

        Log::info("[Bounce IMAP] Traitement terminé : {$hardCount} hard, {$softCount} soft, {$skipped} ignorés");

        return self::SUCCESS;
    }

    /**
     * Extrait l'adresse email bouncée depuis un NDR.
     *
     * Les NDR (RFC 3464) contiennent des headers comme :
     *   Final-Recipient: rfc822; user@example.com
     *   Original-Recipient: rfc822; user@example.com
     */
    private function extractBouncedEmail(string $body, mixed $header): ?string
    {
        // 1. Header NDR standard : Final-Recipient
        if (preg_match('/Final-Recipient:\s*rfc822;\s*([^\s\r\n]+)/i', $body, $m)) {
            $email = trim($m[1]);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return strtolower($email);
            }
        }

        // 2. Original-Recipient
        if (preg_match('/Original-Recipient:\s*rfc822;\s*([^\s\r\n]+)/i', $body, $m)) {
            $email = trim($m[1]);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return strtolower($email);
            }
        }

        // 3. Fallback : chercher "To:" dans la partie texte du NDR
        if (preg_match('/^To:\s*([^\r\n]+)/mi', $body, $m)) {
            $email = trim(strip_tags($m[1]));
            // Extraire juste l'adresse si format "Nom <email>"
            if (preg_match('/<([^>]+)>/', $email, $inner)) {
                $email = $inner[1];
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return strtolower($email);
            }
        }

        return null;
    }

    /**
     * Extrait le code de diagnostic SMTP depuis un NDR.
     *
     * Format NDR (RFC 3464) :
     *   Diagnostic-Code: smtp; 550 5.1.1 User unknown
     */
    private function extractDiagnosticCode(string $body): string
    {
        // Header standard NDR
        if (preg_match('/Diagnostic-Code:\s*(?:smtp;\s*)?(.+?)(?:\r?\n(?!\s))/is', $body, $m)) {
            return trim(preg_replace('/\s+/', ' ', $m[1]));
        }

        // Status code seul
        if (preg_match('/Status:\s*(\d\.\d\.\d)/i', $body, $m)) {
            return trim($m[1]);
        }

        // Chercher un code SMTP dans le corps
        if (preg_match('/\b(5\d{2}|4\d{2})\s+.{5,80}/m', $body, $m)) {
            return trim($m[0]);
        }

        return 'Raison inconnue';
    }
}

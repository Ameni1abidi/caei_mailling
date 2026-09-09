<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Contact;
use App\Http\Controllers\CampaignController;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public Campaign $campaign;
    public Contact $contact;
    public ?int $emailLogId;
    public string $contenuPersonnalise;
    public string $objetPersonnalise;

    public function __construct(Campaign $campaign, Contact $contact, ?int $emailLogId = null)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
        $this->emailLogId = $emailLogId;
        $context = [
            'campaign'      => $campaign,
            'nom_seminaire' => $campaign->nom,
            'date'          => $campaign->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];

        $this->contenuPersonnalise = \App\Models\EmailTemplate::renderContent($campaign->contenu, $contact, $context);
        $this->objetPersonnalise   = CampaignController::personnaliser($campaign->objet, $contact, $context);
    }

    public function build()
    {
        $unsubscribeUrl = route('contact.unsubscribe', ['email' => $this->contact->email]);
        $contenuTexte   = $this->htmlToPlainText($this->contenuPersonnalise);

        // Configuration Multipart/Alternative : HTML + Version texte brut (indispensable anti-spam)
        $mail = $this->subject($this->objetPersonnalise)
            ->view('emails.campaign', [
                'emailLogId'         => $this->emailLogId,
                'contact'            => $this->contact,
                'objetPersonnalise'  => $this->objetPersonnalise,
                'contenuPersonnalise'=> $this->contenuPersonnalise,
            ])
            ->text('emails.campaign_text', [
                'contact'        => $this->contact,
                'contenuTexte'   => $contenuTexte,
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);

        // En-têtes optimisés pour délivrabilité en boîte Principale (sans étiquette bulk Promotions)
        $mail->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
            $headers = $message->getHeaders();

            // 1. Désinscription sécurisée en 1 clic (respect des critères anti-spam sans forcer l'onglet Promotions)
            $headers->addTextHeader('List-Unsubscribe', "<mailto:Contact@caei-afri.com?subject=Unsubscribe>, <{$unsubscribeUrl}>");
            $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');

            // 2. Empêche les réponses automatiques / Out of Office en cascade
            $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
        });

        // Pièces jointes éventuelles
        foreach ($this->campaign->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                $mail->attachFromStorageDisk('public', $attachment->file_path, $attachment->file_name, [
                    'mime' => $attachment->mime_type,
                ]);
            }
        }

        return $mail;
    }

    /**
     * Convertit un contenu HTML en texte brut propre pour la version multipart/alternative.
     */
    private function htmlToPlainText(string $html): string
    {
        // 1. Remplacer les sauts de ligne et blocs de structure
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/(p|div|h[1-6]|tr|li)>/i', "\n\n", $text);

        // 2. Préserver les liens sous format Texte (URL)
        $text = preg_replace_callback('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function ($matches) {
            $url = trim($matches[1]);
            $label = trim(strip_tags($matches[2]));
            if (empty($label) || $label === $url) {
                return $url;
            }
            return "{$label} ({$url})";
        }, $text);

        // 3. Supprimer toutes les autres balises HTML
        $text = strip_tags($text);

        // 4. Nettoyer les entités HTML et les espaces multiples
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t]+/", ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }
}

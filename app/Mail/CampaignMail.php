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
            'campaign' => $campaign,
            'nom_seminaire' => $campaign->nom,
            'date' => $campaign->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];

        $this->contenuPersonnalise = \App\Models\EmailTemplate::renderContent($campaign->contenu, $contact, $context);
        $this->objetPersonnalise = CampaignController::personnaliser($campaign->objet, $contact, $context);
    }

    public function build()
    {
        $mail = $this->subject($this->objetPersonnalise)
            ->view('emails.campaign', [
                'emailLogId' => $this->emailLogId,
                'contact' => $this->contact,
                'objetPersonnalise' => $this->objetPersonnalise,
            ]);

        // Headers anti-spam obligatoires (requis par Gmail/Yahoo depuis 2024)
        $unsubscribeUrl = route('contact.unsubscribe', ['email' => $this->contact->email]);
        $mail->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
            $message->getHeaders()->addTextHeader('List-Unsubscribe', "<{$unsubscribeUrl}>");
            $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            $message->getHeaders()->addTextHeader('Precedence', 'bulk');
        });

        foreach ($this->campaign->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                $mail->attachFromStorageDisk('public', $attachment->file_path, $attachment->file_name, [
                    'mime' => $attachment->mime_type,
                ]);
            }
        }

        return $mail;
    }
}

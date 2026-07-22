<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Contact;
use App\Http\Controllers\CampaignController;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public Campaign $campaign;
    public Contact $contact;
    public string $contenuPersonnalise;
    public string $objetPersonnalise;

    public function __construct(Campaign $campaign, Contact $contact)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
        $context = [
            'campaign' => $campaign,
            'nom_seminaire' => $campaign->nom,
            'date' => $campaign->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];

        $this->contenuPersonnalise = CampaignController::personnaliser($campaign->contenu, $contact, $context);
        $this->objetPersonnalise = CampaignController::personnaliser($campaign->objet, $contact, $context);
    }

    public function build()
    {
        return $this->subject($this->objetPersonnalise)
            ->view('emails.campaign');
    }
}

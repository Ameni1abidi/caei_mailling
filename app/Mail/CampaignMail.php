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
        $this->contenuPersonnalise = CampaignController::personnaliser($campaign->contenu, $contact);
        $this->objetPersonnalise = CampaignController::personnaliser($campaign->objet, $contact);
    }

    public function build()
    {
        return $this->subject($this->objetPersonnalise)
            ->view('emails.campaign');
    }
}
<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\ProspectInteraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProspectStatusAfterCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Campaign $campaign
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Récupérer tous les emails logs de cette campagne
        $emailLogs = $this->campaign->emailLogs()->with('contact')->get();

        foreach ($emailLogs as $log) {
            $contact = $log->contact;

            if ($log->status === 'delivered' || $log->status === 'sent') {
                // Mettre à jour le statut à "Email envoyé" s'il est en phase initiale
                if ($contact->status === Contact::STATUS_NOUVEAU) {
                    $contact->updateStatusWithLog(
                        Contact::STATUS_EMAIL_ENVOYE,
                        "Email de la campagne '{$this->campaign->nom}' envoyé"
                    );
                }

                // Log interaction email sent
                $contact->logInteraction(
                    ProspectInteraction::TYPE_EMAIL_SENT,
                    "Email envoyé: {$this->campaign->nom}",
                    $this->campaign->id,
                    ['subject' => $this->campaign->objet]
                );

                // Mettre à jour last_campaign_id et last_interaction
                $contact->update([
                    'last_campaign_id' => $this->campaign->id,
                    'last_interaction' => $log->sent_at ?? now(),
                ]);
            }

            if ($log->opened) {
                // Mettre à jour le statut à "Email ouvert"
                if (in_array($contact->status, [Contact::STATUS_NOUVEAU, Contact::STATUS_EMAIL_ENVOYE])) {
                    $contact->updateStatusWithLog(
                        Contact::STATUS_EMAIL_OUVERT,
                        "Email de la campagne '{$this->campaign->nom}' ouvert"
                    );
                }

                // Log interaction email opened
                $contact->logInteraction(
                    ProspectInteraction::TYPE_EMAIL_OPENED,
                    "Email ouvert: {$this->campaign->nom}",
                    $this->campaign->id
                );
            }

            if ($log->clicked) {
                // Log interaction email clicked
                $contact->logInteraction(
                    ProspectInteraction::TYPE_EMAIL_CLICKED,
                    "Lien cliqué dans l'email: {$this->campaign->nom}",
                    $this->campaign->id
                );
            }
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'entreprise',
        'fonction',
        'email',
        'telephone',
        'whatsapp',
        'pays',
        'ville',
        'secteur_activite',
        'source',
        'status',
        'unsubscribed_at',
        'bounced_at',
        'notes',
        'last_interaction',
        'next_followup_date',
        'last_campaign_id'
    ];

    protected function casts(): array
    {
        return [
            'unsubscribed_at' => 'datetime',
            'bounced_at' => 'datetime',
            'last_interaction' => 'datetime',
            'next_followup_date' => 'datetime',
        ];
    }

    public const STATUS_NOUVEAU = 'Nouveau prospect';
    public const STATUS_EMAIL_ENVOYE = 'Email envoyé';
    public const STATUS_EMAIL_OUVERT = 'Email ouvert';
    public const STATUS_INTERESSE = 'Intéressé';
    public const STATUS_A_RELANCER = 'À relancer';
    public const STATUS_CLIENT = 'Client';

    public static function getProspectStatuses(): array
    {
        return [
            self::STATUS_NOUVEAU => [
                'label' => 'Nouveau prospect',
                'badge' => 'bg-slate-100 text-slate-700 border-slate-200/80',
                'dot' => 'bg-slate-400',
                'description' => 'Prospect récemment ajouté'
            ],
            self::STATUS_EMAIL_ENVOYE => [
                'label' => 'Email envoyé',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                'dot' => 'bg-blue-500',
                'description' => 'Campagne email transmise'
            ],
            self::STATUS_EMAIL_OUVERT => [
                'label' => 'Email ouvert',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                'dot' => 'bg-indigo-500',
                'description' => 'A ouvert au moins un email'
            ],
            self::STATUS_INTERESSE => [
                'label' => 'Intéressé',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                'dot' => 'bg-amber-500',
                'description' => 'Prospect intéressé'
            ],
            self::STATUS_A_RELANCER => [
                'label' => 'À relancer',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                'dot' => 'bg-rose-500',
                'description' => 'Relance nécessaire'
            ],
            self::STATUS_CLIENT => [
                'label' => 'Client',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                'dot' => 'bg-emerald-500',
                'description' => 'Converti en client'
            ],
        ];
    }

    /**
     * Advance prospect status automatically if in early stages.
     */
    public function advanceStatusTo(string $targetStatus): void
    {
        $hierarchy = [
            self::STATUS_NOUVEAU => 1,
            self::STATUS_EMAIL_ENVOYE => 2,
            self::STATUS_EMAIL_OUVERT => 3,
        ];

        $currentLevel = $hierarchy[$this->status] ?? 99; // If already Intéressé, À relancer, Client -> don't overwrite automatically
        $targetLevel = $hierarchy[$targetStatus] ?? 0;

        if ($targetLevel > $currentLevel) {
            $this->update(['status' => $targetStatus]);
        }
    }

    /**
     * Get the categories/lists that this contact belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the email logs for this contact.
     */
    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    /**
     * Get the last campaign sent to this contact.
     */
    public function lastCampaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'last_campaign_id');
    }

    /**
     * Get the prospect interactions for this contact.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(ProspectInteraction::class)->latest();
    }

    /**
     * Log a new prospect interaction.
     */
    public function logInteraction(string $type, ?string $description = null, ?int $campaignId = null, ?array $metadata = null): ProspectInteraction
    {
        // Update last_interaction timestamp
        $this->update(['last_interaction' => now()]);

        return $this->interactions()->create([
            'type' => $type,
            'description' => $description,
            'campaign_id' => $campaignId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Schedule a follow-up for this contact.
     */
    public function scheduleFollowUp(\DateTime $date, ?string $note = null): void
    {
        $this->update(['next_followup_date' => $date]);
        
        $this->logInteraction(
            ProspectInteraction::TYPE_FOLLOW_UP_SCHEDULED,
            $note ?? "Relance planifiée pour {$date->format('d/m/Y H:i')}",
            metadata: ['scheduled_date' => $date->toIso8601String()]
        );
    }

    /**
     * Add a note to the contact and log interaction.
     */
    public function addNote(string $note): void
    {
        $timestamp = now()->format('d/m/Y H:i');
        $newNote = "[{$timestamp}] {$note}";
        
        // Update notes field
        $this->update([
            'notes' => $this->notes ? $this->notes . "\n" . $newNote : $newNote
        ]);

        // Log interaction
        $this->logInteraction(
            ProspectInteraction::TYPE_NOTE_ADDED,
            $note
        );
    }

    /**
     * Update contact status and log the change.
     */
    public function updateStatusWithLog(string $newStatus, ?string $note = null): void
    {
        $oldStatus = $this->status;
        
        $this->update(['status' => $newStatus]);
        
        // Log interaction
        $description = $note ?? "Changement de statut: {$oldStatus} → {$newStatus}";
        $this->logInteraction(
            ProspectInteraction::TYPE_STATUS_CHANGE,
            $description,
            metadata: [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );
    }
}
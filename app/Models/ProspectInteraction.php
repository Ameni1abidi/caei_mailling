<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectInteraction extends Model
{
    protected $fillable = [
        'contact_id',
        'campaign_id',
        'type',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'json',
        ];
    }

    public const TYPE_EMAIL_SENT = 'email_sent';
    public const TYPE_EMAIL_OPENED = 'email_opened';
    public const TYPE_EMAIL_CLICKED = 'email_clicked';
    public const TYPE_STATUS_CHANGE = 'status_change';
    public const TYPE_NOTE_ADDED = 'note_added';
    public const TYPE_FOLLOW_UP_SCHEDULED = 'follow_up_scheduled';
    public const TYPE_MANUAL_CONTACT = 'manual_contact';

    public static function getInteractionTypes(): array
    {
        return [
            self::TYPE_EMAIL_SENT => [
                'label' => 'Email envoyé',
                'icon' => 'envelope-open',
                'color' => 'text-blue-600',
                'bg' => 'bg-blue-50'
            ],
            self::TYPE_EMAIL_OPENED => [
                'label' => 'Email ouvert',
                'icon' => 'envelope-open',
                'color' => 'text-indigo-600',
                'bg' => 'bg-indigo-50'
            ],
            self::TYPE_EMAIL_CLICKED => [
                'label' => 'Lien cliqué',
                'icon' => 'cursor-click',
                'color' => 'text-purple-600',
                'bg' => 'bg-purple-50'
            ],
            self::TYPE_STATUS_CHANGE => [
                'label' => 'Changement de statut',
                'icon' => 'arrow-right',
                'color' => 'text-orange-600',
                'bg' => 'bg-orange-50'
            ],
            self::TYPE_NOTE_ADDED => [
                'label' => 'Note ajoutée',
                'icon' => 'document-text',
                'color' => 'text-green-600',
                'bg' => 'bg-green-50'
            ],
            self::TYPE_FOLLOW_UP_SCHEDULED => [
                'label' => 'Relance planifiée',
                'icon' => 'calendar',
                'color' => 'text-amber-600',
                'bg' => 'bg-amber-50'
            ],
            self::TYPE_MANUAL_CONTACT => [
                'label' => 'Contact manuel',
                'icon' => 'phone',
                'color' => 'text-teal-600',
                'bg' => 'bg-teal-50'
            ],
        ];
    }

    /**
     * Get the contact associated with this interaction.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the campaign associated with this interaction.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}

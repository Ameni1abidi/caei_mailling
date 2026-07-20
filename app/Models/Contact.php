<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'unsubscribed_at' => 'datetime',
            'bounced_at' => 'datetime',
        ];
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
}
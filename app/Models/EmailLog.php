<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'contact_id',
        'status',
        'opened',
        'clicked',
        'error_message',
        'sent_at'
    ];

    protected function casts(): array
    {
        return [
            'opened' => 'boolean',
            'clicked' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the campaign associated with the log.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the contact associated with the log.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignAttachment extends Model
{
    protected $fillable = [
        'campaign_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type'
    ];

    /**
     * Get the campaign that owns the attachment.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}

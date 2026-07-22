<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignAttachment extends Model
{
    public const TYPE_PROGRAMME_PDF = 'programme_pdf';
    public const TYPE_BROCHURE = 'brochure';
    public const TYPE_PRESENTATION_CAEI = 'presentation_caei';
    public const TYPE_FORMULAIRE_INSCRIPTION = 'formulaire_inscription';

    protected $fillable = [
        'campaign_id',
        'attachment_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type'
    ];

    public static function types(): array
    {
        return [
            self::TYPE_PROGRAMME_PDF => 'Programme PDF',
            self::TYPE_BROCHURE => 'Brochure',
            self::TYPE_PRESENTATION_CAEI => 'Presentation CAEI',
            self::TYPE_FORMULAIRE_INSCRIPTION => 'Formulaire inscription',
        ];
    }

    public function typeLabel(): string
    {
        return self::types()[$this->attachment_type] ?? 'Piece jointe';
    }

    /**
     * Get the campaign that owns the attachment.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}

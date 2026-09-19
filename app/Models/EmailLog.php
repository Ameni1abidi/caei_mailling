<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailLog extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_SENT      = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_BOUNCED   = 'bounced';
    public const STATUS_INVALID   = 'invalid';
    public const STATUS_FAILED    = 'failed';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SENT,
            self::STATUS_DELIVERED,
            self::STATUS_BOUNCED,
            self::STATUS_INVALID,
            self::STATUS_FAILED,
        ];
    }

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'tracking_token',
        'status',
        'retry_count',
        'opened',
        'clicked',
        'clicked_at',
        'clicked_count',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'opened'        => 'boolean',
            'clicked'       => 'boolean',
            'clicked_at'    => 'datetime',
            'clicked_count' => 'integer',
            'retry_count'   => 'integer',
            'sent_at'       => 'datetime',
        ];
    }

    /**
     * Auto-génère un UUID unique à la création du log si absent.
     */
    protected static function booted(): void
    {
        static::creating(function (self $log) {
            if (empty($log->tracking_token)) {
                $log->tracking_token = (string) Str::uuid();
            }
        });
    }

    /**
     * Trouve un log par son token de tracking (opaque, non-séquentiel).
     */
    public static function findByToken(string $token): ?self
    {
        return static::with('contact')->where('tracking_token', $token)->first();
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
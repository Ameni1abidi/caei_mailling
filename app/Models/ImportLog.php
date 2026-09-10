<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportLog extends Model
{
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE       = 'done';
    public const STATUS_FAILED     = 'failed';

    protected $fillable = [
        'filename',
        'disk_path',
        'temp_path',
        'status',
        'total_rows',
        'imported',
        'duplicates',
        'errors',
        'error_details',
        'category_ids',
        'import_options',
        'column_mapping',
        'user_id',
    ];

    protected $casts = [
        'error_details'  => 'array',
        'category_ids'   => 'array',
        'import_options' => 'array',
        'column_mapping' => 'array',
    ];

    public function isDone(): bool
    {
        return in_array($this->status, [self::STATUS_DONE, self::STATUS_FAILED], true);
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * User who performed the import.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Contacts created during this import.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Campaigns targeting this import file.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'import_log_id');
    }
}

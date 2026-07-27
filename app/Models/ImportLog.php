<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportLog extends Model
{
    protected $fillable = [
        'filename',
        'disk_path',
        'total_rows',
        'imported',
        'duplicates',
        'errors',
        'user_id',
    ];

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
}

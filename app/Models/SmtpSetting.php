<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'driver',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'api_key',
        'sender_name',
        'sender_email',
        'reply_to_email',
        'rate_limit',
        'is_active',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
            'port' => 'integer',
            'rate_limit' => 'integer',
        ];
    }
}
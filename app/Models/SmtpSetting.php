<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'provider',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'api_key',
        'sender_name',
        'sender_email',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }
}

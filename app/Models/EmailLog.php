<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = ['campaign_id', 'contact_id', 'status', 'opened', 'clicked', 'sent_at'];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
}
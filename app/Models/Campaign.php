<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Campaign extends Model
{
    protected $fillable = ['nom', 'objet', 'contenu', 'categorie_cible', 'date_envoi', 'statut', 'created_by'];

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
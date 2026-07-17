<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'entreprise', 'fonction', 'email', 'telephone',
        'whatsapp', 'pays', 'ville', 'secteur_activite', 'source', 'categorie', 'notes'
    ];

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
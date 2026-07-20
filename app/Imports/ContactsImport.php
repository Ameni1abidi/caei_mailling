<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        // Évite les doublons : ignore si l'email existe déjà
        if (Contact::where('email', $row['email'])->exists()) {
            return null;
        }

        return new Contact([
            'nom' => $row['nom'] ?? '',
            'prenom' => $row['prenom'] ?? '',
            'entreprise' => $row['entreprise'] ?? null,
            'fonction' => $row['fonction'] ?? null,
            'email' => $row['email'],
            'telephone' => $row['telephone'] ?? null,
            'whatsapp' => $row['whatsapp'] ?? null,
            'pays' => $row['pays'] ?? null,
            'ville' => $row['ville'] ?? null,
            'secteur_activite' => $row['secteur_activite'] ?? null,
            'source' => $row['source'] ?? 'import',
            'categorie' => $row['categorie'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email',
        ];
    }
}
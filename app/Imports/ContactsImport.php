<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;
use Throwable;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private array $errors = [];
    private int $duplicates = 0;
    private int $imported = 0;
    private int $totalRows = 0;
    private ?int $importLogId;

    public function __construct(?int $importLogId = null)
    {
        $this->importLogId = $importLogId;
    }

    public function model(array $row): ?Contact
    {
        $this->totalRows++;

        // Évite les doublons : ignore si l'email existe déjà
        if (Contact::where('email', $row['email'])->exists()) {
            $this->duplicates++;
            return null;
        }

        $this->imported++;

        return new Contact([
            'nom'              => $row['nom'] ?? '',
            'prenom'           => $row['prenom'] ?? '',
            'entreprise'       => $row['entreprise'] ?? null,
            'fonction'         => $row['fonction'] ?? null,
            'email'            => $row['email'],
            'telephone'        => $row['telephone'] ?? null,
            'whatsapp'         => $row['whatsapp'] ?? null,
            'pays'             => $row['pays'] ?? null,
            'ville'            => $row['ville'] ?? null,
            'secteur_activite' => $row['secteur_activite'] ?? null,
            'source'           => $row['source'] ?? 'import',
            'import_log_id'    => $this->importLogId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nom'   => 'required',
            'prenom' => 'required',
            'email' => 'required|email',
        ];
    }

    public function onError(Throwable $e): void
    {
        $this->errors[] = $e;
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->errors[] = $failure;
        }
    }

    public function errors(): Collection
    {
        return collect($this->errors);
    }

    public function getDuplicates(): int
    {
        return $this->duplicates;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }
}
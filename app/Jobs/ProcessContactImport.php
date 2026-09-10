<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\ImportLog;
use App\Services\ContactColumnMapper;
use App\Services\ImportFileReader;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProcessContactImport implements ShouldQueue
{
    use Queueable;

    /**
     * Nombre de tentatives max si le job échoue.
     */
    public int $tries = 1;

    /**
     * Timeout en secondes (10 min pour les très gros fichiers).
     */
    public int $timeout = 600;

    /**
     * Taille des chunks (lignes traitées par lot en mémoire).
     */
    private const CHUNK_SIZE = 500;

    public function __construct(
        public readonly int $importLogId
    ) {}

    public function handle(): void
    {
        $importLog = ImportLog::find($this->importLogId);

        if (!$importLog) {
            Log::error("ProcessContactImport: ImportLog #{$this->importLogId} introuvable.");
            return;
        }

        // Marquer l'import comme en cours
        $importLog->update(['status' => 'processing']);

        try {
            $this->processImport($importLog);
        } catch (\Throwable $e) {
            $importLog->update([
                'status'       => 'failed',
                'error_details' => [['row' => 0, 'error' => 'Erreur système : ' . $e->getMessage()]],
            ]);
            Log::error("ProcessContactImport #{$this->importLogId} failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function processImport(ImportLog $importLog): void
    {
        $storagePath = $importLog->temp_path;
        $mapping     = $importLog->column_mapping ?? [];
        $options     = $importLog->import_options ?? [];
        $categoryIds = $importLog->category_ids ?? [];
        $dupStrategy = $options['duplicate_strategy'] ?? 'ignore';

        if (!$storagePath || !Storage::disk('public')->exists($storagePath)) {
            throw new \RuntimeException("Fichier temporaire introuvable : {$storagePath}");
        }

        $fullPath  = Storage::disk('public')->path($storagePath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Compteurs
        $imported   = 0;
        $duplicates = 0;
        $errors     = [];
        $totalRows  = 0;

        // Emails déjà vus dans le fichier (doublons internes)
        $seenEmails = [];

        // Emails déjà en base (chargés en mémoire par lots via chunk DB)
        $existingEmails = Contact::pluck('email')->map('strtolower')->flip()->all();

        if (in_array($extension, ['csv', 'txt'])) {
            // ─── CSV ────────────────────────────────────────────────────
            $analysed  = ImportFileReader::analyze($storagePath);
            $separator = $analysed['separator'] ?? ',';
            $content   = $this->readFileAsUtf8($fullPath);
            $lines     = str_getcsv($content, "\n");

            $headers = null;
            $batch   = [];

            foreach ($lines as $lineIndex => $line) {
                if (trim($line) === '') {
                    continue;
                }

                $row = str_getcsv($line, $separator);

                if ($headers === null) {
                    $headers = array_map('trim', $row);
                    continue;
                }

                $totalRows++;
                $rowNumber = $lineIndex + 1;

                $assoc = [];
                foreach ($headers as $i => $header) {
                    $assoc[$header] = $row[$i] ?? '';
                }

                $result = $this->processRow($assoc, $mapping, $dupStrategy, $seenEmails, $existingEmails, $rowNumber);

                if ($result['status'] === 'ok') {
                    $batch[] = $result['data'];
                    $seenEmails[strtolower($result['data']['email'])] = true;
                } elseif ($result['status'] === 'duplicate') {
                    $duplicates++;
                } else {
                    $errors[] = $result['error'];
                }

                // Insérer par lots de CHUNK_SIZE
                if (count($batch) >= self::CHUNK_SIZE) {
                    $imported += $this->batchInsert($batch, $categoryIds, $importLog->id, $dupStrategy, $existingEmails);
                    $batch = [];
                }
            }

            // Dernier lot
            if (!empty($batch)) {
                $imported += $this->batchInsert($batch, $categoryIds, $importLog->id, $dupStrategy, $existingEmails);
            }
        } else {
            // ─── XLSX/XLS ────────────────────────────────────────────────
            $processor = new class(
                $mapping, $dupStrategy, $seenEmails, $existingEmails, $importLog->id, $categoryIds
            ) implements ToArray, WithChunkReading {
                public int $imported   = 0;
                public int $duplicates = 0;
                public array $errors   = [];
                public int $totalRows  = 0;
                private bool $headersRead = false;
                private array $headers = [];

                public function __construct(
                    private array $mapping,
                    private string $dupStrategy,
                    private array &$seenEmails,
                    private array &$existingEmails,
                    private int $importLogId,
                    private array $categoryIds,
                ) {}

                public function chunkSize(): int { return 500; }

                public function array(array $rows): void
                {
                    $batch = [];
                    foreach ($rows as $rowIndex => $row) {
                        $row = array_map('strval', $row);

                        if (!$this->headersRead) {
                            $this->headers = array_map('trim', $row);
                            $this->headersRead = true;
                            continue;
                        }

                        if (empty(array_filter($row))) {
                            continue;
                        }

                        $this->totalRows++;
                        $assoc = [];
                        foreach ($this->headers as $i => $header) {
                            $assoc[$header] = $row[$i] ?? '';
                        }

                        $result = ProcessContactImport::processRowStatic(
                            $assoc, $this->mapping, $this->dupStrategy,
                            $this->seenEmails, $this->existingEmails, $this->totalRows
                        );

                        if ($result['status'] === 'ok') {
                            $batch[] = $result['data'];
                            $this->seenEmails[strtolower($result['data']['email'])] = true;
                        } elseif ($result['status'] === 'duplicate') {
                            $this->duplicates++;
                        } else {
                            $this->errors[] = $result['error'];
                        }
                    }

                    if (!empty($batch)) {
                        $this->imported += ProcessContactImport::batchInsertStatic(
                            $batch, $this->categoryIds, $this->importLogId,
                            $this->dupStrategy, $this->existingEmails
                        );
                    }
                }
            };

            Excel::import($processor, $storagePath, 'public');
            $imported   = $processor->imported;
            $duplicates = $processor->duplicates;
            $errors     = $processor->errors;
            $totalRows  = $processor->totalRows;
        }

        // Nettoyer le fichier temporaire
        Storage::disk('public')->delete($storagePath);

        // Sauvegarder les résultats
        $importLog->update([
            'status'        => 'done',
            'total_rows'    => $totalRows,
            'imported'      => $imported,
            'duplicates'    => $duplicates,
            'errors'        => count($errors),
            'error_details' => array_slice($errors, 0, 500), // Max 500 erreurs stockées
            'temp_path'     => null,
        ]);
    }

    /**
     * Traite une ligne et retourne le statut + données nettoyées.
     */
    private function processRow(
        array $assoc, array $mapping, string $dupStrategy,
        array &$seenEmails, array &$existingEmails, int $rowNumber
    ): array {
        return self::processRowStatic($assoc, $mapping, $dupStrategy, $seenEmails, $existingEmails, $rowNumber);
    }

    /**
     * Version statique pour usage dans les classes anonymes (Excel chunks).
     */
    public static function processRowStatic(
        array $assoc, array $mapping, string $dupStrategy,
        array &$seenEmails, array &$existingEmails, int $rowNumber
    ): array {
        // Appliquer le mapping : colonnes fichier → champs DB
        $data = [];
        foreach ($mapping as $fileCol => $dbField) {
            if (!$dbField || $dbField === '__ignore__') {
                continue;
            }
            $raw = $assoc[$fileCol] ?? '';
            $data[$dbField] = ContactColumnMapper::sanitizeValue($dbField, $raw);
        }

        // Valider l'email (obligatoire)
        $email = $data['email'] ?? null;
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => 'error',
                'error'  => [
                    'row'   => $rowNumber,
                    'error' => $email
                        ? "Email invalide : {$email}"
                        : 'Email manquant',
                ],
            ];
        }

        // Doublon interne au fichier
        if (isset($seenEmails[strtolower($email)])) {
            return ['status' => 'duplicate'];
        }

        // Doublon avec la base existante
        if (isset($existingEmails[strtolower($email)])) {
            if ($dupStrategy === 'ignore') {
                return ['status' => 'duplicate'];
            }
            // strategy = 'update' : on marque pour mise à jour
            $data['_update'] = true;
        }

        return ['status' => 'ok', 'data' => $data];
    }

    /**
     * Insère un lot de contacts en base (batch insert + associations catégories).
     */
    private function batchInsert(
        array $batch, array $categoryIds, int $importLogId,
        string $dupStrategy, array &$existingEmails
    ): int {
        return self::batchInsertStatic($batch, $categoryIds, $importLogId, $dupStrategy, $existingEmails);
    }

    public static function batchInsertStatic(
        array $batch, array $categoryIds, int $importLogId,
        string $dupStrategy, array &$existingEmails
    ): int {
        $imported   = 0;
        $toInsert   = [];
        $toUpdate   = [];
        $now        = now()->toDateTimeString();
        $defaultStatus = Contact::STATUS_NOUVEAU;

        foreach ($batch as $data) {
            $isUpdate = $data['_update'] ?? false;
            unset($data['_update']);

            $data['import_log_id']   = $importLogId;
            $data['prospect_status'] = $data['prospect_status'] ?? $defaultStatus;
            $data['nom']             = $data['nom'] ?? '';
            $data['prenom']          = $data['prenom'] ?? '';

            if ($isUpdate) {
                $toUpdate[] = $data;
            } else {
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                $toInsert[] = $data;
            }
        }

        // Batch insert nouveaux contacts
        if (!empty($toInsert)) {
            try {
                DB::table('contacts')->insertOrIgnore($toInsert);
                $imported += count($toInsert);

                // Mettre à jour les emails connus
                foreach ($toInsert as $row) {
                    $existingEmails[strtolower($row['email'])] = true;
                }
            } catch (\Throwable $e) {
                Log::warning("batchInsert error: " . $e->getMessage());
            }
        }

        // Mise à jour des contacts existants
        foreach ($toUpdate as $data) {
            try {
                $email = $data['email'];
                unset($data['email'], $data['created_at']);
                $data['updated_at'] = $now;
                Contact::where('email', $email)->update($data);
                $imported++;
            } catch (\Throwable $e) {
                Log::warning("batchUpdate error: " . $e->getMessage());
            }
        }

        // Associer aux catégories
        if (!empty($categoryIds) && !empty($toInsert)) {
            $insertedIds = Contact::whereIn('email', array_column($toInsert, 'email'))
                ->pluck('id');

            $pivots = [];
            foreach ($insertedIds as $contactId) {
                foreach ($categoryIds as $catId) {
                    $pivots[] = ['contact_id' => $contactId, 'category_id' => $catId];
                }
            }

            if (!empty($pivots)) {
                DB::table('category_contact')->insertOrIgnore($pivots);
            }
        }

        return $imported;
    }

    /**
     * Lit un fichier CSV en UTF-8 (gère l'encodage automatiquement).
     */
    private function readFileAsUtf8(string $path): string
    {
        $content = file_get_contents($path);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'UTF-16'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        // Retirer BOM
        return ltrim($content, "\xEF\xBB\xBF");
    }
}

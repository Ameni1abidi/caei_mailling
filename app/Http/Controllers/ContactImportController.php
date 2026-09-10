<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessContactImport;
use App\Models\Category;
use App\Models\ImportLog;
use App\Services\ContactColumnMapper;
use App\Services\ImportFileReader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Contrôleur du wizard d'import de contacts (5 étapes).
 *
 * Étape 1 : Upload du fichier
 * Étape 2 : Mapping des colonnes
 * Étape 3 : Prévisualisation + choix des options
 * Étape 4 : Lancement du job + suivi progression
 * Étape 5 : Rapport final
 */
class ContactImportController extends Controller
{
    // ─────────────────────────────────────────────
    // ÉTAPE 1 : Page d'upload (GET)
    // ─────────────────────────────────────────────

    public function showUpload(): View
    {
        return view('contacts.import.step1-upload');
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 1 : Traiter l'upload (POST)
    // ─────────────────────────────────────────────

    public function handleUpload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:51200', // 50 MB max
                'mimes:xlsx,xls,csv,txt',
            ],
        ], [
            'file.required'  => 'Veuillez sélectionner un fichier.',
            'file.max'       => 'Le fichier ne doit pas dépasser 50 Mo.',
            'file.mimes'     => 'Format accepté : .xlsx, .xls, .csv, .txt',
        ]);

        $file = $request->file('file');

        // Vérification supplémentaire de sécurité (MIME réel)
        $allowedMimes = [
            'text/csv', 'text/plain', 'application/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            return back()->withErrors(['file' => 'Type de fichier non autorisé.']);
        }

        // Stocker le fichier dans un dossier temporaire
        $storagePath = $file->store('imports/temp', 'public');

        // Analyser le fichier (colonnes + aperçu)
        try {
            $analysis = ImportFileReader::analyze($storagePath);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($storagePath);
            return back()->withErrors(['file' => 'Impossible de lire le fichier : ' . $e->getMessage()]);
        }

        if (empty($analysis['headers'])) {
            Storage::disk('public')->delete($storagePath);
            return back()->withErrors(['file' => 'Le fichier est vide ou ne contient pas de colonnes valides.']);
        }

        // Créer le log d'import (statut = pending)
        $importLog = ImportLog::create([
            'filename'   => $file->getClientOriginalName(),
            'temp_path'  => $storagePath,
            'status'     => 'pending',
            'user_id'    => Auth::id(),
            'imported'   => 0,
            'duplicates' => 0,
            'errors'     => 0,
            'total_rows' => $analysis['total_estimate'],
        ]);

        // Stocker l'analyse en session
        session([
            "import_{$importLog->id}_analysis" => $analysis,
        ]);

        return redirect()->route('contacts.import.mapping', $importLog->id);
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 2 : Mapping (GET)
    // ─────────────────────────────────────────────

    public function showMapping(int $importLogId): View|RedirectResponse
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        $analysis = session("import_{$importLogId}_analysis");
        if (!$analysis) {
            return redirect()->route('contacts.import.upload')
                ->with('error', 'Session expirée. Veuillez re-uploader le fichier.');
        }

        $headers       = $analysis['headers'];
        $autoMapping   = ContactColumnMapper::autoMap($headers);
        $mappableFields = ContactColumnMapper::getMappableFields();
        $categories    = Category::orderBy('name')->get(['id', 'name']);

        return view('contacts.import.step2-mapping', compact(
            'importLog', 'headers', 'autoMapping', 'mappableFields', 'categories'
        ));
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 2 : Sauvegarder le mapping (POST)
    // ─────────────────────────────────────────────

    public function saveMapping(Request $request, int $importLogId): RedirectResponse
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        $analysis = session("import_{$importLogId}_analysis");
        if (!$analysis) {
            return redirect()->route('contacts.import.upload')
                ->with('error', 'Session expirée. Veuillez re-uploader le fichier.');
        }

        $mapping     = $request->input('mapping', []);
        $categoryIds = $request->input('category_ids', []);
        $dupStrategy = $request->input('duplicate_strategy', 'ignore');

        // Vérifier que l'email est mappé
        if (!in_array('email', $mapping, true)) {
            return back()->withErrors([
                'mapping' => 'Le champ Email est obligatoire pour importer les contacts.',
            ])->withInput();
        }

        // Nettoyer le mapping (retirer les colonnes ignorées)
        $cleanMapping = [];
        foreach ($mapping as $fileCol => $dbField) {
            if ($dbField && $dbField !== '__ignore__') {
                $cleanMapping[$fileCol] = $dbField;
            }
        }

        // Sauvegarder le mapping
        $importLog->update([
            'column_mapping' => $cleanMapping,
            'category_ids'   => array_map('intval', $categoryIds),
            'import_options' => ['duplicate_strategy' => $dupStrategy],
            'status'         => 'mapping',
        ]);

        // Stocker le mapping en session pour la prévisualisation
        session(["import_{$importLogId}_mapping" => $cleanMapping]);

        return redirect()->route('contacts.import.preview', $importLogId);
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 3 : Prévisualisation (GET)
    // ─────────────────────────────────────────────

    public function showPreview(int $importLogId): View|RedirectResponse
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        $analysis = session("import_{$importLogId}_analysis");
        $mapping  = $importLog->column_mapping ?? [];

        if (!$analysis || empty($mapping)) {
            return redirect()->route('contacts.import.mapping', $importLogId)
                ->with('error', 'Veuillez d\'abord configurer le mapping.');
        }

        // Appliquer le mapping sur les 10 premières lignes
        $previewRaw = $analysis['preview'] ?? [];
        $previewMapped = [];
        $validCount  = 0;
        $errorCount  = 0;
        $errorRows   = [];

        // Emails vus (pour détection doublons internes dans l'aperçu)
        $seenEmails = [];

        foreach ($previewRaw as $rowIndex => $row) {
            $mapped = [];
            foreach ($mapping as $fileCol => $dbField) {
                $mapped[$dbField] = $row[$fileCol] ?? '';
            }

            $email = strtolower(trim($mapped['email'] ?? ''));
            $hasError = false;
            $errorMsg = '';

            if (!$email) {
                $hasError = true;
                $errorMsg = 'Email manquant';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $hasError = true;
                $errorMsg = "Email invalide : {$email}";
            } elseif (isset($seenEmails[$email])) {
                $hasError = true;
                $errorMsg = 'Doublon dans le fichier';
            }

            if ($hasError) {
                $errorCount++;
                $errorRows[] = ['row' => $rowIndex + 2, 'error' => $errorMsg, 'data' => $mapped];
            } else {
                $validCount++;
                $seenEmails[$email] = true;
            }

            $previewMapped[] = [
                'data'     => $mapped,
                'has_error' => $hasError,
                'error_msg' => $errorMsg,
            ];
        }

        $mappedFields = array_values($mapping);
        $allFields    = ContactColumnMapper::getMappableFields();
        $categories   = Category::whereIn('id', $importLog->category_ids ?? [])->get();

        return view('contacts.import.step3-preview', compact(
            'importLog', 'previewMapped', 'mappedFields', 'allFields',
            'validCount', 'errorCount', 'errorRows', 'categories'
        ));
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 4 : Lancer l'import (POST)
    // ─────────────────────────────────────────────

    public function executeImport(Request $request, int $importLogId): RedirectResponse
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        if (!$importLog->column_mapping || !in_array('email', $importLog->column_mapping, true)) {
            return redirect()->route('contacts.import.mapping', $importLogId)
                ->with('error', 'Mapping invalide.');
        }

        // Marquer comme en attente et envoyer le job
        $importLog->update(['status' => 'pending']);

        ProcessContactImport::dispatch($importLog->id)
            ->onQueue('default');

        // Nettoyer la session
        session()->forget(["import_{$importLogId}_analysis", "import_{$importLogId}_mapping"]);

        return redirect()->route('contacts.import.progress', $importLogId);
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 4 : Page de progression (GET)
    // ─────────────────────────────────────────────

    public function showProgress(int $importLogId): View
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        return view('contacts.import.step4-progress', compact('importLog'));
    }

    // ─────────────────────────────────────────────
    // API : Statut de l'import (polling JSON)
    // ─────────────────────────────────────────────

    public function importStatus(int $importLogId): JsonResponse
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        $status = $importLog->status;
        $isDone = in_array($status, ['done', 'failed'], true);

        $progressPercent = 0;
        if ($isDone && $importLog->total_rows > 0) {
            $progressPercent = 100;
        } elseif ($status === 'processing' && $importLog->total_rows > 0) {
            // Estimation basée sur les importés actuels
            $done = $importLog->imported + $importLog->errors + $importLog->duplicates;
            $progressPercent = min(95, (int) (($done / $importLog->total_rows) * 100));
        }

        return response()->json([
            'status'          => $status,
            'is_done'         => $isDone,
            'progress'        => $progressPercent,
            'imported'        => $importLog->imported,
            'duplicates'      => $importLog->duplicates,
            'errors'          => $importLog->errors,
            'total_rows'      => $importLog->total_rows,
            'result_url'      => $isDone
                ? route('contacts.import.result', $importLogId)
                : null,
        ]);
    }

    // ─────────────────────────────────────────────
    // ÉTAPE 5 : Résultat final (GET)
    // ─────────────────────────────────────────────

    public function showResult(int $importLogId): View
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        $errorDetails = $importLog->error_details ?? [];
        $categories   = Category::whereIn('id', $importLog->category_ids ?? [])->get();

        return view('contacts.import.step5-result', compact(
            'importLog', 'errorDetails', 'categories'
        ));
    }

    // ─────────────────────────────────────────────
    // Télécharger template CSV
    // ─────────────────────────────────────────────

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $fields = ContactColumnMapper::getMappableFields();
        $headers = array_keys($fields);
        $example1 = ['ahmed@example.com', 'Ben Ali', 'Ahmed', 'CAEI Group', 'Directeur', '+216 71 234 567', '+216 20 123 456', 'Tunisie', 'Tunis', 'Finance', 'LinkedIn', ''];
        $example2 = ['sara@company.ma', 'Benali', 'Sara', 'Company SA', 'Manager RH', '+212 661 234 567', '', 'Maroc', 'Casablanca', 'Ressources Humaines', 'Référence', ''];

        // Réordonner les colonnes : email en premier
        $orderedHeaders = ['email', 'nom', 'prenom', 'entreprise', 'fonction', 'telephone', 'whatsapp', 'pays', 'ville', 'secteur_activite', 'source', 'notes'];
        $orderedEx1     = ['ahmed@example.com', 'Ben Ali', 'Ahmed', 'CAEI Group', 'Directeur', '+216 71 234 567', '+216 20 123 456', 'Tunisie', 'Tunis', 'Finance', 'LinkedIn', ''];
        $orderedEx2     = ['sara@company.ma', 'Benali', 'Sara', 'Company SA', 'Manager RH', '+212 661 234 567', '', 'Maroc', 'Casablanca', 'Ressources Humaines', 'Référence', ''];

        return response()->streamDownload(function () use ($orderedHeaders, $orderedEx1, $orderedEx2) {
            $output = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, $orderedHeaders, ';');
            fputcsv($output, $orderedEx1, ';');
            fputcsv($output, $orderedEx2, ';');
            fclose($output);
        }, 'template_contacts_caei.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─────────────────────────────────────────────
    // Télécharger les erreurs en CSV
    // ─────────────────────────────────────────────

    public function downloadErrors(int $importLogId): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $importLog = ImportLog::findOrFail($importLogId);
        $this->authorizeImport($importLog);

        $errors = $importLog->error_details ?? [];

        return response()->streamDownload(function () use ($errors) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['Ligne', 'Erreur'], ';');
            foreach ($errors as $err) {
                fputcsv($output, [$err['row'] ?? '?', $err['error'] ?? ''], ';');
            }
            fclose($output);
        }, "erreurs_import_{$importLogId}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function authorizeImport(ImportLog $importLog): void
    {
        if ($importLog->user_id && $importLog->user_id !== Auth::id()) {
            abort(403);
        }
    }
}


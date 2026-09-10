<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Storage;

/**
 * Lit les fichiers CSV/XLSX uploadés et extrait :
 * - les colonnes (première ligne)
 * - un aperçu des premières lignes
 * - le nombre approximatif de lignes
 */
class ImportFileReader
{
    /**
     * Lit les colonnes + aperçu d'un fichier stocké sur le disque.
     *
     * @param  string  $storagePath  Chemin relatif dans storage/app/public
     * @param  int     $previewRows  Nombre de lignes d'aperçu
     * @return array{headers: string[], preview: array[], total_estimate: int}
     */
    public static function analyze(string $storagePath, int $previewRows = 10): array
    {
        $fullPath = Storage::disk('public')->path($storagePath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (in_array($extension, ['csv', 'txt'])) {
            return self::analyzeCsv($fullPath, $previewRows);
        }

        return self::analyzeExcel($fullPath, $storagePath, $previewRows);
    }

    /**
     * Analyse un fichier CSV avec détection automatique du séparateur et encodage.
     */
    private static function analyzeCsv(string $path, int $previewRows): array
    {
        // Lire quelques octets pour détecter l'encodage
        $sample = file_get_contents($path, false, null, 0, 4096);

        // Détecter et convertir l'encodage vers UTF-8
        $encoding = mb_detect_encoding($sample, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'UTF-16'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            // Convertir le fichier entier
            $content = mb_convert_encoding(file_get_contents($path), 'UTF-8', $encoding);
        } else {
            // Retirer le BOM UTF-8 si présent
            $content = ltrim(file_get_contents($path), "\xEF\xBB\xBF");
        }

        // Détecter le séparateur (,  ;  \t  |)
        $separator = self::detectCsvSeparator($content);

        // Parser le CSV
        $lines = str_getcsv($content, "\n");
        $allRows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $allRows[] = str_getcsv($line, $separator);
        }

        if (empty($allRows)) {
            return ['headers' => [], 'preview' => [], 'total_estimate' => 0];
        }

        $headers = array_map('trim', $allRows[0]);
        $dataRows = array_slice($allRows, 1);
        $totalEstimate = count($dataRows);

        $preview = [];
        foreach (array_slice($dataRows, 0, $previewRows) as $row) {
            $assoc = [];
            foreach ($headers as $i => $header) {
                $assoc[$header] = $row[$i] ?? '';
            }
            $preview[] = $assoc;
        }

        return [
            'headers'        => $headers,
            'preview'        => $preview,
            'total_estimate' => $totalEstimate,
            'separator'      => $separator,
        ];
    }

    /**
     * Analyse un fichier Excel (.xlsx, .xls) via PhpSpreadsheet.
     */
    private static function analyzeExcel(string $fullPath, string $storagePath, int $previewRows): array
    {
        if (!file_exists($fullPath)) {
            $fullPath = Storage::disk('public')->path($storagePath);
        }

        if (!file_exists($fullPath)) {
            return ['headers' => [], 'preview' => [], 'total_estimate' => 0];
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fullPath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($fullPath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = (int) $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        if ($highestRow < 1) {
            return ['headers' => [], 'preview' => [], 'total_estimate' => 0];
        }

        $limitRow = min($previewRows + 1, $highestRow);
        $rows = $sheet->rangeToArray("A1:{$highestColumn}{$limitRow}", null, true, true, false);

        if (empty($rows)) {
            return ['headers' => [], 'preview' => [], 'total_estimate' => 0];
        }

        $headers = array_filter(array_map('trim', array_map('strval', $rows[0] ?? [])), fn($h) => $h !== '');
        $headers = array_values($headers);

        $dataRows = array_slice($rows, 1);

        $preview = [];
        foreach ($dataRows as $row) {
            $assoc = [];
            foreach ($headers as $i => $header) {
                $assoc[$header] = isset($row[$i]) ? trim((string) $row[$i]) : '';
            }
            $preview[] = $assoc;
        }

        return [
            'headers'        => $headers,
            'preview'        => $preview,
            'total_estimate' => max(0, $highestRow - 1),
        ];
    }

    /**
     * Détecte le séparateur d'un contenu CSV en analysant la première ligne.
     */
    private static function detectCsvSeparator(string $content): string
    {
        $firstLine = strtok($content, "\n");
        $separators = [';', ',', "\t", '|'];
        $counts = [];

        foreach ($separators as $sep) {
            $counts[$sep] = substr_count($firstLine, $sep);
        }

        arsort($counts);
        return array_key_first($counts) ?: ',';
    }
}

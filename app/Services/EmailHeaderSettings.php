<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EmailHeaderSettings
{
    private const SETTINGS_FILE = 'settings/email_header.json';

    /**
     * Valeurs par défaut officielles de CAEI.
     */
    public static function defaults(): array
    {
        return [
            'company_name'     => 'CAEI COMPANY GROUP',
            'siege_social'     => 'SIS 8 Rue Claude Bernard 1002 Belvédère-Tunis Tunisie',
            'telephone'        => '+216 58 332 143',
            'email'            => 'Training@caei-afri.com',
            'site_web'         => 'www.caei-afri.com',
            'matricule_fiscal' => '1821205 MAM 000',
            'logo_path'        => null,
            'show_header'      => true,
            'footer_logo_path' => null,
            'show_footer_logo' => true,
        ];
    }

    /**
     * Récupère la configuration actuelle fusionnée avec les valeurs par défaut.
     */
    public static function get(): array
    {
        $defaults = self::defaults();
        $stored = [];

        $fullPath = storage_path('app/' . self::SETTINGS_FILE);
        if (file_exists($fullPath)) {
            $json = file_get_contents($fullPath);
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $stored = $decoded;
            }
        }

        $settings = array_merge($defaults, $stored);

        // Résoudre l'URL du logo en-tête
        $settings['logo_url'] = self::resolveLogoUrl($settings['logo_path'] ?? null);

        // Résoudre l'URL du logo pied de page (footer)
        $settings['footer_logo_url'] = self::resolveFooterLogoUrl($settings['footer_logo_path'] ?? null);

        return $settings;
    }

    /**
     * Enregistre les paramètres et gère l'upload éventuel des logos.
     */
    public static function save(array $data, ?UploadedFile $logoFile = null, ?UploadedFile $footerLogoFile = null): array
    {
        $current = self::get();

        $updated = [
            'company_name'     => trim((string) ($data['company_name'] ?? $current['company_name'])),
            'siege_social'     => trim((string) ($data['siege_social'] ?? $current['siege_social'])),
            'telephone'        => trim((string) ($data['telephone'] ?? $current['telephone'])),
            'email'            => trim((string) ($data['email'] ?? $current['email'])),
            'site_web'         => trim((string) ($data['site_web'] ?? $current['site_web'])),
            'matricule_fiscal' => trim((string) ($data['matricule_fiscal'] ?? $current['matricule_fiscal'])),
            'show_header'      => isset($data['show_header']) ? (bool) $data['show_header'] : true,
            'show_footer_logo' => isset($data['show_footer_logo']) ? (bool) $data['show_footer_logo'] : true,
            'logo_path'        => $current['logo_path'] ?? null,
            'footer_logo_path' => $current['footer_logo_path'] ?? null,
        ];

        // Traiter l'upload d'un nouveau logo en-tête
        if ($logoFile && $logoFile->isValid()) {
            if (!empty($current['logo_path']) && Storage::disk('public')->exists($current['logo_path'])) {
                Storage::disk('public')->delete($current['logo_path']);
            }

            $extension = $logoFile->getClientOriginalExtension() ?: 'png';
            $filename = 'email-logo-' . time() . '.' . $extension;
            $savedPath = $logoFile->storeAs('settings', $filename, 'public');

            $updated['logo_path'] = $savedPath;
        }

        // Traiter l'upload d'un nouveau logo pied de page (footer)
        if ($footerLogoFile && $footerLogoFile->isValid()) {
            if (!empty($current['footer_logo_path']) && Storage::disk('public')->exists($current['footer_logo_path'])) {
                Storage::disk('public')->delete($current['footer_logo_path']);
            }

            $extension = $footerLogoFile->getClientOriginalExtension() ?: 'png';
            $filename = 'email-footer-logo-' . time() . '.' . $extension;
            $savedPath = $footerLogoFile->storeAs('settings', $filename, 'public');

            $updated['footer_logo_path'] = $savedPath;
        }

        // Sauvegarder dans storage/app/settings/email_header.json
        $dir = storage_path('app/settings');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(storage_path('app/' . self::SETTINGS_FILE), json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $updated['logo_url'] = self::resolveLogoUrl($updated['logo_path']);
        $updated['footer_logo_url'] = self::resolveFooterLogoUrl($updated['footer_logo_path']);

        return $updated;
    }

    /**
     * Réinitialise la configuration et supprime les logos personnalisés.
     */
    public static function reset(): array
    {
        $current = self::get();
        if (!empty($current['logo_path']) && Storage::disk('public')->exists($current['logo_path'])) {
            Storage::disk('public')->delete($current['logo_path']);
        }
        if (!empty($current['footer_logo_path']) && Storage::disk('public')->exists($current['footer_logo_path'])) {
            Storage::disk('public')->delete($current['footer_logo_path']);
        }

        $fullPath = storage_path('app/' . self::SETTINGS_FILE);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return self::get();
    }

    /**
     * Détermine l'URL publique du logo d'en-tête.
     */
    private static function resolveLogoUrl(?string $logoPath): string
    {
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return asset('storage/' . $logoPath);
        }

        if (file_exists(public_path('images/logo-caei-header.png'))) {
            return asset('images/logo-caei-header.png');
        }

        if (file_exists(public_path('images/logo-caei.jpg'))) {
            return asset('images/logo-caei.jpg');
        }

        return asset('images/logo-caei.png');
    }

    /**
     * Détermine l'URL publique du logo de pied de page (footer).
     */
    private static function resolveFooterLogoUrl(?string $footerLogoPath): string
    {
        if ($footerLogoPath && Storage::disk('public')->exists($footerLogoPath)) {
            return asset('storage/' . $footerLogoPath);
        }

        if (file_exists(public_path('images/logo-caei-footer.png'))) {
            return asset('images/logo-caei-footer.png');
        }

        if (file_exists(public_path('images/logo-caei-footer.jpg'))) {
            return asset('images/logo-caei-footer.jpg');
        }

        return asset('images/logo-caei-header.png');
    }
}


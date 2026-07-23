<?php

namespace App\Models;

use App\Http\Controllers\CampaignController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    public const TYPES = [
        'invitation' => 'Invitation',
        'newsletter' => 'Newsletter',
        'confirmation' => 'Confirmation',
        'remerciement' => 'Remerciement',
        'relance' => 'Relance',
    ];

    protected $fillable = ['nom', 'sujet', 'type', 'contenu', 'gjs_data', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'gjs_data' => 'array',
        ];
    }

    public static function availableVariables(): array
    {
        return [
            '{{nom}}' => 'Nom du contact',
            '{{prenom}}' => 'Prenom du contact',
            '{{entreprise}}' => 'Entreprise du contact',
            '{{fonction}}' => 'Fonction du contact',
            '{{pays}}' => 'Pays du contact',
            '{{nom_seminaire}}' => 'Nom de la campagne ou du seminaire',
            '{{date}}' => 'Date de la campagne',
            '{{lien}}' => 'Lien utile ou lien de participation',
        ];
    }

    public static function previewValues(): array
    {
        return [
            'nom' => 'Ben Ali',
            'prenom' => 'Amel',
            'entreprise' => 'CAEI Partner',
            'fonction' => 'Directrice Formation',
            'pays' => 'Tunisie',
            'nom_seminaire' => 'Seminaire Audit & Gouvernance',
            'date' => now()->addWeek()->format('d/m/Y'),
            'lien' => config('app.url'),
        ];
    }

    public static function parseBlocks(?string $content): array
    {
        $content = trim((string) $content);

        if ($content === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            if (isset($decoded['blocks']) && is_array($decoded['blocks'])) {
                return $decoded['blocks'];
            }

            if (array_is_list($decoded)) {
                return $decoded;
            }
        }

        return [[
            'type' => 'text',
            'content' => $content,
            'align' => 'left',
        ]];
    }

    public static function renderBlocks(array $blocks, ?Contact $contact = null, array $extraVariables = []): string
    {
        $html = '';

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $type = $block['type'] ?? 'text';

            switch ($type) {
                case 'text':
                    $content = CampaignController::personnaliser((string) ($block['content'] ?? ''), $contact, $extraVariables);
                    $html .= '<div style="margin:0 0 16px 0; text-align:' . e($block['align'] ?? 'left') . ';"><p style="margin:0; line-height:1.7; color:#0f172a;">' . nl2br(e($content)) . '</p></div>';
                    break;

                case 'image':
                case 'logo':
                    $src = (string) ($block['src'] ?? '/storage/logo-caei.png');
                    $alt = (string) ($block['alt'] ?? 'Logo CAEI');
                    $width = (string) ($block['width'] ?? 220);
                    $html .= '<div style="margin:0 0 16px 0;"><img src="' . e($src) . '" alt="' . e($alt) . '" width="' . e($width) . '" style="max-width:100%; height:auto; display:block;" /></div>';
                    break;

                case 'button':
                    $label = CampaignController::personnaliser((string) ($block['label'] ?? 'Bouton'), $contact, $extraVariables);
                    $url = CampaignController::personnaliser((string) ($block['url'] ?? '#'), $contact, $extraVariables);
                    $color = (string) ($block['color'] ?? '#2563eb');
                    $html .= '<div style="margin:0 0 16px 0;"><a href="' . e($url) . '" style="display:inline-block;background:' . e($color) . ';color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:999px;font-weight:600;">' . e($label) . '</a></div>';
                    break;

                case 'link':
                    $label = CampaignController::personnaliser((string) ($block['label'] ?? 'En savoir plus'), $contact, $extraVariables);
                    $url = CampaignController::personnaliser((string) ($block['url'] ?? '#'), $contact, $extraVariables);
                    $html .= '<div style="margin:0 0 16px 0;"><a href="' . e($url) . '" style="color:#2563eb; text-decoration:underline;">' . e($label) . '</a></div>';
                    break;

                case 'signature':
                    $content = CampaignController::personnaliser((string) ($block['content'] ?? ''), $contact, $extraVariables);
                    $html .= '<div style="margin:0 0 16px 0; font-style:italic; color:#334155;">' . nl2br(e($content)) . '</div>';
                    break;

                case 'attachment':
                    $label = CampaignController::personnaliser((string) ($block['label'] ?? 'Pièce jointe'), $contact, $extraVariables);
                    $url = CampaignController::personnaliser((string) ($block['url'] ?? '#attachments'), $contact, $extraVariables);
                    $html .= '<div style="margin:0 0 16px 0;"><a href="' . e($url) . '" style="color:#2563eb; text-decoration:underline;">' . e($label) . '</a></div>';
                    break;
            }
        }

        return $html;
    }

    public static function renderContent(string $content, ?Contact $contact = null, array $extraVariables = []): string
    {
        $trimmed = trim($content);

        if ($trimmed === '') {
            return '';
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $blocks = $decoded['blocks'] ?? $decoded;
            if (is_array($blocks) && $blocks !== []) {
                return self::renderBlocks($blocks, $contact, $extraVariables);
            }
        }

        if (preg_match('/^<\s*[^>]+>/', $trimmed) || str_contains($trimmed, '<p') || str_contains($trimmed, '<div') || str_contains($trimmed, '<table')) {
            return self::sanitizeContent(CampaignController::personnaliser($trimmed, $contact, $extraVariables));
        }

        return '<div style="font-family:Arial, sans-serif; line-height:1.7; color:#0f172a;">' . nl2br(e(CampaignController::personnaliser($trimmed, $contact, $extraVariables))) . '</div>';
    }

    public static function sanitizeContent(string $content): string
    {
        $content = preg_replace('#<(script|iframe|object|embed|form|input|button|style)[^>]*>.*?</\1>#is', '', $content);
        $content = preg_replace('#</?(script|iframe|object|embed|form|input|button|style)[^>]*>#is', '', $content);
        $content = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content);
        $content = preg_replace('/\s+(href|src)\s*=\s*([\'"])\s*javascript:[^\'"]*\2/i', '', $content);

        return trim($content ?? '');
    }

    public static function hasValidContent(?string $content): bool
    {
        $content = trim((string) $content);

        if ($content === '') {
            return false;
        }

        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $blocks = $decoded['blocks'] ?? $decoded;
            if (is_array($blocks) && $blocks !== []) {
                return true;
            }
        }

        $content = trim(strip_tags(self::sanitizeContent($content)));

        return $content !== '';
    }

    public function renderPreview(): array
    {
        return [
            'sujet' => CampaignController::personnaliser($this->sujet ?: $this->nom, null, self::previewValues()),
            'contenu' => self::renderContent($this->contenu, null, self::previewValues()),
        ];
    }

    public static function defaultTemplates(): array
    {
        return [
            [
                'nom' => 'Invitation seminaire',
                'sujet' => 'Invitation au seminaire CAEI',
                'type' => 'invitation',
                'contenu' => "Bonjour {{prenom}} {{nom}},\n\nNous avons le plaisir de vous inviter au prochain seminaire CAEI : {{nom_seminaire}}, prevu le {{date}}.\n\nVotre experience au sein de {{entreprise}} serait particulierement precieuse pour enrichir les echanges.\n\nLien utile : {{lien}}\n\nCordialement,\nL'equipe CAEI",
            ],
            [
                'nom' => 'Relance commerciale',
                'sujet' => 'Suite a notre echange - accompagnement CAEI',
                'type' => 'relance',
                'contenu' => "Bonjour {{prenom}} {{nom}},\n\nJe me permets de revenir vers vous concernant nos solutions d'accompagnement en audit, conseil et formation.\n\nNous serions ravis d'echanger avec vous sur les besoins de {{entreprise}} et les pistes de collaboration possibles.\n\nCordialement,\nL'equipe CAEI",
            ],
            [
                'nom' => 'Confirmation inscription',
                'sujet' => 'Confirmation de votre inscription',
                'type' => 'confirmation',
                'contenu' => "Bonjour {{prenom}} {{nom}},\n\nNous vous confirmons votre inscription a {{nom_seminaire}} prevu le {{date}}.\n\nVous recevrez prochainement les informations pratiques relatives au programme, aux horaires et aux modalites de participation.\n\nCordialement,\nL'equipe CAEI",
            ],
            [
                'nom' => 'Remerciement',
                'sujet' => 'Merci pour votre participation',
                'type' => 'remerciement',
                'contenu' => "Bonjour {{prenom}} {{nom}},\n\nNous vous remercions chaleureusement pour votre participation a {{nom_seminaire}}.\n\nNous restons a votre disposition pour toute information complementaire ou pour poursuivre nos echanges.\n\nCordialement,\nL'equipe CAEI",
            ],
            [
                'nom' => 'Newsletter',
                'sujet' => 'Actualites CAEI - audit, conseil et formation',
                'type' => 'newsletter',
                'contenu' => "Bonjour {{prenom}} {{nom}},\n\nRetrouvez les dernieres actualites CAEI : formations a venir, evenements, publications et ressources utiles pour les professionnels de l'audit, du controle et de la gouvernance.\n\nEn savoir plus : {{lien}}\n\nBonne lecture,\nL'equipe CAEI",
            ],
        ];
    }

    public static function installDefaults(): int
    {
        $created = 0;

        foreach (self::defaultTemplates() as $template) {
            $model = self::firstOrCreate(
                ['nom' => $template['nom']],
                [
                    'sujet' => $template['sujet'],
                    'type' => $template['type'],
                    'contenu' => $template['contenu'],
                    'is_active' => true,
                ]
            );

            if ($model->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? Str::headline((string) $this->type);
    }
}

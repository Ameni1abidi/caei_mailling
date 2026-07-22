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

    protected $fillable = ['nom', 'sujet', 'type', 'contenu', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
        $content = trim(strip_tags(self::sanitizeContent((string) $content)));

        return $content !== '';
    }

    public function renderPreview(): array
    {
        return [
            'sujet' => CampaignController::personnaliser($this->sujet ?: $this->nom, null, self::previewValues()),
            'contenu' => CampaignController::personnaliser($this->contenu, null, self::previewValues()),
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

<?php

namespace App\Services;

/**
 * Service de mapping intelligent des colonnes CSV/Excel → champs contacts CAEI.
 * Reconnaît automatiquement les variantes de noms de colonnes
 * provenant de LinkedIn, Outlook, Excel, CRM, etc.
 */
class ContactColumnMapper
{
    /**
     * Champs importables de la table contacts (sans champs système).
     */
    public static function getMappableFields(): array
    {
        return [
            'email'            => 'Email *',
            'nom'              => 'Nom',
            'prenom'           => 'Prénom',
            'entreprise'       => 'Entreprise',
            'fonction'         => 'Fonction / Poste',
            'telephone'        => 'Téléphone',
            'whatsapp'         => 'WhatsApp',
            'pays'             => 'Pays',
            'ville'            => 'Ville',
            'secteur_activite' => "Secteur d'activité",
            'source'           => 'Source',
            'notes'            => 'Notes',
        ];
    }

    /**
     * Dictionnaire de variantes connues pour chaque champ.
     * Clé = nom du champ DB, valeur = liste de variantes (minuscules, sans accents).
     */
    public static function getVariants(): array
    {
        return [
            'email' => [
                'email', 'e-mail', 'e mail', 'mail', 'courriel',
                'email address', 'adresse email', 'adresse e-mail',
                'adresse mail', 'email professionnel', 'professional email',
                'work email', 'business email',
            ],
            'nom' => [
                'nom', 'last name', 'lastname', 'name', 'surname',
                'family name', 'nom de famille', 'last_name',
            ],
            'prenom' => [
                'prenom', 'prénom', 'first name', 'firstname', 'given name',
                'first_name', 'givenname', 'forename', 'prénom',
            ],
            'entreprise' => [
                'entreprise', 'societe', 'société', 'company', 'company name',
                'organization', 'organisation', 'firm', 'employer',
                'account name', 'nom entreprise', 'nom société',
            ],
            'fonction' => [
                'fonction', 'poste', 'job title', 'jobtitle', 'title',
                'position', 'role', 'profession', 'occupation',
                'intitulé du poste', 'intitule poste',
            ],
            'telephone' => [
                'telephone', 'téléphone', 'phone', 'phone number',
                'phonenumber', 'mobile', 'gsm', 'tel', 'tel.',
                'numero telephone', 'numéro téléphone', 'cell',
                'cellphone', 'numero de telephone',
            ],
            'whatsapp' => [
                'whatsapp', 'whatsapp number', 'whatsapp no',
                'numero whatsapp', 'numéro whatsapp',
            ],
            'pays' => [
                'pays', 'country', 'country name', 'countryname',
                'nation', 'pays origine',
            ],
            'ville' => [
                'ville', 'city', 'town', 'locality', 'localite',
                'localité', 'commune',
            ],
            'secteur_activite' => [
                'secteur_activite', 'secteur activite', "secteur d'activité",
                'secteur', 'industry', 'business sector', 'activity sector',
                'secteur activité', 'domaine activite', "domaine d'activité",
                'business', 'sector',
            ],
            'source' => [
                'source', 'origine', 'lead source', 'contact source',
                'leadsource', 'provenance',
            ],
            'notes' => [
                'notes', 'note', 'comments', 'comment', 'commentaires',
                'commentaire', 'remarques', 'remarque', 'observations',
                'description',
            ],
        ];
    }

    /**
     * Normalise un nom de colonne : retire accents, met en minuscule, retire ponctuation superflue.
     */
    public static function normalize(string $value): string
    {
        // Convertir en minuscule
        $value = mb_strtolower(trim($value));

        // Translittérer les accents (é→e, è→e, ê→e, à→a, etc.)
        $trans = [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ç' => 'c', 'ñ' => 'n',
        ];
        $value = strtr($value, $trans);

        // Retirer les caractères spéciaux sauf lettres, chiffres, espaces et tirets
        $value = preg_replace('/[^a-z0-9\s\-_]/', '', $value);

        // Normaliser les espaces multiples
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Tente de détecter automatiquement le champ DB correspondant à un nom de colonne.
     * Retourne le nom du champ DB ou null si non détecté.
     */
    public static function detectField(string $columnName): ?string
    {
        $normalized = self::normalize($columnName);

        foreach (self::getVariants() as $field => $variants) {
            foreach ($variants as $variant) {
                if (self::normalize($variant) === $normalized) {
                    return $field;
                }
            }
        }

        // Deuxième passe : correspondance partielle (contains)
        foreach (self::getVariants() as $field => $variants) {
            foreach ($variants as $variant) {
                $normVariant = self::normalize($variant);
                if (
                    str_contains($normalized, $normVariant) ||
                    str_contains($normVariant, $normalized)
                ) {
                    return $field;
                }
            }
        }

        return null;
    }

    /**
     * Génère un mapping automatique pour un tableau de colonnes du fichier.
     * Retourne: ['colonne_fichier' => 'champ_db_ou_null']
     */
    public static function autoMap(array $fileColumns): array
    {
        $mapping = [];
        $usedFields = [];

        foreach ($fileColumns as $col) {
            $detected = self::detectField($col);

            // Éviter d'assigner le même champ DB deux fois
            if ($detected && in_array($detected, $usedFields, true)) {
                $detected = null;
            }

            $mapping[$col] = $detected;

            if ($detected) {
                $usedFields[] = $detected;
            }
        }

        return $mapping;
    }

    /**
     * Nettoie et normalise une valeur avant insertion en base.
     */
    public static function sanitizeValue(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        // Protection contre CSV injection
        if (in_array($value[0] ?? '', ['=', '+', '-', '@', "\t", "\r"], true)) {
            $value = "'" . $value;
        }

        return match ($field) {
            'email'  => strtolower($value),
            'pays', 'ville' => mb_convert_case($value, MB_CASE_TITLE),
            default  => $value,
        };
    }
}

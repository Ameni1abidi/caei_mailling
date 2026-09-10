<?php

namespace App\Http\Controllers;

use App\Services\EmailHeaderSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailHeaderSettingController extends Controller
{
    /**
     * Affiche la page de configuration de l'en-tête d'email et du logo.
     */
    public function edit(): View
    {
        $settings = EmailHeaderSettings::get();

        return view('settings.email-header', compact('settings'));
    }

    /**
     * Enregistre les modifications apportées à l'en-tête d'email et au logo.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name'     => ['nullable', 'string', 'max:255'],
            'siege_social'     => ['nullable', 'string', 'max:500'],
            'telephone'        => ['nullable', 'string', 'max:100'],
            'email'            => ['nullable', 'email', 'max:255'],
            'site_web'         => ['nullable', 'string', 'max:255'],
            'matricule_fiscal' => ['nullable', 'string', 'max:100'],
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp,gif', 'max:5120'],
            'show_header'      => ['nullable', 'boolean'],
        ], [
            'email.email' => "L'adresse e-mail saisie n'est pas valide.",
            'logo.image'  => 'Le fichier téléversé doit être une image.',
            'logo.mimes'  => 'Formats acceptés pour le logo : PNG, JPG, JPEG, SVG, WEBP.',
            'logo.max'    => 'Le logo ne doit pas dépasser 5 Mo.',
        ]);

        $data = [
            'company_name'     => $request->input('company_name'),
            'siege_social'     => $request->input('siege_social'),
            'telephone'        => $request->input('telephone'),
            'email'            => $request->input('email'),
            'site_web'         => $request->input('site_web'),
            'matricule_fiscal' => $request->input('matricule_fiscal'),
            'show_header'      => $request->boolean('show_header', true),
        ];

        EmailHeaderSettings::save($data, $request->file('logo'));

        return redirect()->route('settings.email-header')
            ->with('success', 'En-tête et logo de l\'email mis à jour avec succès !');
    }

    /**
     * Réinitialise l'en-tête et le logo aux valeurs par défaut de CAEI.
     */
    public function reset(): RedirectResponse
    {
        EmailHeaderSettings::reset();

        return redirect()->route('settings.email-header')
            ->with('success', 'L\'en-tête et le logo ont été réinitialisés aux valeurs d\'origine de CAEI.');
    }
}


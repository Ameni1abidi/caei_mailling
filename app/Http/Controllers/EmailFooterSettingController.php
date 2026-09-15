<?php

namespace App\Http\Controllers;

use App\Services\EmailHeaderSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailFooterSettingController extends Controller
{
    /**
     * Affiche la page de configuration du pied de page (footer) d'email.
     */
    public function edit(): View
    {
        $settings = EmailHeaderSettings::get();

        return view('settings.email-footer', compact('settings'));
    }

    /**
     * Enregistre les modifications apportées au pied de page d'email.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'footer_title'            => ['nullable', 'string', 'max:255'],
            'footer_subtitle'         => ['nullable', 'string', 'max:500'],
            'footer_disclaimer'       => ['nullable', 'string', 'max:1000'],
            'footer_unsubscribe_text' => ['nullable', 'string', 'max:255'],
            'footer_extra_text'       => ['nullable', 'string', 'max:1000'],
            'footer_logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp,gif', 'max:5120'],
            'show_footer'             => ['nullable', 'boolean'],
            'show_footer_logo'        => ['nullable', 'boolean'],
        ], [
            'footer_logo.image' => 'Le fichier téléversé pour le logo de pied de page doit être une image.',
            'footer_logo.mimes' => 'Formats acceptés pour le logo : PNG, JPG, JPEG, SVG, WEBP.',
            'footer_logo.max'   => 'Le logo de pied de page ne doit pas dépasser 5 Mo.',
        ]);

        $data = [
            'footer_title'            => $request->input('footer_title'),
            'footer_subtitle'         => $request->input('footer_subtitle'),
            'footer_disclaimer'       => $request->input('footer_disclaimer'),
            'footer_unsubscribe_text' => $request->input('footer_unsubscribe_text'),
            'footer_extra_text'       => $request->input('footer_extra_text'),
            'show_footer'             => $request->boolean('show_footer', true),
            'show_footer_logo'        => $request->boolean('show_footer_logo', true),
        ];

        EmailHeaderSettings::save($data, null, $request->file('footer_logo'));

        return redirect()->route('settings.email-footer')
            ->with('success', 'Pied de page des emails mis à jour avec succès !');
    }

    /**
     * Réinitialise le pied de page aux valeurs par défaut de CAEI.
     */
    public function reset(): RedirectResponse
    {
        EmailHeaderSettings::resetFooter();

        return redirect()->route('settings.email-footer')
            ->with('success', 'Le pied de page a été réinitialisé aux valeurs d\'origine de CAEI.');
    }
}

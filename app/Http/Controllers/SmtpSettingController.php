<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use App\Http\Requests\StoreSmtpSettingRequest;
use App\Http\Requests\UpdateSmtpSettingRequest;
use Illuminate\Http\Request;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class SmtpSettingController extends Controller
{
    public function index()
    {
        $smtpSettings = SmtpSetting::latest()->get();

        return view('smtp-settings.index', compact('smtpSettings'));
    }

    public function create()
    {
        return view('smtp-settings.create');
    }

    public function store(StoreSmtpSettingRequest $request)
    {
        $data = $request->validated();

        // Valeur par défaut pour rate_limit
        $data['rate_limit'] = $data['rate_limit'] ?? 100;

        SmtpSetting::create($data);

        return redirect()->route('smtp-settings.index')
            ->with('success', 'Configuration SMTP créée avec succès.');
    }

    public function edit(SmtpSetting $smtpSetting)
    {
        return view('smtp-settings.edit', compact('smtpSetting'));
    }

    public function update(UpdateSmtpSettingRequest $request, SmtpSetting $smtpSetting)
    {
        $data = $request->validated();

        // Conserver le mot de passe existant s'il n'est pas fourni
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Conserver la clé API existante si non fournie
        if (empty($data['api_key'])) {
            unset($data['api_key']);
        }

        $data['rate_limit'] = $data['rate_limit'] ?? $smtpSetting->rate_limit;

        $smtpSetting->update($data);

        return redirect()->route('smtp-settings.index')
            ->with('success', 'Configuration SMTP mise à jour avec succès.');
    }

    public function destroy(SmtpSetting $smtpSetting)
    {
        if ($smtpSetting->is_active) {
            return back()->with('error', 'Impossible de supprimer la configuration active. Activez une autre configuration d\'abord.');
        }

        $smtpSetting->delete();

        return redirect()->route('smtp-settings.index')
            ->with('success', 'Configuration SMTP supprimée.');
    }

    /**
     * Active une configuration SMTP et désactive toutes les autres.
     */
    public function activate(SmtpSetting $smtpSetting)
    {
        // Désactiver toutes les configurations
        SmtpSetting::where('is_active', true)->update(['is_active' => false]);

        // Activer celle demandée
        $smtpSetting->update(['is_active' => true]);

        return back()->with('success', "Configuration « {$smtpSetting->provider} » activée.");
    }

    /**
     * Teste la connexion SMTP et retourne un résultat JSON.
     */
    public function testConnection(SmtpSetting $smtpSetting)
    {
        if ($smtpSetting->driver !== 'smtp') {
            return response()->json([
                'success' => false,
                'message' => 'Le test de connexion est disponible uniquement pour le driver SMTP.',
            ]);
        }

        try {
            $tls = $smtpSetting->encryption === 'tls';
            $transport = new EsmtpTransport(
                $smtpSetting->host,
                $smtpSetting->port,
                $tls,
            );

            if ($smtpSetting->username) {
                $transport->setUsername($smtpSetting->username);
            }
            if ($smtpSetting->password) {
                $transport->setPassword($smtpSetting->password);
            }

            $transport->start();
            $transport->stop();

            return response()->json([
                'success' => true,
                'message' => 'Connexion SMTP réussie ! Le serveur répond correctement.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Échec de la connexion : ' . $e->getMessage(),
            ]);
        }
    }
}

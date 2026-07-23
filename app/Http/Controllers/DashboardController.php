<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function index()
    {
        $campagnesEnvoyees = Campaign::where('statut', 'envoyee')->count();
        $campagnesProgrammees = Campaign::where('statut', 'en_cours')->count();

        $emailsEnvoyes = EmailLog::count();
        $emailsDelivres = EmailLog::where('status', 'sent')->count();
        $emailsOuverts = EmailLog::where('opened', true)->count();
        $emailsClics = EmailLog::where('clicked', true)->count();
        $emailsRejetes = EmailLog::where('status', 'failed')->count();

        $prospectStats = [
            'total' => Contact::count(),
            'nouveau' => Contact::where('status', Contact::STATUS_NOUVEAU)->count(),
            'envoye' => Contact::where('status', Contact::STATUS_EMAIL_ENVOYE)->count(),
            'ouvert' => Contact::where('status', Contact::STATUS_EMAIL_OUVERT)->count(),
            'interesse' => Contact::where('status', Contact::STATUS_INTERESSE)->count(),
            'relancer' => Contact::where('status', Contact::STATUS_A_RELANCER)->count(),
            'client' => Contact::where('status', Contact::STATUS_CLIENT)->count(),
        ];

        $campaignsWithStats = Campaign::withCount([
            'emailLogs as envoyes_count',
            'emailLogs as ouverts_count' => function($query) {
                $query->where('opened', true);
            },
            'emailLogs as clics_count' => function($query) {
                $query->where('clicked', true);
            },
            'emailLogs as erreurs_count' => function($query) {
                $query->where('status', 'failed');
            }
        ])->latest()->take(10)->get();

        return view('dashboard', compact(
            'campagnesEnvoyees',
            'campagnesProgrammees',
            'emailsEnvoyes',
            'emailsDelivres',
            'emailsOuverts',
            'emailsClics',
            'emailsRejetes',
            'prospectStats',
            'campaignsWithStats'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Email logs stats — 1 query instead of 6 ─────────────────────
        $emailLogStats = EmailLog::selectRaw(
            'status,
             COUNT(*) as cnt,
             COALESCE(SUM(opened), 0)  as opened_cnt,
             COALESCE(SUM(clicked), 0) as clicked_cnt'
        )
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $campagnesEnvoyees    = Campaign::where('statut', 'envoyee')->count();
        $campagnesProgrammees = Campaign::where('statut', 'en_cours')->count();

        $sentRow      = $emailLogStats->get(EmailLog::STATUS_SENT);
        $deliveredRow = $emailLogStats->get(EmailLog::STATUS_DELIVERED);
        $failedRow    = $emailLogStats->get(EmailLog::STATUS_FAILED);
        $bouncedRow   = $emailLogStats->get(EmailLog::STATUS_BOUNCED);
        $invalidRow   = $emailLogStats->get(EmailLog::STATUS_INVALID);

        $emailsEnvoyes  = (int) ($sentRow?->cnt ?? 0) + (int) ($deliveredRow?->cnt ?? 0);
        $emailsDelivres = (int) ($deliveredRow?->cnt ?? 0);
        $emailsOuverts  = (int) ($sentRow?->opened_cnt ?? 0) + (int) ($deliveredRow?->opened_cnt ?? 0);
        $emailsClics    = (int) ($sentRow?->clicked_cnt ?? 0) + (int) ($deliveredRow?->clicked_cnt ?? 0);
        $emailsRejetes  = (int) ($failedRow?->cnt ?? 0)
                        + (int) ($bouncedRow?->cnt ?? 0)
                        + (int) ($invalidRow?->cnt ?? 0);

        // ── Prospect stats — 1 query instead of 7 ──────────────────────
        $contactStatRows = Contact::selectRaw('prospect_status, COUNT(*) as cnt')
            ->groupBy('prospect_status')
            ->pluck('cnt', 'prospect_status');

        $prospectStats = [
            'total'     => $contactStatRows->sum(),
            'nouveau'   => (int) $contactStatRows->get(Contact::STATUS_NOUVEAU, 0),
            'envoye'    => (int) $contactStatRows->get(Contact::STATUS_EMAIL_ENVOYE, 0),
            'ouvert'    => (int) $contactStatRows->get(Contact::STATUS_EMAIL_OUVERT, 0),
            'interesse' => (int) $contactStatRows->get(Contact::STATUS_INTERESSE, 0),
            'relancer'  => (int) $contactStatRows->get(Contact::STATUS_A_RELANCER, 0),
            'client'    => (int) $contactStatRows->get(Contact::STATUS_CLIENT, 0),
        ];

        // ── Campaign stats table — still 1 query with withCount ─────────
        $campaignsWithStats = Campaign::withCount([
            'emailLogs as envoyes_count',
            'emailLogs as delivered_count' => function ($query) {
                $query->where('status', EmailLog::STATUS_DELIVERED);
            },
            'emailLogs as bounced_count' => function ($query) {
                $query->where('status', EmailLog::STATUS_BOUNCED);
            },
            'emailLogs as invalid_count' => function ($query) {
                $query->where('status', EmailLog::STATUS_INVALID);
            },
            'emailLogs as ouverts_count' => function ($query) {
                $query->where('opened', true);
            },
            'emailLogs as clics_count' => function ($query) {
                $query->where('clicked', true);
            },
            'emailLogs as erreurs_count' => function ($query) {
                $query->whereIn('status', [
                    EmailLog::STATUS_FAILED,
                    EmailLog::STATUS_BOUNCED,
                    EmailLog::STATUS_INVALID,
                ]);
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

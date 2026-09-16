<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $isAdmin = $user?->hasRole('admin');

        // ── Email logs stats — scoped to user's campaigns ───────────────
        $userCampaignQuery = Campaign::forUser($user);
        $userCampaignIds   = (clone $userCampaignQuery)->pluck('id');

        $emailLogStats = EmailLog::whereIn('campaign_id', $userCampaignIds)
            ->selectRaw(
                'status,
                 COUNT(*) as cnt,
                 COALESCE(SUM(opened), 0)  as opened_cnt,
                 COALESCE(SUM(clicked), 0) as clicked_cnt'
            )
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $campagnesEnvoyees    = (clone $userCampaignQuery)->where('statut', 'envoyee')->count();
        $campagnesProgrammees = (clone $userCampaignQuery)->where('statut', 'en_cours')->count();
        $campagnesCeMois      = (clone $userCampaignQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

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

        $tauxLivraison = $emailsEnvoyes > 0 ? round(($emailsDelivres / $emailsEnvoyes) * 100, 1) : 100.0;
        $tauxOuvertureGlobal = $emailsEnvoyes > 0 ? round(($emailsOuverts / $emailsEnvoyes) * 100, 1) : 0.0;

        // ── Contact stats ───────────────
        $totalContacts = Contact::count();
        $totalImportedLogsSum = Schema::hasTable('import_logs') ? (int) ImportLog::sum('imported') : $totalContacts;

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

        // ── Queue & Worker stats ───────────────────────
        $hasJobsTable       = Schema::hasTable('jobs');
        $hasFailedJobsTable = Schema::hasTable('failed_jobs');

        $jobsEnAttente = $hasJobsTable ? DB::table('jobs')->count() : 0;
        $jobsEchoues24h = $hasFailedJobsTable
            ? DB::table('failed_jobs')->where('failed_at', '>=', now()->subHours(24))->count()
            : 0;

        $jobsTraitesAujourdhui = EmailLog::whereIn('campaign_id', $userCampaignIds)
            ->whereDate('created_at', now()->today())
            ->count();

        // ── Chart 14 jours data ───────────────────────
        $chartLabels = [];
        $chartSentData = [];
        $chartErrorData = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $chartLabels[] = $date->translatedFormat('d M');

            $dayLogs = EmailLog::whereIn('campaign_id', $userCampaignIds)
                ->whereDate('created_at', $dateKey)
                ->selectRaw('
                    SUM(CASE WHEN status IN ("sent", "delivered") THEN 1 ELSE 0 END) as sent_cnt,
                    SUM(CASE WHEN status IN ("failed", "bounced", "invalid") THEN 1 ELSE 0 END) as err_cnt
                ')
                ->first();

            $chartSentData[]  = (int) ($dayLogs->sent_cnt ?? 0);
            $chartErrorData[] = (int) ($dayLogs->err_cnt ?? 0);
        }

        // ── Répartition par catégories ─────────────────
        $categoriesBreakdown = Category::withCount('contacts')
            ->orderByDesc('contacts_count')
            ->take(5)
            ->get();
        $uncategorizedCount = Contact::whereDoesntHave('categories')->count();

        // ── Fil d'activité récente (Timeline) ───────────
        $recentActivities = collect();

        // Ajout des dernières campagnes
        Campaign::forUser($user)->latest()->take(3)->get()->each(function ($c) use (&$recentActivities) {
            $recentActivities->push([
                'type'       => 'campaign',
                'title'      => 'Campagne « ' . $c->nom . ' » (' . ucfirst($c->statut) . ')',
                'time'       => $c->created_at,
                'time_human' => $c->created_at ? $c->created_at->diffForHumans() : 'Récemment',
                'icon_bg'    => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
            ]);
        });

        // Ajout des derniers imports
        if (Schema::hasTable('import_logs')) {
            ImportLog::with('user')->latest()->take(3)->get()->each(function ($imp) use (&$recentActivities) {
                $userName = $imp->user?->name ?? 'Utilisateur';
                $recentActivities->push([
                    'type'       => 'import',
                    'title'      => $userName . ' a importé « ' . $imp->filename . ' » — ' . $imp->imported . ' contact(s) ajouté(s)',
                    'time'       => $imp->created_at,
                    'time_human' => $imp->created_at ? $imp->created_at->diffForHumans() : 'Récemment',
                    'icon_bg'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                    'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>',
                ]);
            });
        }

        $recentActivities = $recentActivities->sortByDesc('time')->take(5)->values();

        // ── Active SMTP ─────────────────
        $activeSmtp = Schema::hasTable('smtp_settings')
            ? SmtpSetting::where('is_active', true)->first()
            : null;

        // ── Campaign stats table ─────────────────────────
        $campaignsWithStats = Campaign::forUser($user)
            ->with('creator')
            ->withCount([
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
            ])->latest()->take(6)->get();

        return view('dashboard', compact(
            'campagnesEnvoyees',
            'campagnesProgrammees',
            'campagnesCeMois',
            'emailsEnvoyes',
            'emailsDelivres',
            'emailsOuverts',
            'emailsClics',
            'emailsRejetes',
            'tauxLivraison',
            'tauxOuvertureGlobal',
            'totalContacts',
            'totalImportedLogsSum',
            'jobsEnAttente',
            'jobsEchoues24h',
            'jobsTraitesAujourdhui',
            'chartLabels',
            'chartSentData',
            'chartErrorData',
            'categoriesBreakdown',
            'uncategorizedCount',
            'recentActivities',
            'activeSmtp',
            'prospectStats',
            'campaignsWithStats',
            'isAdmin'
        ));
    }
}

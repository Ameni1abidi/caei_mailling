<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters ───────────────────────────────────────────────────────
        $period    = $request->input('period', '30');   // days
        $campaignId = $request->input('campaign_id');

        $dateFrom = match ($period) {
            '7'   => now()->subDays(7),
            '30'  => now()->subDays(30),
            '90'  => now()->subDays(90),
            '365' => now()->subDays(365),
            'all' => null,
            default => now()->subDays(30),
        };

        // ── Campaign list for filter dropdown ─────────────────────────────
        $campaigns = Campaign::select('id', 'nom', 'statut', 'created_at')
            ->latest()->get();

        // ── Global email stats (1 query) ──────────────────────────────────
        $emailQuery = EmailLog::query();
        if ($dateFrom) {
            $emailQuery->where('created_at', '>=', $dateFrom);
        }
        if ($campaignId) {
            $emailQuery->where('campaign_id', $campaignId);
        }

        $emailStats = (clone $emailQuery)
            ->selectRaw('
                status,
                COUNT(*) as cnt,
                COALESCE(SUM(opened), 0)  as opened_cnt,
                COALESCE(SUM(clicked), 0) as clicked_cnt
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $totalSent      = (int) ($emailStats->get('sent')?->cnt ?? 0)
                        + (int) ($emailStats->get('delivered')?->cnt ?? 0);
        $totalDelivered = (int) ($emailStats->get('delivered')?->cnt ?? 0);
        $totalOpened    = $emailStats->sum('opened_cnt');
        $totalClicked   = $emailStats->sum('clicked_cnt');
        $totalBounced   = (int) ($emailStats->get('bounced')?->cnt ?? 0);
        $totalFailed    = (int) ($emailStats->get('failed')?->cnt ?? 0);
        $totalInvalid   = (int) ($emailStats->get('invalid')?->cnt ?? 0);
        $totalRejected  = $totalBounced + $totalFailed + $totalInvalid;
        $totalAll       = $emailStats->sum('cnt');

        $openRate  = $totalSent > 0 ? round(($totalOpened  / $totalSent) * 100, 1) : 0;
        $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 1) : 0;
        $bounceRate = $totalAll > 0 ? round(($totalBounced / $totalAll) * 100, 1)  : 0;
        $deliveryRate = $totalAll > 0 ? round(($totalSent   / $totalAll) * 100, 1)  : 0;

        // ── Emails per day (for line chart) ───────────────────────────────
        $dailyEmailsQuery = EmailLog::selectRaw("
                DATE(created_at) as day,
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN status IN ('sent','delivered') THEN 1 ELSE 0 END), 0) as sent,
                COALESCE(SUM(opened), 0) as opened,
                COALESCE(SUM(CASE WHEN status IN ('bounced','failed','invalid') THEN 1 ELSE 0 END), 0) as rejected
            ")
            ->groupBy('day')
            ->orderBy('day');

        if ($dateFrom) {
            $dailyEmailsQuery->where('created_at', '>=', $dateFrom);
        }
        if ($campaignId) {
            $dailyEmailsQuery->where('campaign_id', $campaignId);
        }

        $dailyEmails = $dailyEmailsQuery->get();

        // ── Per-campaign stats table ──────────────────────────────────────
        $perCampaignQuery = Campaign::select('campaigns.id', 'campaigns.nom', 'campaigns.statut', 'campaigns.created_at')
            ->withCount([
                'emailLogs as total_count',
                'emailLogs as sent_count' => fn ($q) => $q->whereIn('status', ['sent', 'delivered']),
                'emailLogs as opened_count' => fn ($q) => $q->where('opened', true),
                'emailLogs as clicked_count' => fn ($q) => $q->where('clicked', true),
                'emailLogs as bounced_count' => fn ($q) => $q->where('status', 'bounced'),
                'emailLogs as failed_count' => fn ($q) => $q->whereIn('status', ['failed', 'invalid']),
            ])
            ->latest();

        if ($dateFrom) {
            $perCampaignQuery->where('campaigns.created_at', '>=', $dateFrom);
        }
        if ($campaignId) {
            $perCampaignQuery->where('campaigns.id', $campaignId);
        }

        $perCampaign = $perCampaignQuery->paginate(15)->withQueryString();

        // ── Prospect funnel stats (1 query) ───────────────────────────────
        $prospectFunnel = Contact::selectRaw('prospect_status, COUNT(*) as cnt')
            ->groupBy('prospect_status')
            ->pluck('cnt', 'prospect_status');

        // ── Top domains avec le plus de bounces ───────────────────────────
        $topBouncedDomains = DB::table('email_logs')
            ->join('contacts', 'email_logs.contact_id', '=', 'contacts.id')
            ->where('email_logs.status', 'bounced')
            ->selectRaw("SUBSTRING_INDEX(contacts.email, '@', -1) as domain, COUNT(*) as cnt")
            ->groupBy('domain')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        return view('statistics.index', compact(
            'campaigns',
            'period',
            'campaignId',
            'totalSent',
            'totalDelivered',
            'totalOpened',
            'totalClicked',
            'totalBounced',
            'totalFailed',
            'totalInvalid',
            'totalRejected',
            'totalAll',
            'openRate',
            'clickRate',
            'bounceRate',
            'deliveryRate',
            'dailyEmails',
            'perCampaign',
            'prospectFunnel',
            'topBouncedDomains',
            'emailStats'
        ));
    }
}

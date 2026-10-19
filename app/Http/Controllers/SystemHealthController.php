<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemHealthController extends Controller
{
    public function index()
    {
        // 1. File d'attente (Jobs & Failed Jobs)
        $jobsCount = DB::table('jobs')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();
        $oldestJob = DB::table('jobs')->min('created_at');
        $newestJob = DB::table('jobs')->max('created_at');

        // 2. Activité récente (Dernier email envoyé ou traité)
        $lastSentLog = EmailLog::whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->latest('updated_at')
            ->first();

        // 3. Suivi Quotas SMTP (dernière heure & aujourd'hui)
        $oneHourAgo = now()->subHour();
        $startOfDay = now()->startOfDay();

        // Récupérer les envois de la dernière heure groupés par campagne
        $sentLastHourByCampaign = EmailLog::whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->where('updated_at', '>=', $oneHourAgo)
            ->selectRaw('campaign_id, count(*) as count')
            ->groupBy('campaign_id')
            ->pluck('count', 'campaign_id');

        $sentTodayByCampaign = EmailLog::whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->where('updated_at', '>=', $startOfDay)
            ->selectRaw('campaign_id, count(*) as count')
            ->groupBy('campaign_id')
            ->pluck('count', 'campaign_id');

        // Associer à chaque compte SMTP les campagnes et le débit
        $activeCampaigns = Campaign::whereIn('statut', ['en_cours', 'en_pause'])
            ->with(['creator.smtpSetting', 'smtpSetting'])
            ->get();

        $smtpSettings = SmtpSetting::orderBy('sender_email')->get();
        $smtpStats = [];

        foreach ($smtpSettings as $smtp) {
            $smtpCampaigns = $activeCampaigns->filter(function ($c) use ($smtp) {
                $cSetting = $c->resolveSmtpSetting();
                return $cSetting && $cSetting->id === $smtp->id;
            });

            $sentLastHour = 0;
            $sentToday = 0;

            foreach ($smtpCampaigns as $c) {
                $sentLastHour += $sentLastHourByCampaign[$c->id] ?? 0;
                $sentToday += $sentTodayByCampaign[$c->id] ?? 0;
            }

            // Plafond horaire d'OVH = 200/h
            $limitHour = 200;
            $pctHour = min(100, round(($sentLastHour / $limitHour) * 100));

            $status = 'ok';
            if ($sentLastHour >= 180) {
                $status = 'danger';
            } elseif ($sentLastHour >= 130) {
                $status = 'warning';
            }

            $smtpStats[] = [
                'id'           => $smtp->id,
                'email'        => $smtp->sender_email ?? $smtp->username,
                'host'         => $smtp->host,
                'port'         => $smtp->port,
                'encryption'   => $smtp->encryption,
                'rate_limit'   => $smtp->rate_limit ?? 2,
                'is_active'    => $smtp->is_active,
                'sent_last_h'  => $sentLastHour,
                'sent_today'   => $sentToday,
                'pct_hour'     => $pctHour,
                'status'       => $status,
                'campaigns'    => $smtpCampaigns,
            ];
        }

        // 4. Détails des campagnes actives
        $campaignsData = $activeCampaigns->map(function ($c) {
            $pending = EmailLog::where('campaign_id', $c->id)->where('status', EmailLog::STATUS_PENDING)->count();
            $queued = EmailLog::where('campaign_id', $c->id)->where('status', EmailLog::STATUS_QUEUED)->count();
            $sent = EmailLog::where('campaign_id', $c->id)->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])->count();
            $failed = EmailLog::where('campaign_id', $c->id)->whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED, EmailLog::STATUS_INVALID])->count();
            $total = max(1, $pending + $queued + $sent + $failed);

            return [
                'campaign'  => $c,
                'smtp'      => $c->resolveSmtpSetting(),
                'pending'   => $pending,
                'queued'    => $queued,
                'sent'      => $sent,
                'failed'    => $failed,
                'total'     => $total,
                'pct'       => round(($sent / $total) * 100),
            ];
        });

        // 5. Dernières erreurs SMTP (10 plus récentes)
        $recentErrors = EmailLog::whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED, EmailLog::STATUS_INVALID])
            ->whereNotNull('error_message')
            ->where('error_message', '!=', '')
            ->with(['campaign:id,nom', 'contact:id,email'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        // 6. Statistiques globales du jour
        $globalTodaySent = EmailLog::whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->where('updated_at', '>=', $startOfDay)
            ->count();
        $globalTodayFailed = EmailLog::whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED, EmailLog::STATUS_INVALID])
            ->where('updated_at', '>=', $startOfDay)
            ->count();
        $globalTodayPending = EmailLog::where('status', EmailLog::STATUS_PENDING)->count();

        return view('system.monitoring', compact(
            'jobsCount',
            'failedJobsCount',
            'oldestJob',
            'newestJob',
            'lastSentLog',
            'smtpStats',
            'campaignsData',
            'recentErrors',
            'globalTodaySent',
            'globalTodayFailed',
            'globalTodayPending'
        ));
    }

    public function clearQueue(Request $request)
    {
        try {
            DB::table('jobs')->delete();
            return back()->with('success', 'La file d\'attente a été vidée avec succès ! Les jobs parasites ont été supprimés.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur lors du vidage de la file : ' . $e->getMessage());
        }
    }

    public function dispatchBatch(Request $request)
    {
        try {
            Artisan::call('campaigns:send-batch');
            return back()->with('success', 'Batch d\'envoi déclenché avec succès pour toutes les campagnes actives !');
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur lors du dispatch : ' . $e->getMessage());
        }
    }

    public function togglePause(Campaign $campaign)
    {
        if ($campaign->statut === 'en_cours') {
            $campaign->update(['statut' => 'en_pause']);
            return back()->with('success', "La campagne \"{$campaign->nom}\" a été mise en pause.");
        } elseif ($campaign->statut === 'en_pause') {
            $campaign->update(['statut' => 'en_cours']);
            return back()->with('success', "La campagne \"{$campaign->nom}\" a été reprise.");
        }

        return back()->with('error', 'Action non disponible pour le statut actuel.');
    }

    public function terminateCampaign(Campaign $campaign)
    {
        $campaign->update(['statut' => 'envoyee']);
        return back()->with('success', "La campagne \"{$campaign->nom}\" a été clôturée.");
    }
}

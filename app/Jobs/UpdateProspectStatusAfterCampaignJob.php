<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateProspectStatusAfterCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign
    ) {}

    /**
     * Execute the job.
     *
     * Uses bulk UPDATE statements instead of per-contact loops.
     * For a campaign with 1M recipients this goes from ~5M queries to ~4 queries.
     */
    public function handle(): void
    {
        $campaignId = $this->campaign->id;
        $now        = now();

        // ── 1. Bulk-update contacts whose email was sent/delivered ───────
        //    Only advance "Nouveau prospect" → "Email envoyé"
        $sentContactIds = DB::table('email_logs')
            ->where('campaign_id', $campaignId)
            ->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->pluck('contact_id');

        if ($sentContactIds->isNotEmpty()) {
            // Advance status for contacts still at "Nouveau prospect"
            DB::table('contacts')
                ->whereIn('id', $sentContactIds)
                ->where('prospect_status', Contact::STATUS_NOUVEAU)
                ->update([
                    'prospect_status'  => Contact::STATUS_EMAIL_ENVOYE,
                    'last_campaign_id' => $campaignId,
                    'last_interaction' => $now,
                    'updated_at'       => $now,
                ]);

            // Also update last_campaign_id / last_interaction for contacts
            // that were already beyond "Nouveau" (don't overwrite status)
            DB::table('contacts')
                ->whereIn('id', $sentContactIds)
                ->where('prospect_status', '!=', Contact::STATUS_NOUVEAU)
                ->update([
                    'last_campaign_id' => $campaignId,
                    'last_interaction' => $now,
                    'updated_at'       => $now,
                ]);
        }

        // ── 2. Bulk-update contacts who opened the email ─────────────────
        //    Advance "Nouveau prospect" or "Email envoyé" → "Email ouvert"
        $openedContactIds = DB::table('email_logs')
            ->where('campaign_id', $campaignId)
            ->where('opened', true)
            ->pluck('contact_id');

        if ($openedContactIds->isNotEmpty()) {
            DB::table('contacts')
                ->whereIn('id', $openedContactIds)
                ->whereIn('prospect_status', [
                    Contact::STATUS_NOUVEAU,
                    Contact::STATUS_EMAIL_ENVOYE,
                ])
                ->update([
                    'prospect_status' => Contact::STATUS_EMAIL_OUVERT,
                    'updated_at'      => $now,
                ]);
        }

        // ── 3. Bulk-insert prospect interactions (sent) ──────────────────
        //    Insert in chunks to stay within MySQL max_allowed_packet
        $sentLogs = DB::table('email_logs')
            ->where('campaign_id', $campaignId)
            ->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->select('contact_id', 'sent_at')
            ->get();

        $sentLogs->chunk(500)->each(function ($chunk) use ($campaignId, $now) {
            $rows = $chunk->map(fn ($log) => [
                'contact_id'  => $log->contact_id,
                'campaign_id' => $campaignId,
                'type'        => 'email_sent',
                'description' => "Email de la campagne envoyé (campagne #{$campaignId})",
                'metadata'    => null,
                'created_at'  => $log->sent_at ?? $now,
                'updated_at'  => $now,
            ])->toArray();

            DB::table('prospect_interactions')->insert($rows);
        });

        // ── 4. Bulk-insert prospect interactions (opened) ────────────────
        $openedLogs = DB::table('email_logs')
            ->where('campaign_id', $campaignId)
            ->where('opened', true)
            ->pluck('contact_id');

        $openedLogs->chunk(500)->each(function ($chunk) use ($campaignId, $now) {
            $rows = $chunk->map(fn ($contactId) => [
                'contact_id'  => $contactId,
                'campaign_id' => $campaignId,
                'type'        => 'email_opened',
                'description' => "Email ouvert (campagne #{$campaignId})",
                'metadata'    => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ])->toArray();

            DB::table('prospect_interactions')->insert($rows);
        });
    }
}

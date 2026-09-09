<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes for scale — millions of emails.
 *
 * Without these indexes, every worker job and every dashboard query
 * does a full table scan on email_logs (potentially millions of rows).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── email_logs ────────────────────────────────────────────────────
        Schema::table('email_logs', function (Blueprint $table) {
            // markAsSentIfAllEmailsAreSent() — WHERE campaign_id + WHERE status = 'pending'
            $table->index(['campaign_id', 'status'], 'idx_email_logs_campaign_status');

            // Duplicate-check at send time — WHERE campaign_id + WHERE contact_id + WHERE status IN (...)
            $table->index(['campaign_id', 'contact_id'], 'idx_email_logs_campaign_contact');

            // Dashboard / reporting — GROUP BY status or filter by status globally
            $table->index('status', 'idx_email_logs_status');

            // retryFailed() — WHERE campaign_id + WHERE status IN (failed, bounced)
            // Already covered by idx_email_logs_campaign_status above.

            // Tracking queries — opened / clicked counts
            $table->index('opened',  'idx_email_logs_opened');
            $table->index('clicked', 'idx_email_logs_clicked');
        });

        // ── contacts ─────────────────────────────────────────────────────
        Schema::table('contacts', function (Blueprint $table) {
            // Filter out unsubscribed contacts at send time
            $table->index('unsubscribed_at', 'idx_contacts_unsubscribed_at');

            // Target by import batch
            $table->index('import_log_id', 'idx_contacts_import_log_id');

            // Dashboard prospect stats GROUP BY
            $table->index('prospect_status', 'idx_contacts_prospect_status');
        });

        // ── campaigns ────────────────────────────────────────────────────
        Schema::table('campaigns', function (Blueprint $table) {
            // Dashboard / index page GROUP BY statut or WHERE statut = X
            $table->index('statut', 'idx_campaigns_statut');

            // auto-retry command
            $table->index(['auto_retry', 'statut'], 'idx_campaigns_auto_retry_statut');
        });

        // ── jobs (queue table) ───────────────────────────────────────────
        // Laravel's default jobs table only indexes `queue`.
        // Add a composite index that the queue worker uses: queue + available_at
        Schema::table('jobs', function (Blueprint $table) {
            // Guard: only add if it doesn't already exist
            $table->index(['queue', 'available_at'], 'idx_jobs_queue_available_at');
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropIndex('idx_email_logs_campaign_status');
            $table->dropIndex('idx_email_logs_campaign_contact');
            $table->dropIndex('idx_email_logs_status');
            $table->dropIndex('idx_email_logs_opened');
            $table->dropIndex('idx_email_logs_clicked');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('idx_contacts_unsubscribed_at');
            $table->dropIndex('idx_contacts_import_log_id');
            $table->dropIndex('idx_contacts_prospect_status');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex('idx_campaigns_statut');
            $table->dropIndex('idx_campaigns_auto_retry_statut');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('idx_jobs_queue_available_at');
        });
    }
};

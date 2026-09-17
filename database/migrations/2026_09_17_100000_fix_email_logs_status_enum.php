<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix: The `status` ENUM on `email_logs` was missing the `invalid` value in
 * the live database, causing MySQL "Data truncated" warnings whenever the job
 * tried to mark an email as invalid (e.g. domain not found / 450 response).
 *
 * We use a raw ALTER TABLE because Laravel's Blueprint->enum() on an existing
 * column requires dropping & re-adding the column, which would lose data.
 * A raw DDL statement is safer and faster on large tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Redefine the ENUM to include all known statuses, including 'invalid'.
        DB::statement("
            ALTER TABLE `email_logs`
            MODIFY COLUMN `status`
                ENUM('pending', 'sent', 'delivered', 'bounced', 'invalid', 'failed')
                NOT NULL
                DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert to the enum without 'invalid' (original broken state — rarely needed).
        DB::statement("
            ALTER TABLE `email_logs`
            MODIFY COLUMN `status`
                ENUM('pending', 'sent', 'delivered', 'bounced', 'failed')
                NOT NULL
                DEFAULT 'pending'
        ");
    }
};

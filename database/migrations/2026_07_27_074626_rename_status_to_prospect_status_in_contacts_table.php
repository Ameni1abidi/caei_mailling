<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename the 'status' column to 'prospect_status' in the contacts table.
     * The codebase uses 'prospect_status' everywhere but the DB column was named 'status'.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('status', 'prospect_status');
        });

        // Also drop the duplicate 'last_interaction_at' column if it exists
        // (the code uses 'last_interaction' added by the prospect tracking migration)
        if (Schema::hasColumn('contacts', 'last_interaction_at')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('last_interaction_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('prospect_status', 'status');
        });
    }
};

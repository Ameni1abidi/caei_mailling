<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Dernière interaction
            $table->timestamp('last_interaction')->nullable()->after('notes');
            
            // Date de prochaine relance
            $table->timestamp('next_followup_date')->nullable()->after('last_interaction');
            
            // Dernière campagne reçue
            $table->foreignId('last_campaign_id')->nullable()->constrained('campaigns')->nullOnDelete()->after('next_followup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeignIdFor('Campaign', 'last_campaign_id');
            $table->dropColumn(['last_interaction', 'next_followup_date', 'last_campaign_id']);
        });
    }
};

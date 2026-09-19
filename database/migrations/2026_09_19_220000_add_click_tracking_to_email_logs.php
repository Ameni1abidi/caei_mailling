<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le suivi des clics à la table email_logs.
     * - clicked_at : horodatage du premier clic enregistré
     * - clicked_count : nombre total de clics (pour les multi-clics)
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->timestamp('clicked_at')->nullable()->after('clicked');
            $table->unsignedSmallInteger('clicked_count')->default(0)->after('clicked_at');
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn(['clicked_at', 'clicked_count']);
        });
    }
};

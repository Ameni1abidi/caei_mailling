<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Remplace les IDs séquentiels exposés par des tokens UUID opaques pour le tracking.
     *
     * Avant : /track/open/1  → itérable par un attaquant
     * Après : /track/open/a3f8c21d-...  → impossible à deviner
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->uuid('tracking_token')
                  ->nullable()
                  ->unique()
                  ->after('id')
                  ->comment('Token UUID opaque pour les URLs de tracking ouverture/clic');
        });

        // Remplir les logs existants avec un UUID unique chacun
        DB::table('email_logs')->orderBy('id')->chunk(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('email_logs')
                    ->where('id', $row->id)
                    ->update(['tracking_token' => (string) Str::uuid()]);
            }
        });

        // Maintenant qu'ils sont tous remplis, on peut passer NOT NULL
        Schema::table('email_logs', function (Blueprint $table) {
            $table->uuid('tracking_token')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn('tracking_token');
        });
    }
};

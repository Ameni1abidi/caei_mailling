<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute des champs de gestion des bounces sur les contacts.
     *
     * - hard_bounce      : adresse définitivement invalide (ne plus jamais envoyer)
     * - soft_bounce_count: compteur de soft bounces (boîte pleine, temporaire...)
     * - last_bounce_reason: message d'erreur du NDR pour le debug
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('hard_bounce')->default(false)->after('bounced_at')
                  ->comment('Hard bounce : adresse invalide, ne plus envoyer');
            $table->unsignedSmallInteger('soft_bounce_count')->default(0)->after('hard_bounce')
                  ->comment('Nombre de soft bounces (temporaires)');
            $table->string('last_bounce_reason', 500)->nullable()->after('soft_bounce_count')
                  ->comment('Dernier message d\'erreur de bounce (NDR/SMTP)');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['hard_bounce', 'soft_bounce_count', 'last_bounce_reason']);
        });
    }
};

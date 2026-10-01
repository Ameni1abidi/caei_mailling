<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter le statut 'queued' à la colonne status de email_logs.
        // Ce statut indique qu'un job a été dispatché pour cet email_log
        // mais que l'envoi n'est pas encore confirmé.
        // Cela permet au batch command de ne pas re-dispatcher un log déjà en cours.
        DB::statement("ALTER TABLE email_logs MODIFY COLUMN status ENUM('pending','queued','sent','delivered','bounced','invalid','failed') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Remettre les 'queued' en 'pending' avant de supprimer le statut
        DB::table('email_logs')->where('status', 'queued')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE email_logs MODIFY COLUMN status ENUM('pending','sent','delivered','bounced','invalid','failed') NOT NULL DEFAULT 'pending'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des historiques d'import
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');                          // Nom original du fichier
            $table->string('disk_path')->nullable();             // Chemin stocké (optionnel)
            $table->unsignedInteger('total_rows')->default(0);   // Lignes lues dans le fichier
            $table->unsignedInteger('imported')->default(0);     // Contacts réellement créés
            $table->unsignedInteger('duplicates')->default(0);   // Ignorés car email déjà présent
            $table->unsignedInteger('errors')->default(0);       // Lignes en erreur (validation)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // Colonne de traçabilité sur les contacts
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('import_log_id')
                  ->nullable()
                  ->constrained('import_logs')
                  ->nullOnDelete()
                  ->after('last_campaign_id');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ImportLog::class, 'import_log_id');
            $table->dropColumn('import_log_id');
        });

        Schema::dropIfExists('import_logs');
    }
};

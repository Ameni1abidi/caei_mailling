<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            // Statut du traitement (pending, analyzing, mapping, processing, done, failed)
            $table->string('status')->default('done')->after('filename');
            // Chemin du fichier temporaire stocké pendant le wizard
            $table->string('temp_path')->nullable()->after('disk_path');
            // Mapping colonnes fichier → colonnes DB (JSON serialized)
            $table->json('column_mapping')->nullable()->after('temp_path');
            // Détail des erreurs ligne par ligne [{row:25, error:"email invalide"}]
            $table->json('error_details')->nullable()->after('errors');
            // IDs des catégories/listes sélectionnées lors de l'import
            $table->json('category_ids')->nullable()->after('error_details');
            // Options d'import (duplicate_strategy: ignore|update)
            $table->json('import_options')->nullable()->after('category_ids');
        });
    }

    public function down(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'temp_path', 'column_mapping',
                'error_details', 'category_ids', 'import_options',
            ]);
        });
    }
};

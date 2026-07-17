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
    Schema::create('campaigns', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->string('objet');
        $table->longText('contenu'); // HTML de l'email
        $table->string('categorie_cible')->nullable(); // liste ciblée
        $table->dateTime('date_envoi')->nullable();
        $table->enum('statut', ['brouillon', 'programmee', 'en_cours', 'envoyee', 'annulee'])
              ->default('brouillon');
        $table->foreignId('created_by')->constrained('users');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};

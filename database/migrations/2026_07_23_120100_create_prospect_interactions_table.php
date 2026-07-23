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
        Schema::create('prospect_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['email_sent', 'email_opened', 'email_clicked', 'status_change', 'note_added', 'follow_up_scheduled', 'manual_contact'])->default('manual_contact');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Pour stocker des données additionnelles (ancien statut, nouveau statut, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_interactions');
    }
};

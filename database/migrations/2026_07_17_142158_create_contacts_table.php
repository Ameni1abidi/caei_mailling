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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('entreprise')->nullable();
            $table->string('fonction')->nullable();
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('pays')->nullable();
            $table->string('ville')->nullable();
            $table->string('secteur_activite')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('active'); // active, unsubscribed, bounced, invalid
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

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
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();

            // Provider : smtp, brevo, amazon_ses, mailgun, sendgrid...
            $table->string('provider');

            // Driver Laravel : smtp, ses, mailgun, log...
            $table->string('driver')->default('smtp');

            // SMTP Configuration
            $table->string('host')->nullable();
            $table->unsignedSmallInteger('port')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // Encrypted
            $table->enum('encryption', ['tls', 'ssl'])->nullable();

            // API configuration (Brevo API, Mailgun API, SES API...)
            $table->text('api_key')->nullable(); // Encrypted

            // Sender information
            $table->string('sender_name');
            $table->string('sender_email');

            // Reply-To email
            $table->string('reply_to_email')->nullable();

            // Email sending rate (emails per minute)
            $table->unsignedInteger('rate_limit')->default(100);

            // Active configuration
            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
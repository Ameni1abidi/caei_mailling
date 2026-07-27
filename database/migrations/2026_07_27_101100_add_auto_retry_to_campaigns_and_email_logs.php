<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('auto_retry')->default(true)->after('statut');
            $table->unsignedTinyInteger('max_auto_retries')->default(3)->after('auto_retry');
        });

        Schema::table('email_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('retry_count')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['auto_retry', 'max_auto_retries']);
        });

        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn('retry_count');
        });
    }
};

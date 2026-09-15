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
        // Add user_id to smtp_settings if not present
        if (! Schema::hasColumn('smtp_settings', 'user_id')) {
            Schema::table('smtp_settings', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            });
        }

        // Add smtp_setting_id to campaigns if not present
        if (! Schema::hasColumn('campaigns', 'smtp_setting_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->foreignId('smtp_setting_id')
                    ->nullable()
                    ->after('import_log_id')
                    ->constrained('smtp_settings')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('campaigns', 'smtp_setting_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropForeign(['smtp_setting_id']);
                $table->dropColumn('smtp_setting_id');
            });
        }

        if (Schema::hasColumn('smtp_settings', 'user_id')) {
            Schema::table('smtp_settings', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};

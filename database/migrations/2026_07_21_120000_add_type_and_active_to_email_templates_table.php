<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('email_templates', 'sujet')) {
                $table->string('sujet')->nullable()->after('nom');
            }

            if (! Schema::hasColumn('email_templates', 'type')) {
                $table->string('type', 50)->default('newsletter')->after('sujet');
            }

            if (! Schema::hasColumn('email_templates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('contenu');
            }
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            if (Schema::hasColumn('email_templates', 'sujet')) {
                $table->dropColumn('sujet');
            }

            if (Schema::hasColumn('email_templates', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('email_templates', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};

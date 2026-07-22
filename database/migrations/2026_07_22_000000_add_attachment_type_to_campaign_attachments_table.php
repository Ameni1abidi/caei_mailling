<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_attachments', function (Blueprint $table) {
            $table->string('attachment_type')->nullable()->after('campaign_id');
            $table->unique(['campaign_id', 'attachment_type']);
        });
    }

    public function down(): void
    {
        Schema::table('campaign_attachments', function (Blueprint $table) {
            $table->dropUnique(['campaign_id', 'attachment_type']);
            $table->dropColumn('attachment_type');
        });
    }
};

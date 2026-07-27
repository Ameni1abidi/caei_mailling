<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('import_log_id')
                  ->nullable()
                  ->constrained('import_logs')
                  ->nullOnDelete()
                  ->after('category_ids');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ImportLog::class, 'import_log_id');
            $table->dropColumn('import_log_id');
        });
    }
};

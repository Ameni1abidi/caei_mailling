<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing contacts with legacy status 'active' or empty to 'Nouveau prospect'
        DB::table('contacts')
            ->whereIn('status', ['active', '', null])
            ->orWhereNull('status')
            ->update(['status' => 'Nouveau prospect']);

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('status')->default('Nouveau prospect')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }
};

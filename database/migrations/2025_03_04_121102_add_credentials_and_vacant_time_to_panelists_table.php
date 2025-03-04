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
        Schema::table('panelists', function (Blueprint $table) {
            $table->json('credentials')->nullable()->after('email');
            $table->json('vacant_time')->nullable()->after('credentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panelists', function (Blueprint $table) {
            $table->dropColumn(['credentials', 'vacant_time']);
        });
    }
};

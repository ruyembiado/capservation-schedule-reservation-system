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
        Schema::table('users', function (Blueprint $table) {
            $table->text('credentials')->nullable()->after('status');
            $table->integer('capacity')->default(5)->after('credentials');
            $table->text('vacant_time')->nullable()->after('capacity');
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['credentials', 'capacity', 'vacant_time']);
        });
    }
};

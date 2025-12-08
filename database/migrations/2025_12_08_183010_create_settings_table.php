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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
		    $table->string('dean_name')->nullable();
		    $table->string('dean_email')->nullable();
		    $table->string('it_head_name')->nullable();
		    $table->string('it_head_email')->nullable();
		    $table->string('cs_head_name')->nullable();
		    $table->string('cs_head_email')->nullable();
		    $table->string('is_head_name')->nullable();
		    $table->string('is_head_email')->nullable();
		    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

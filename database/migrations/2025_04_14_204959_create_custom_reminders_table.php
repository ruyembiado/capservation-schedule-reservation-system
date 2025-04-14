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
        Schema::create('custom_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('title_status');
            $table->text('message');
            $table->unsignedBigInteger('group_id'); 
            $table->string('defense_stage')->nullable(); 
            $table->dateTime('schedule_datetime');
            $table->string('status')->default('unread'); 
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_reminders');
    }
};

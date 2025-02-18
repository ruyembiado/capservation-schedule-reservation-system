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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('capstone_title_id');
            $table->unsignedBigInteger('reserve_by');
            $table->string('status')->default('pending');
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('capstone_title_id')->references('id')->on('capstones')->onDelete('cascade');
            $table->foreign('reserve_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};

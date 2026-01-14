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
        Schema::create('capstone_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capstone_id');
            $table->unsignedBigInteger('user_id');
            $table->string('old_capstone_name');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('capstone_id')->references('id')->on('capstones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_histories');
    }
};

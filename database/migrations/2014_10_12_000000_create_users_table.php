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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->unique();
            $table->json('members')->nullable();
            $table->string('program')->nullable();
            $table->string('year_section')->nullable();
            $table->string('position')->nullable();
            $table->string('capstone_adviser')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->enum('user_type', ['student', 'instructor', 'admin']);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('status')->default('1');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('instructor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

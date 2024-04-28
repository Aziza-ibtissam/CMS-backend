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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userID')->constrained('users');
            $table->string('email');
            $table->string('title');
            $table->string('acronym');
            $table->string('city');
            $table->string('country');
            $table->string('webpage');
            $table->string('category');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->dateTime('paper_subm_due_date');
            $table->dateTime('register_due_date')->default('2024-01-01');
            $table->dateTime('acceptation_notification')->default('2024-01-01');
            $table->string('camera_ready_paper')->default('camera_ready_paper');
            $table->string('logo');
            $table->integer('is_verified')->default(0);
            $table->integer('is_accept')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};

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
            $table->foreignId('form_id')->nullable()->constrained('forms');
            $table->foreignId('topic_id')->nullable()->constrained('topics');
            $table->foreignId('paper_call_id')->nullable()->constrained('paper_calls');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->dateTime('paper_subm_date');
            $table->string('logo');
            $table->integer('is_verified')->default(0);
            $table->integer('is_accept')->default(0);
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

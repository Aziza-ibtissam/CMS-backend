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
        Schema::create('paper_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->json('emailAuther');
            $table->dateTime('callstart_at');
            $table->dateTime('callend_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_calls');
    }
};

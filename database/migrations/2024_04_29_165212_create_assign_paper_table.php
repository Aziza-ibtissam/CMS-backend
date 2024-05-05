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
        Schema::create('assign_paper', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->unsignedBigInteger('paper_id');
            $table->foreign('paper_id')->references('id')->on('papers')->onDelete('cascade');
            $table->unsignedBigInteger('userId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->json('answers');
            $table->integer('finalDecision');
            $table->enum('isEligible', ['yes', 'no'])->default('no');
            $table->text('comments');
            $table->text('confidentialRemarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_paper');
    }
};

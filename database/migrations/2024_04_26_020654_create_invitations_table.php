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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade'); 
            $table->string('email');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('affiliation');
            $table->enum('invitationStatus', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviewer_conferences');
    }
};

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
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->unique();
            $table->unsignedBigInteger('conference_id');
            $table->foreign('conference_id')->references('id')->on('conferences'); 
            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('paperTitle');
            $table->string('paperFile');
            $table->json('authors');
            $table->text('abstract');
            $table->string('keywords');
            $table->dateTime('submitted_at');
            $table->decimal('mark')->default(0);
            $table->enum('acceptations_setting', ['pending','Oral_presentations', 'Poster', 'Waiting_list','Rejected'])->default('pending');
            $table->string('finalVersionFile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('papers');
    }

};

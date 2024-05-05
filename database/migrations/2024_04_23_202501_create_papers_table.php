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
            $table->string('paperTitle');
            $table->string('emailAuth');
            $table->dateTime('submitted_at');
            $table->string('paperfile');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->unsignedBigInteger('conference_id');
            $table->foreign('conference_id')->references('id')->on('conferences'); 
            $table->decimal('mark')->default(0);
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

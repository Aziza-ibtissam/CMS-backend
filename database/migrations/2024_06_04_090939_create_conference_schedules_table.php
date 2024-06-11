<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConferenceSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conference_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('conference_id')->constrained('conferences')->onDelete('cascade');
            $table->integer('session_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conference_schedules');
    }
}

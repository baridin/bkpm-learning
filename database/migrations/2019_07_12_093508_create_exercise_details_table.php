<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExerciseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercise_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exercise_id');
            $table->string('key');
            $table->string('value');
            $table->timestamps();

            $table->foreign('exercise_id')
                ->references('id')->on('exercises')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_details');
    }
}

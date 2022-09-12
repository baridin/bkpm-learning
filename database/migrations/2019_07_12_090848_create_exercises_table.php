<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->unsignedBigInteger('section_id');
            $table->string('title')->nullable();
            $table->enum('type', ['pg', 'essay', 'file'])->default('file');
            $table->bigInteger('line')->default(1);
            $table->timestamps();

            $table->foreign('mata_diklat_id')
                ->references('id')->on('mata_diklats')
                ->onDelete('cascade');

            $table->foreign('section_id')
                ->references('id')->on('sections')
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
        Schema::dropIfExists('exercises');
    }
}

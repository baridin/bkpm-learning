<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizzUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizz_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quizz_id');
            $table->unsignedBigInteger('user_id');
            $table->string('answer');
            $table->timestamps();

            $table->foreign('quizz_id')->references('id')->on('quizzs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizz_users');
    }
}

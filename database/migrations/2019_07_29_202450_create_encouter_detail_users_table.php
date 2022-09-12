<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncouterDetailUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encouter_detail_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('encouter_detail_id');
            $table->unsignedBigInteger('user_id');
            $table->string('answer');
            $table->bigInteger('value')->nullable();
            $table->timestamps();

            $table->foreign('encouter_detail_id')->references('id')->on('encouter_details')->onDelete('cascade');
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
        Schema::dropIfExists('encouter_detail_users');
    }
}

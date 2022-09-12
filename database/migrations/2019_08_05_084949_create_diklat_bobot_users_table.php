<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatBobotUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklat_bobot_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('diklat_bobot_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('diklat_id');
            $table->bigInteger('assesment')->default(0);
            $table->timestamps();

            $table->foreign('diklat_bobot_id')->references('id')->on('diklat_bobots')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('diklat_id')->references('id')->on('diklats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diklat_bobot_users');
    }
}

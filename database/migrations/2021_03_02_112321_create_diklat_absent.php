<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatAbsent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklat_absent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('diklat_id');
            $table->unsignedBigInteger('diklat_detail_id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->unsignedBigInteger('user_id');
            $table->string('signature');
            $table->timestamps();

            $table->foreign('diklat_id')->references('id')->on('diklats')->onDelete('cascade');
            $table->foreign('diklat_detail_id')->references('id')->on('diklat_details')->onDelete('cascade');
            $table->foreign('mata_diklat_id')->references('id')->on('mata_diklats')->onDelete('cascade');
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
        Schema::dropIfExists('diklat_absent');
    }
}

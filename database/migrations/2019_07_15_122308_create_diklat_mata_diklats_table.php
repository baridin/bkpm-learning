<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatMataDiklatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklat_mata_diklats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->unsignedBigInteger('diklat_id');
            $table->bigInteger('bobot')->default(0);
            $table->timestamps();

            $table->foreign('mata_diklat_id')
                ->references('id')->on('mata_diklats')
                ->onDelete('cascade');
            $table->foreign('diklat_id')
                ->references('id')->on('diklats')
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
        Schema::dropIfExists('diklat_mata_diklats');
    }
}

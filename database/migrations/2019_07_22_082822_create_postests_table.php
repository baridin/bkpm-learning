<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('diklat_id');
            $table->timestamps();

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
        Schema::dropIfExists('postests');
    }
}

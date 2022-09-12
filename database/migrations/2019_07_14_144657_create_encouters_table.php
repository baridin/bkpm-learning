<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncoutersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encouters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->string('title')->nullable();
            $table->text('detail')->nullable();
            $table->string('file');
            $table->bigInteger('start_at');
            $table->time('time');
            $table->bigInteger('duration');
            $table->timestamps();

            $table->foreign('mata_diklat_id')
                ->references('id')->on('mata_diklats')
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
        Schema::dropIfExists('encouters');
    }
}

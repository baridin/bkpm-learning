<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaerahKcamatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daerah_kcamatans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('daerah_kbupaten_id');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('daerah_kbupaten_id')->references('id')->on('daerah_kbupatens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daerah_kcamatans');
    }
}

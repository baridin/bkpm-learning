<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaerahKbupatensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daerah_kbupatens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('daerah_provinsi_id');
            $table->string('nama');
            $table->unsignedBigInteger('daerah_jenis_id');
            $table->timestamps();

            $table->foreign('daerah_provinsi_id')->references('id')->on('daerah_provinsis')->onDelete('cascade');
            $table->foreign('daerah_jenis_id')->references('id')->on('daerah_jenis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daerah_kbupatens');
    }
}

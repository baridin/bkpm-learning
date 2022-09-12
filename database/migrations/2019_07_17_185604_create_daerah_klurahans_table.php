<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaerahKlurahansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daerah_klurahans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('daerah_kcamatan_id');
            $table->string('nama');
            $table->unsignedBigInteger('daerah_jenis_id');
            $table->timestamps();

            $table->foreign('daerah_kcamatan_id')->references('id')->on('daerah_kcamatans')->onDelete('cascade');
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
        Schema::dropIfExists('daerah_klurahans');
    }
}

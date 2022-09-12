<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankSoalLatihansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_soal_latihans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->enum('type_soal', ['pg'])->default('pg');
            $table->text('soal')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('mata_diklat_id')->references('id')->on('mata_diklats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_soal_latihans');
    }
}

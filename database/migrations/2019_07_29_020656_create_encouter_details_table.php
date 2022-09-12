<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncouterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encouter_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('encouter_id');
            $table->enum('type', ['pg', 'essay'])->default('essay');
            $table->enum('key', ['soal','true','a','b','c','d','e'])->default('soal');
            $table->text('value');
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('encouter_id')->references('id')->on('encouters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encouter_details');
    }
}

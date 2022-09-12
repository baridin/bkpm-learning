<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatParrentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklat_parrents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('diklat_id');
            $table->bigInteger('bobot')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')->on('diklats')
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
        Schema::dropIfExists('diklat_parrents');
    }
}

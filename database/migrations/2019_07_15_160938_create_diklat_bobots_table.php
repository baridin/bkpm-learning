<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatBobotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklat_bobots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('diklat_id');
            $table->string('title')->nullable();
            $table->enum('type', ['online', 'offline', 'etc'])->default('online');
            $table->bigInteger('bobot')->default(0);
            $table->timestamps();

            $table->foreign('diklat_id')->references('id')->on('diklats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diklat_bobots');
    }
}

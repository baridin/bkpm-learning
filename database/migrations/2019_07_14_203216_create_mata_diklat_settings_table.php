<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMataDiklatSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mata_diklat_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->boolean('available')->default(false);
            $table->boolean('is_video')->default(false);
            $table->boolean('is_quiz')->default(false);
            $table->boolean('is_exercise')->default(false);
            $table->boolean('is_encounter')->default(false);
            $table->boolean('is_masterpiece')->default(false);
            $table->timestamps();

            $table->foreign('mata_diklat_id')->references('id')
                ->on('mata_diklats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mata_diklat_settings');
    }
}

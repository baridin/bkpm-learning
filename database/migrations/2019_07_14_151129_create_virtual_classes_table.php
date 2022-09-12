<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirtualClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('zoom_account_id');
            $table->bigInteger('diklat_id');
            $table->bigInteger('diklat_detail_id');
            $table->unsignedBigInteger('mata_diklat_id');
            $table->string('title')->nullable();
            $table->enum('type', ['meeting', 'webinar'])->default('webinar');
            $table->string('zoom_id')->nullable();
            $table->text('zoom_join')->nullable();
            $table->text('zoom_start')->nullable();
            $table->text('detail')->nullable();
            $table->string('password');
            $table->bigInteger('start_at');
            $table->time('time');
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
        Schema::dropIfExists('virtual_classes');
    }
}

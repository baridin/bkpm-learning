<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('line');
            $table->enum('type', ['video', 'pdf'])->default('video');
            $table->string('file')->nullable();
            $table->string('wistia_hashed_link')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('section_id');
            $table->timestamps();

            $table->foreign('section_id')
                ->references('id')->on('sections')
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
        Schema::dropIfExists('materials');
    }
}

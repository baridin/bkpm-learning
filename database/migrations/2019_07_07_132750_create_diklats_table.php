<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiklatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diklats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('category_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('is_publish')->default(true);
            $table->text('suitable')->nullable();
            $table->text('requirement')->nullable();
            $table->text('can_be')->nullable();
            $table->string('image')->nullable();
            $table->string('file_requirment')->nullable();
            $table->string('video')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_tag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diklats');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankSoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_soals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['latihan', 'ujian', 'pretest', 'postest'])->default('latihan');
            $table->enum('type_soal', ['pg', 'essay'])->default('essay');
            $table->text('soal')->nullable();
            $table->text('details')->nullable();
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
        Schema::dropIfExists('bank_soals');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldMataDiklatIdOnBankSoalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_soals', function (Blueprint $table) {
            $table->unsignedBigInteger('mata_diklat_id')->nullable();
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
        Schema::table('bank_soals', function (Blueprint $table) {
            $table->dropForeign(['mata_diklat_id']);
        });
    }
}

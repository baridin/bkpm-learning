<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankSoalIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pretest_details', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_soal_id')->nullable();
            $table->enum('type_soal', ['pg', 'essay'])->default('pg');
            $table->text('details')->nullable();
            $table->foreign('bank_soal_id')->references('id')->on('bank_soals')->onDelete('set null');
        });

        Schema::table('postest_details', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_soal_id')->nullable();
            $table->enum('type_soal', ['pg', 'essay'])->default('pg');
            $table->text('details')->nullable();
            $table->foreign('bank_soal_id')->references('id')->on('bank_soals')->onDelete('set null');
        });

        Schema::table('exercise_details', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_soal_id')->nullable();
            $table->foreign('bank_soal_id')->references('id')->on('bank_soals')->onDelete('set null');
            $table->text('details')->nullable();
        });

        Schema::table('encouter_details', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_soal_id')->nullable();
            $table->foreign('bank_soal_id')->references('id')->on('bank_soals')->onDelete('set null');
            $table->text('details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pretest_details', function (Blueprint $table) {
            $table->dropColumn('type_soal');
            $table->dropColumn('details');
            $table->dropForeign(['bank_soal_id']);
        });

        Schema::table('postest_details', function (Blueprint $table) {
            $table->dropColumn('type_soal');
            $table->dropColumn('details');
            $table->dropForeign(['bank_soal_id']);
        });

        Schema::table('exercise_details', function (Blueprint $table) {
            $table->dropForeign(['bank_soal_id']);
            $table->text('details')->nullable();
        });

        Schema::table('encouter_details', function (Blueprint $table) {
            $table->dropForeign(['bank_soal_id']);
            $table->text('details')->nullable();
        });
    }
}

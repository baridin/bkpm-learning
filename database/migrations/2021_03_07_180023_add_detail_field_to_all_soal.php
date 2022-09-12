<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailFieldToAllSoal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encouters', function (Blueprint $table) {
            $table->enum('settings', ['otomatis', 'manual'])->default('manual');
            $table->text('options')->nullable(); // json data count of essay and pg, null if manual
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->enum('settings', ['otomatis', 'manual'])->default('manual');
            $table->text('options')->nullable(); // json data count of essay and pg, null if manual
        });

        Schema::table('pretests', function (Blueprint $table) {
            $table->enum('settings', ['otomatis', 'manual'])->default('manual');
            $table->text('options')->nullable(); // json data count of essay and pg, null if manual
        });

        Schema::table('postests', function (Blueprint $table) {
            $table->enum('settings', ['otomatis', 'manual'])->default('manual');
            $table->text('options')->nullable(); // json data count of essay and pg, null if manual
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('encouters', function (Blueprint $table) {
            $table->dropColumn('settings');
            $table->dropColumn('options'); // json data count of essay and pg, null if manual
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn('settings');
            $table->dropColumn('options'); // json data count of essay and pg, null if manual
        });

        Schema::table('pretests', function (Blueprint $table) {
            $table->dropColumn('settings');
            $table->dropColumn('options'); // json data count of essay and pg, null if manual
        });

        Schema::table('postests', function (Blueprint $table) {
            $table->dropColumn('settings');
            $table->dropColumn('options'); // json data count of essay and pg, null if manual
        });
    }
}

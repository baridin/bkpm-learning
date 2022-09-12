<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAbsensiVirtualClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('virtual_classes')) {
            Schema::table('virtual_classes', function (Blueprint $table) {
                $table->boolean('absensi')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('virtual_classes')) {
            Schema::table('virtual_classes', function (Blueprint $table) {
                $table->dropColumn('absensi');
            });
        }
    }
}

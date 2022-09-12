<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates', function(Blueprint $table) {
            $table->enum('source', ['system', 'manual'])->default('system');
            $table->json('details')->nullable(); // apabila nulll maka transkip nilai di generate oleh sistem
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates', function(Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('details');
        });
    }
}

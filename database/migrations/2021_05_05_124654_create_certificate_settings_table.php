<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('logo');
            $table->string('logo_transkip');
            $table->text('kepala_pusdiklat');
            $table->text('nip_kepala_pusdiklat');
            $table->text('berdasar_akreditasi')->default('490/K.1/PDP.10.4');
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
        Schema::dropIfExists('certificate_settings');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->nullable()->unique();
            $table->bigInteger('category_id')->default(1);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('kelamin');
            $table->string('password');
            $table->text('password_encrypt')->nullable();
            $table->string('facebook')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('birth_date')->nullable();
            $table->text('home_address')->nullable();
            $table->text('home_city')->nullable();
            $table->text('home_prov')->nullable();
            $table->text('home_phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('boss_name')->nullable();
            $table->string('boss_phone')->nullable();
            $table->string('dept')->nullable();
            $table->string('info_instansion')->nullable();
            $table->string('info_instansion_detail')->nullable();
            $table->text('office_address')->nullable();
            $table->text('office_city')->nullable();
            $table->text('office_prov')->nullable();
            $table->text('office_phone')->nullable();
            $table->text('office_fax')->nullable();
            $table->string('website')->nullable();
            $table->string('position')->nullable();
            $table->string('bagian')->nullable();
            $table->string('grade')->nullable();
            $table->string('provider_id')->nullable();
            $table->enum('status', ['normal', 'pending', 'active', 'blacklist', 'finish'])->default('pending');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}

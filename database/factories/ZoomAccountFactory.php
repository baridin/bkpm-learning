<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ZoomAccount;
use Faker\Generator as Faker;

$factory->define(ZoomAccount::class, function (Faker $faker) {
    /**
     * Nama: Pusdiklat BKPM1
    Username: zoommeetingpusdiklat1@gmail.com
    Password: Virtual2021
    Api key: q8goRm-0T8SpKb3n2s4xHg
    Api Secret: c0y1LWD6eCbrq6nx8ALJxxSU3O4KK1CeH0Bi
    IM Chat Hystory Token: eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJSR2ZwS0VsYlRRU1RwbmlFMEdZVkdBIn0.Qf8myibNwLv05dcuzCst_xcenhdMBHYGngHfzLKXVhA
    JWT Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6InE4Z29SbS0wVDhTcEtiM24yczR4SGciLCJleHAiOjE3MzkwMTQ3NDAsImlhdCI6MTYxMjc3ODk3NH0.6Nmtngv6cf7M29AVPk6x3qG74QoZYOh_Bwv93S3Lv74
    Expired: 06:39 02/08/2025

    Nama: Pusdiklat BKPM2
    Username: zoommeetingpusdiklat2@gmail.com
    Password: Virtual2021
    Api key: F7O62XnHTUG-PSgaxDa05g
    Api Secret: 1JxrhxrIGywH6q7tudG4ypF2gnTiA590F6ZP
    IM Chat Hystory Token: eyJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJLVzVqQmdGY1NabW9vN09rcWdBaERBIn0.BSBxhpZMlHtUMWXmD_UTGgDyzWJTTzKYMOU4E2KE-NU
    JWT Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IkY3TzYyWG5IVFVHLVBTZ2F4RGEwNWciLCJleHAiOjE3MzkwMTQ3NDAsImlhdCI6MTYxMjc3OTI2OH0.Bw5K6v2pNaQ4WAeKWG_kmCRWPrW_sjYBEQtf5gpY_vc
    Expired: 06:39 02/08/2025
     */
    return [
        'name' => '',
        'email' => '',
        'password' => '',
        'jwt_token' => '',
        'is_active' => '',
    ];
});

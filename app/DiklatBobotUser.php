<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiklatBobotUser extends Model
{
    protected $fillable = [
        'diklat_bobot_id',
        'user_id',
        'diklat_id',
        'assesment',
    ];

    public $timestamps = true;
}

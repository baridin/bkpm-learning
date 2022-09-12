<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiklatUser extends Model
{
    protected $fillable = [
        'diklat_id',
        'user_id',
        'progress',
    ];

    public $timestamps = true;
}

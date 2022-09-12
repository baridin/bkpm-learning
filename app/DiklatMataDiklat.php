<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiklatMataDiklat extends Model
{
    protected $fillable = [
        'diklat_id',
        'mata_diklat_id',
        'bobot',
    ];

    public $timestamp = true;
}

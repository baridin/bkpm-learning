<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DaerahJenis extends Model
{
    protected $fillable = [
        'nama'
    ];

    public $timestamps = true;
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    protected $fillable = [
        'title'
    ];

    public $timestamps = true;
}

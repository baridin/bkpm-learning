<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PretestUser extends Model
{
    protected $fillable = [
        'user_id',
        'pretest_id',
        'value',
    ];

    public $timestamps = true;
}

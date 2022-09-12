<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostestUser extends Model
{
    protected $fillable = [
        'user_id',
        'postest_id',
        'value',
    ];

    public $timestamps = true;
}

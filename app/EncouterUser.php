<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncouterUser extends Model
{
    protected $fillable = [
        'user_id',
        'encouter_id',
        'assesment',
        'is_remedial',
        'status_ujian'
    ];

    public $timestamps = true;
}

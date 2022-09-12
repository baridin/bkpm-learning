<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseUser extends Model
{
    protected $fillable = [
        'user_id',
        'exercise_id'.
        'assesment'
    ];

    public $timestamps = true;
}

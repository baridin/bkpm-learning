<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseDetailUser extends Model
{
    protected $fillablde = [
        'exercise_detail_id',
        'user_id',
        'answer'
    ];

    public $timestamps = true;
}

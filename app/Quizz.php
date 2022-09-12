<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    protected $fillable = [
        'material_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_true',
        'minute',
        'second',
    ];

    public $timestamp = true;

    public function quizAnswers()
    {
        return $this->belongsToMany(User::class, 'quizz_users', 'quizz_id', 'user_id')->withTimestamps()->withPivot('answer');
    }
}

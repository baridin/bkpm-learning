<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseDetail extends Model
{
    protected $fillable = [
        'exercise_id',
        'key',
        'value',
        'details',
        'bank_soal_id'
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany('App\User', 'exercise_detail_users', 'exercise_detail_id', 'user_id')->withPivot('answer')->withTimestamps();
    }
    
    public function bankSoal()
    {
        return $this->hasOne(BankSoal::class, 'id', 'bank_soal_id');
    }

    // public function getDetailsAttribute()
    // {
    //     return json_decode($this->attributes['details']);
    // }
}

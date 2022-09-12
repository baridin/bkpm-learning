<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PretestDetail extends Model
{
    protected $fillable = [
        'pretest_id',
        'question',
        'details',
        'type_soal',
        'bank_soal_id'
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany('App\User', 'pretest_detail_users', 'pretest_detail_id', 'user_id')->withPivot('answer', 'diklat_id')->withTimestamps();
    }

    public function bankSoal()
    {
        return $this->hasOne(BankSoal::class, 'id', 'bank_soal_id');
    }

    public function getDetailsAttribute()
    {
        return json_decode($this->attributes['details']);
    }
}

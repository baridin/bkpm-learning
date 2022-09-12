<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostestDetail extends Model
{
    protected $fillable = [
        'postest_id',
        'question',
        'details',
        'type_soal',
        'bank_soal_id',
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany('App\User', 'postest_detail_users', 'postest_detail_id', 'user_id')->withPivot('answer', 'diklat_id')->withTimestamps();
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

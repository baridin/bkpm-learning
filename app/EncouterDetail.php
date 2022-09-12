<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncouterDetail extends Model
{
    protected $fillable = [
        'encouter_id',
        'type',
        'key',
        'value',
        'details', // store soal
        'bank_soal_id'
    ];

    public $timestamps = true;

    public function encounter()
    {
        return $this->hasOne('App\Encouter', 'id', 'encouter_id');
    }

    public function users($user=NULL)
    {
        if (!empty($user)) {
            return $this->hasOne('App\EncouterDetailUser', 'encouter_detail_id', 'id')->where('encouter_detail_users.user_id', $user);
        } else {
            return $this->belongsToMany('App\User', 'encouter_detail_users', 'encouter_detail_id', 'user_id')->withPivot('answer')->withTimestamps();
        }
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

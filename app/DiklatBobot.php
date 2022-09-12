<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiklatBobot extends Model
{
    protected $fillable = [
        'diklat_id',
        'title',
        'type',
        'bobot',
        'custom_name_sertif'
    ];
    public $timestamps = true;

    public function users($user_id=null)
    {
        $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id) ;
        if (!is_null($user_id)) {
            return $this->hasOne('App\DiklatBobotUser', 'diklat_bobot_id', 'id')->whereUserId($user->id);
        } else {
            return $this->belongsToMany('App\User', 'exercise_users', 'exercise_id', 'user_id')->withPivot('assesment')->withTimestamps();
        }
    }
}

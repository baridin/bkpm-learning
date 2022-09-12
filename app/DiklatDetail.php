<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiklatDetail extends Model
{
    protected $fillable = [
        'diklat_id',
        'title',
        'kuota',
        'force',
        'online_at',
        'start_at',
        'end_at',
    ];

    public $timestamps = true;

    public function users(int $active=null)
    {
        $user = $this->belongsToMany(User::class, 'diklat_detail_users', 'diklat_detail_id', 'user_id')->withTimestamps()->withPivot('status', 'file');
        if (!is_null($active)) {
            $user->wherePivot('status', 2)->wherePivot('file', '!=', null);
        }
        return $user;
    }

    public function userActive()
    {
        return $this->users()->select('users.id', 'users.name', 'users.username', 'users.mobile')->where('users.status', 'active')->get();
    }

    public function getDiklat(int $id = null)
    {
        if (is_null($id)) {
            return $this->hasOne(Diklat::class, 'id', 'diklat_id');
        }
    }
}

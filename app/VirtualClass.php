<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VirtualClass extends Model
{
    protected $fillable = [
        'diklat_id',
        'diklat_detail_id',
        'zoom_account_id',
        'mata_diklat_id',
        'title',
        'type',
        'zoom_id',
        'zoom_join',
        'zoom_start',
        'detail',
        'absensi',
        'password',
        'start_at',
        'duration',
    ];

    public $timestamp = true;

    public function users($id=NULL)
    {
        if (!empty($id)) {
            return $this->hasOne(VirtualClassUser::class, 'virtual_class_id', 'id')->where('virtual_class_users.user_id', $id);
        } else {
            return $this->belongsToMany(User::class, 'virtual_class_users', 'virtual_class_id', 'user_id')->withPivot('value')->withTimestamps();
        }
    }

    public function detailDiklatId()
    {
        return $this->hasOne(DiklatDetail::class, 'id', 'diklat_detail_id');
    }

    public function diklatId()
    {
        return $this->hasOne(Diklat::class, 'id', 'diklat_id');
    }

    public function mataDiklatId()
    {
        return $this->hasOne(MataDiklat::class, 'id', 'mata_diklat_id');
    }
}

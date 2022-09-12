<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'diklat_id',
        'diklat_detail_id',
        'no_absen',
        'no_certificate',
        'nilai',
        'source',
        'details',
        'is_remedial',
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public $timestamps = true;

    public function diklat()
    {
        return $this->hasOne(Diklat::class, 'id', 'diklat_id');
    }

    public function diklatDetail()
    {
        return $this->hasOne(DiklatDetail::class, 'id', 'diklat_detail_id');
    }
    public function getDiklat()
    {
        return $this->belongsToMany(Diklat::class, 'diklat_users', 'user_id', 'diklat_id')->withTimestamps();
    }

    public function getDiklatDetail()
    {
        return $this->belongsToMany(DiklatDetail::class, 'diklat_detail_users', 'user_id', 'diklat_detail_id')->withTimestamps()->withPivot('id', 'status', 'file', 'diklat_id', 'diklat_detail_id', 'user_id');
    }

    public function getDiklatDetailYear()
    {
        return $this->belongsToMany(DiklatDetail::class, 'diklat_detail_users', 'user_id', 'diklat_detail_id')->withTimestamps()->withPivot('id', 'status', 'file', 'diklat_id', 'diklat_detail_id', 'user_id');
    }

    public function getLatestDiklat()
    {
        return $this->getDiklat()->latest('created_at')->first();
    }

    public function getLatestDetailDiklat(int $id)
    {
        return $this->getDiklatDetail()->where('diklat_detail_users.diklat_id', $id)->latest('created_at')->first();
    }
}

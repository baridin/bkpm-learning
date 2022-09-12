<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    //
    protected $table = 'certificates';
    protected $fillable = [
        'user_id',
        'diklat_id',
        'diklat_detail_id',
        'no_absen',
        'no_certificate',
        'nilai',
        'source',
        'details'
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
    
}

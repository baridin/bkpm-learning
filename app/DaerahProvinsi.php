<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DaerahProvinsi extends Model
{
    protected $fillable = [
        'daerah_kcamatan_id',
        'nama',
        'daerah_jenis_id',
    ];

    public $timestamps = true;
}

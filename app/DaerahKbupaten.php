<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DaerahKbupaten extends Model
{
    protected $fillable = [
        'daerah_provinsi_id',
        'nama',
        'daerah_jenis_id',
    ];

    public $timestamps = true;
}

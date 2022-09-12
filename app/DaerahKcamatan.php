<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DaerahKcamatan extends Model
{
    protected $fillable = [
        'daerah_kbupaten_id',
        'nama',
    ];

    public $timestamps = true;
}

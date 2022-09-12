<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'mata_diklat_id',
        'type_soal',
        'type',
        'soal',
        'details'
    ];

    protected $casts = [
        'type' => 'array'
    ];
}

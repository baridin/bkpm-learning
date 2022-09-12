<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VirtualClassAbsent extends Model
{
    protected $fillable = [
        'virtual_class_id',
        'diklat_id',
        'diklat_detail_id',
        'mata_diklat_id',
        'user_id',
        'signature',
        'created_at',
    ];

    public $timestamp = true;
}

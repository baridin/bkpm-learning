<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterPiece extends Model
{
    protected $fillable = [
        'user_id',
        'diklat_id',
        'mata_diklat_id',
        'type',
        'title',
        'file',
    ];

    public $timestamp = true;
}

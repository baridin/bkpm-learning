<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialUser extends Model
{
    protected $fillable = [
        'material_id',
        'user_id',
    ];

    public $timestamps = true;
}

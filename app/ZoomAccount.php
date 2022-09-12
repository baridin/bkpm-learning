<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoomAccount extends Model
{
    protected $fillable = [
        'name',
        'email',
        'api_key',
        'jwt_token',
        'is_active'
    ];
}

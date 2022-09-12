<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErrorDc extends Model
{
    //
    protected $fillable = ['response'];
    protected $table = 'error_dc';
}

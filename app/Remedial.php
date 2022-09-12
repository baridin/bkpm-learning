<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remedial extends Model
{
    //
   	protected $table = 'remedial';
   	protected $fillable = ['user_id','diklat_id','mata_diklat_id','encouter_id','nilai'];
}

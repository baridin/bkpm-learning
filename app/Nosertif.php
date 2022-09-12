<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nosertif extends Model
{
    //
    protected $table = 'no_sertif';
    protected $fillable = ['nip','status'];
}

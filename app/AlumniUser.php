<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlumniUser extends Model
{
    protected $fillable = [
        'email',
        'username',
        'category_id',
        'name',
        'facebook',
        'birth_place',
        'birth_date',
        'home_address',
        'home_city',
        'home_prov',
        'home_phone',
        'mobile',
        'boss_name',
        'boss_phone',
        'dept',
        'info_instansion',
        'office_address',
        'office_city',
        'office_prov',
        'office_phone',
        'office_fax',
        'website',
        'position',
        'bagian',
        'grade',
        'status'
    ];

    public $timestamps = true;
}

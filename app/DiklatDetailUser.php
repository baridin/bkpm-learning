<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiklatDetailUser extends Model
{
    protected $fillable = [
        'user_id',
        'diklat_id',
        'diklat_detail_id',
        'status',
        'status_nilai',
        'file'
    ];

    public $timestamps = true;

    public function users()
    {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

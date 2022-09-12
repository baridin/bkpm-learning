<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncouterDetailUser extends Model
{
    protected $fillable = [
        'encouter_detail_id',
        'user_id',
        'answer',
        'status_ujian',
        'value',
        'encouter_id',
    ];

    public $timestamps = true;

    public function details()
    {
        return $this->hasOne('App\EncouterDetail', 'id', 'encouter_detail_id');
    }

    public function encounter()
    {
        return $this->details->encounter;
    }
}

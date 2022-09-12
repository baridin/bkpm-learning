<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonitorLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'item_id',
        'type',
        'type_detail',
    ];

    public $timestamps = true;

    static function insert(array $data)
    {
        return self::create($data);
    }
}

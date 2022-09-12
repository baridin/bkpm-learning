<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\MenuItem;

class Menu extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;

    public function getItem()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'id');
    }
}

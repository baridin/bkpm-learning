<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'title',
        'description',
        'line',
        'mata_diklat_id',
    ];

    public $timestampts = true;
    
    public function materials()
    {
        return $this->hasMany(Material::class, 'section_id', 'id')->orderBy('line');
    }

    public function exercieses()
    {
        return $this->hasMany(Exercise::class, 'section_id', 'id')->orderBy('line');
    }

    public function mataDiklat()
    {
        return $this->hasOne(MataDiklat::class, 'section_id', 'id');
    }
}

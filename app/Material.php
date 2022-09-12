<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'title',
        'line',
        'type',
        'file',
        'wistia_hashed_link',
        'thumbnail',
        'video',
        'status',
        'section_id',
    ];

    public $timestamps = true;

    public function quizz()
    {
        return $this->hasMany('App\Quizz', 'material_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'material_users', 'material_id', 'user_id')->withTimestamps();
    }
}

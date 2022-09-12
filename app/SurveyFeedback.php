<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyFeedback extends Model
{
    protected $fillable = [
        'question',
    ];

    public $timestamp = true;

    public function users(int $diklat_id = null)
    {
        if (is_null($diklat_id)) {
            return $this->belongsToMany(User::class, 'survey_feedback_users', 'survey_feedback_id', 'user_id')->withPivot('value', 'diklat_id')->withTimestamps();
        } else {
            return $this->belongsToMany(User::class, 'survey_feedback_users', 'survey_feedback_id', 'user_id')->withPivot('value', 'diklat_id')->wherePivot('diklat_id', $diklat_id);
        }
    }
}

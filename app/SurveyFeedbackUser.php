<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyFeedbackUser extends Model
{
    protected $fillable = [
        'user_id',
        'diklat_id',
        'survey_feedback_id',
        'value',
    ];

    public $timestamps = true;
}

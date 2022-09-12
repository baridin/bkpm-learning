<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyFeedbackInstrukturUser extends Model
{
    //
    protected $fillable = [
        'user_id',
        'diklat_id',
        'instruktur_id',
        'survey_feedback_instruktur_id',
        'value',
    ];

    public $timestamps = true;
}

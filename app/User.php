<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'category_id',
        'username',
        'name',
        'email',
        'avatar',
        'password',
        'password_encrypt',
        'facebook',
        'birth_date',
        'birth_place',
        'home_address',
        'home_city',
        'home_prov',
        'home_phone',
        'mobile',
        'boss_name',
        'boss_phone',
        'dept',
        'info_instansion',
        'info_instansion_detail',
        'office_address',
        'office_city',
        'office_prov',
        'office_phone',
        'office_fax',
        'website',
        'position',
        'bagian',
        'grade',
        'status',
        'settings',
        'kelamin',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getDiklat()
    {
        return $this->belongsToMany(Diklat::class, 'diklat_users', 'user_id', 'diklat_id')->withTimestamps();
    }

    public function getDiklatDetail()
    {
        return $this->belongsToMany(DiklatDetail::class, 'diklat_detail_users', 'user_id', 'diklat_detail_id')->withTimestamps()->withPivot('id', 'status', 'file', 'diklat_id', 'diklat_detail_id', 'user_id');
    }

    public function getDiklatDetailYear()
    {
        return $this->belongsToMany(DiklatDetail::class, 'diklat_detail_users', 'user_id', 'diklat_detail_id')->withTimestamps()->withPivot('id', 'status', 'file', 'diklat_id', 'diklat_detail_id', 'user_id');
    }

    public function getLatestDiklat()
    {
        return $this->getDiklat()->latest('created_at')->first();
    }

    public function getLatestDetailDiklat(int $id)
    {
        return $this->getDiklatDetail()->where('diklat_detail_users.diklat_id', $id)->latest('created_at')->first();
    }

    public function assesmentExercises($id = null)
    {
        if (empty($id)) {
            return $this->hasMany(ExerciseUser::class);
        } else {
            return $this->hasOne(ExerciseUser::class)->whereExerciseId($id);
        }
    }

    public function assesmentEncounter($id = null)
    {
        if (empty($id)) {
            return $this->hasMany(EncouterUser::class);
        } else {
            return $this->hasOne(EncouterUser::class)->whereEncouterId($id);
        }
    }

    public function monitorLog()
    {
        return $this->hasMany(MonitorLog::class);
    }

    public function material()
    {
        return $this->hasMany(MaterialUser::class);
    }

    public function quizz()
    {
        return $this->hasMany(QuizzUser::class);
    }

    public function exercise()
    {
        return $this->hasMany(ExerciseUser::class);
    }

    public function virtualClass()
    {
        return $this->hasMany(VirtualClassUser::class);
    }

    public function encounter()
    {
        return $this->hasMany(EncouterUser::class);
    }

    public function surveys()
    {
        return $this->belongsToMany(SurveyFeedback::class, 'survey_feedback_users', 'user_id', 'survey_feedback_id')->withPivot('diklat_id', 'value')->withTimestamps();
    }
    public function surveysInstruktur()
    {
        return $this->belongsToMany(SurveyFeedbackInstruktur::class, 'survey_feedback_instruktur_users', 'user_id', 'survey_feedback_instruktur_id')->withPivot('diklat_id', 'value')->withTimestamps();
    }

    public function mySurveys(int $user_id = null, int $diklat_id = null, int $survey_id = null)
    {
        $survey = SurveyFeedbackUser::whereUserId($user_id)->whereDiklatId($diklat_id)->whereSurveyFeedbackId($survey_id)->first();
        return $nilai = ($survey) ? $survey->value : 0;
    }
    public function mySurveyso(int $user_id = null, int $diklat_id = null, int $survey_id = null,int $instruktur_id = null)
    {
        $survey = SurveyFeedbackInstrukturUser::whereUserId($user_id)->whereDiklatId($diklat_id)->whereSurveyFeedbackInstrukturId($survey_id)->where('instruktur_id',$instruktur_id)->first();
        return $nilai = ($survey) ? $survey->value : 0;
    }

    public function diklatCertificate()
    {
        return $this->belongsToMany(Diklat::class, 'certificates', 'user_id', 'diklat_id')->withTimestamps()->withPivot('id', 'user_id', 'diklat_id', 'diklat_detail_id', 'no_absen', 'no_certificate', 'nilai');
    }

    public function myScore(int $diklat_id)
    {
        $user = auth()->user();
        $diklat = $this->getDiklat()->findOrFail($diklat_id);
        $mata = $diklat->mataDiklat;
        $nilai = 0;
        $n_ex = [];
        $n_en = [];
        foreach ($mata as $km => $vm) {
            foreach ($vm->sections as $ks => $vs) {
                foreach ($vs->exercieses as $ke => $ve) {
                    $exn = $ve->users($ve->id, $user->id)->first();
                    if (!empty($exn)) {
                        array_push($n_ex, $exn->assesment);
                    }
                }
            }
            foreach ($vm->encounters($user->id, $diklat->id)->get() as $kx => $vx) {
                $vxen = $vx->users($user->id)->first();
                if (!empty($vxen)) array_push($n_en, $vxen->assesment);
            }
        }
        if (count($n_ex) > 0 && count($n_en) > 0) {
            $n_exer = array_sum(array_values($n_ex)) / count($n_ex) * 100;
            $n_enc = array_sum(array_values($n_en)) / count($n_en) * 100;
            $nilai = round($n_exer) + round($n_enc);
        }
        return $nilai;
    }

    public function kategori()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}

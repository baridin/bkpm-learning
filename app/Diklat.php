<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Diklat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'is_publish',
        'suitable',
        'requirement',
        'can_be',
        'image',
        'file_requirment',
        'video',
        'meta_title',
        'meta_description',
        'meta_tag',
        'category_id',
        'duration'
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany(User::class, 'diklat_users', 'diklat_id', 'user_id')->withTimestamps();
    }

    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }

    public function detail()
    {
        $years = Carbon::now()->format('Y');
        return $this->hasMany('App\DiklatDetail', 'diklat_id', 'id')->where(DB::raw("YEAR(start_at)"), $years);
    }

    public function diklatDetail()
    {
        return $this->hasMany(DiklatDetail::class, 'diklat_id', 'id');
    }

    public function categoryDiklat()
    {
        return $this->belongsToMany('App\Category', 'diklat_categories', 'diklat_id', 'category_id');
    }

    public function mataDiklat()
    {
        return $this->belongsToMany('App\MataDiklat', 'diklat_mata_diklats', 'diklat_id', 'mata_diklat_id')->withTimestamps()->withPivot('bobot', 'id');
    }
    // public function mataDiklat()
    // {
    //     return $this->belongsToMany('App\Instruktur', 'diklat', 'diklat_id', 'mata_diklat_id')->withTimestamps()->withPivot('bobot', 'id');
    // }

    public function bobots()
    {
        return $this->hasMany(DiklatBobot::class, 'diklat_id', 'id')->orderByDesc('id');
    }

    public function method()
    {
        $method = 'E-Learning';
        $bobots = $this->bobots()->get()->pluck('type')->toArray();
        if (count($bobots) > 0 && in_array('offline', $bobots)) {
            $method = 'Blended';
        }
        return $method;
    }
    public function custom_name()
    {
        
        $name = $this->bobots()->get()->where('diklat_id')->first();
        if (empty($name)) {
             $method = 'E-Learning';
        }else{
            $method  = $name->custom_name_sertif;
        }
        return $method;
    }

    public function durations($isLowercase = false)
    {
        $str = '';
        $mataDiklats = $this->mataDiklat()->get()->pluck('duration')->toArray();
        $minutes = array_sum($mataDiklats);
        $timeArr = explode(':', gmdate("H:i:s", $minutes * 60));
        $str = (int)$timeArr[0] > 0 ? (int)$timeArr[0]." Jam " : '';
        $str .= (int)$timeArr[1] > 0 ? (int)$timeArr[1]." Menit" : '';
        return !$isLowercase ? $str : strtolower($str);
    }

    public function diklatParent()
    {
        return $this->belongsToMany('App\Diklat', 'diklat_parrents', 'diklat_id', 'parent_id')->orderByDesc('diklat_parrents.id');
    }

    public function pretest()
    {
        return $this->hasMany(Pretest::class);
    }

    public function postest()
    {
        return $this->hasMany(Postest::class);
    }

    public function survey(int $id = null)
    {
        if (is_null($id)) {
            return SurveyFeedback::orderBy('created_at')->get();
        }
    }
    public function survey_instruktur(int $id = null)
    {
        if (is_null($id)) {
            return SurveyFeedbackInstruktur::orderBy('created_at')->get();
        }
    }

    // public function getProgress($user_id = null)
    // {
    //     $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id);
    //     $mata = $this->mataDiklat()->get();
    //     $has = [];
    //     foreach ($mata as $value) {
    //         array_push($has, $value->status($user_id, $this->id));
    //     }
    //     $hasil = array_sum(array_values($has)) / count($has);
    //     return $hasil;
    // }

    public function getProgress($user_id = null)
    {
        $user = User::findOrFail($user_id);
        $mata = $this->mataDiklat()->get();
        $has = [];
        foreach ($mata as $value) {
            array_push($has, $value->status($user->id, $this->id));
        }
        $hasil = array_sum(array_values($has)) / count($has);
        if($hasil == 75){
            return 0;
        }else{
            return $hasil;
        }
    }

    public function getScore($user_id = null)
    {
        $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id);
        $mata = $this->mataDiklat()->get();
        $has = [];
        $hes = [];
        foreach ($mata as $value) {
            // array_push($has, $value->score($user->id));
            $bobot = $value->pivot->bobot;
            $nm = $value->score($user->id, $this->id);
            $aw =  $nm * $bobot;
            array_push($has, $aw);
            array_push($hes, $bobot);
        }
        $hasil = array_sum(array_values($has)) / array_sum(array_values($hes));
        return round($hasil);
    }

    public function getNilai($user_id = null)
    {
        $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id);
        $bobots = $this->bobots()->get();
        $hasil = 0;
        $has = [];
        $hes = [];
        if (!empty($bobots->count())) {
            foreach ($bobots as $value) {
                $bobot = $value->bobot;
                $nm = (!empty($value->users($user->id)->first())) ? $value->users($user->id)->first()->assesment : 0;
                $aw =  $nm * $bobot;
                array_push($has, $aw);
                array_push($hes, $bobot);
            }
            $hasil = array_sum(array_values($has)) / array_sum(array_values($hes));
            return round($hasil);
        } else {
            return $this->getScore($user->id);
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\DiklatDetail;

class MataDiklat extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title',
        'image',
        'is_publish',
        'category_id',
        'description',
        'suitable',
        'requirement',
        'can_be',
        'wistia_hashed_id',
        'meta_title',
        'meta_description',
        'meta_tag',
        'duration', // durasi menit
    ];

    public $timestamps = true;

    public function sections()
    {
        return $this->hasMany(Section::class, 'mata_diklat_id', 'id')->orderBy('line');
    }

    public function reference()
    {
        return $this->hasMany(Reference::class, 'mata_diklat_id', 'id')->orderBy('id');
    }

    public function exercieses()
    {
        return $this->hasMany(Exercise::class, 'section_id', 'id')->orderBy('line');
    }

    public function encounters(int $user = null, int $diklat_id = null)
    {
        // $users = User::find($user);
        // $diklat = Diklat::find($diklat_id);
        if (!empty($user) && !empty($diklat_id)) {
            $users = User::findOrFail($user);
            $diklat = Diklat::findOrFail($diklat_id);

            if($users->username == 'pusdiklat_bkpm') {
                $diklat_detail = DiklatDetail::where('diklat_id', $diklat->id)->limit(1)->get();

                return $this->hasMany(Encouter::class, 'mata_diklat_id', 'id')->whereDiklatDetailId($diklat_detail[0]->id)->inRandomOrder();
            }
            else {
                return $this->hasMany(Encouter::class, 'mata_diklat_id', 'id')->whereDiklatDetailId($users->getLatestDetailDiklat($diklat->id)->id)->inRandomOrder();
            }
        } else {
            return $this->hasMany(Encouter::class, 'mata_diklat_id', 'id')->inRandomOrder();
        }
    }

    public function virtualClass(int $user = null, int $diklat_id = null)
    {
        if (!empty($user) && !empty($diklat_id)) {
            $users = User::findOrFail($user);
            $diklat = Diklat::findOrFail($diklat_id);

            if($users->username == 'pusdiklat_bkpm') {
                $diklat_detail = DiklatDetail::where('diklat_id', $diklat->id)->limit(1)->get();
                
                return $this->hasMany(VirtualClass::class, 'mata_diklat_id', 'id')->whereDiklatDetailId($diklat_detail[0]->id);
            }
            else {
                return $this->hasMany(VirtualClass::class, 'mata_diklat_id', 'id')->whereDiklatDetailId($users->getLatestDetailDiklat($diklat->id)->id);
            }
        } else {
            return $this->hasMany(VirtualClass::class, 'mata_diklat_id', 'id');
        }
    }

    function countMaterial($user_id=null)
    {
        $section = $this->sections()->get();
        $material = [];
        foreach ($section as $ks => $vs) {
            if (!empty($user_id)) {
                foreach ($vs->materials()->get() as $km => $vm) {
                    $vm_u = $vm->users()->whereUserId($user_id)->first();
                    if (!empty($vm_u)) {
                        array_push($material, 1);
                    } else {
                        array_push($material, 0);
                    }

                }
            } else {
                array_push($material, $vs->materials()->get()->count());
            }
        }
        return array_sum(array_values($material));
    }

    // function countQuizz($user_id=null)
    // {
    //     $section = $this->sections()->get();
    //     $quizz = [];
    //     foreach ($section as $ks => $vs) {
    //         foreach ($vs->materials as $km => $vm) {
    //             if (!empty($user_id)) {
    //                 foreach ($vm->quizz()->get() as $km => $vm) {
    //                     $vm_u = $vm->quizAnswers()->whereUserId($user_id)->first();
    //                     if (!empty($vm_u)) {
    //                         array_push($quizz, 1);
    //                     } else {
    //                         array_push($quizz, 0);
    //                     }

    //                 }
    //             } else {
    //                 array_push($quizz, $vm->quizz()->get()->count());
    //             }
    //         }
    //     }
    //     return array_sum(array_values($quizz));
    // }

    function countExercise($user_id=null)
    {
        $section = $this->sections()->get();
        $exer = [];
        foreach ($section as $ks => $vs) {
            if (!empty($user_id)) {
                foreach ($vs->exercieses()->get() as $km => $vm) {
                    $vm_u = $vm->users()->whereUserId($user_id)->first();
                    if (!empty($vm_u)) {
                        array_push($exer, 1);
                    } else {
                        array_push($exer, 0);
                    }
                }
            } else {
                array_push($exer, $vs->exercieses()->get()->count());
            }
        }
        return array_sum(array_values($exer));
    }

    function countVirtualClass($id=null, $user=null, $diklat_id=null)
    {
        $vir = [];
        if (is_null($user)) {
            return $this->virtualClass($id, $diklat_id)->get()->count();
        } else {
            $uV = $this->virtualClass($id, $diklat_id)->get();
            foreach ($uV as $km => $vm) {
                $vm_u = $vm->users($id)->first();
                if (!empty($vm_u)) {
                    array_push($vir, 1);
                } else {
                    array_push($vir, 0);
                }
            }
            return array_sum(array_values($vir));
        }
    }

    function countEncounter($id=null, $user=null, $diklat_id=null)
    {
        $vir = [];
        if (is_null($user)) {
            return $this->encounters($id, $diklat_id)->get()->count();
        } else {
            $uV = $this->encounters($id, $diklat_id)->get();
            foreach ($uV as $km => $vm) {
                $vm_u = $vm->users($id)->first();
                if (!empty($vm_u)) {
                    array_push($vir, 1);
                } else {
                    array_push($vir, 0);
                }
            }
            return array_sum(array_values($vir));
        }

    }

    function getSetting($id=null)
    {
        return $this->hasOne(MataDiklatSetting::class, 'mata_diklat_id', 'id');
    }

    function status($user_id=null, int $diklat_id=null)
    {
        $progres = [];
        // $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id) ;
        $user = User::findOrFail($user_id) ;
        $diklat = Diklat::findOrFail($diklat_id);
        $data_m = [
            'is_video' => $this->countMaterial(),
            // 'is_quiz' => $this->countQuizz(),
            'is_exercise' => $this->countExercise(),
            'virtual_class' => $this->countVirtualClass($user->id, null, $diklat->id),
            'is_encounter' => $this->countEncounter($user->id, null, $diklat->id),
        ];
        $data_u = [
            'is_video' => $this->countMaterial($user->id),
            // 'is_quiz' => $this->countQuizz($user->id),
            'is_exercise' => $this->countExercise($user->id),
            'virtual_class' => $this->countVirtualClass($user->id, 'user', $diklat->id),
            'is_encounter' => $this->countEncounter($user->id, 'user', $diklat->id),
        ];
        $pem = 100 / count($data_m);
        foreach ($data_m as $key => $value) {
            $setting = $this->getSetting;
            if ($key == 'virtual_class' || $setting->{$key} == 1) {
                if ($value == 0 || $data_u[$key] >= $value) {
                    array_push($progres, $pem);
                } else {
                    array_push($progres, 0);
                }
            } else {
                array_push($progres, $pem);
            }
        }
        $hasil = array_sum(array_values($progres));
        $hasil_akhir =  ($hasil >= 100) ? 100 : $hasil ;
        return ($hasil >= 100) ? 100 : $hasil ;
        // if($hasil_akhir <= 50){
        //     return 0;
        // }else{
        //     return ($hasil >= 100) ? 100 : $hasil;
        // }
        // return $this->countEncounter($user->id, null, $diklat->id);
    }

    public function score($user_id=null, int $diklat_id=null)
    {
        $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id) ;
        $diklat = Diklat::findOrFail($diklat_id);
        $pg = call_user_func(array(&$this, 'scoreType'), $user->getKey(), $diklat->getKey(), 'pg');
        $essay = call_user_func(array(&$this, 'scoreType'), $user->getKey(), $diklat->getKey(), 'essay');
        $nilai = (!empty($pg) || !empty($essay))?((!empty($essay))?($pg+$essay)/2:$pg):0;
        return round($nilai);
    }

    public function scoreType($user_id=null, int $diklat_id=null, string $type)
    {
        $type2 = [];
        $nilai = 0;
        $user = (is_null($user_id)) ? auth()->user() : User::findOrFail($user_id) ;
        $diklat = Diklat::findOrFail($diklat_id);
        $uV = $this->encounters($user->id, $diklat->id);
        if ($uV) {
            $uV = $uV->get();
            foreach ($uV as $km => $vm) {
                foreach ($vm->details as $kd => $vd) {
                    if ($vd->key == 'soal') {
                        if ($vd->type == "{$type}") {
                            $vm_e = $vd->users($user->id)->first();
                            array_push($type2, (!empty($vm_e))?round($vm_e->value):0);
                        }
                    }
                }
            }
            if (array_sum(array_values($type2)) > 0) {
                $nilai_ujian = array_sum(array_values($type2)) / count($type2);
                if (!empty($nilai_ujian)) $nilai = round($nilai_ujian);
            }
        }
        return $nilai;
    }
}

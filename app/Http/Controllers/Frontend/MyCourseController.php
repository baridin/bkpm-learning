<?php

namespace App\Http\Controllers\Frontend;

use App\Certificate;
use App\CertificateSetting;
use Illuminate\Http\Request;
use App\ModulTambahan;
use App\User;
use App\Diklat;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\DiklatDetailUser;
// use Illuminate\Database\Eloquent\Builder;
use App\Section;
use App\Material;
use App\MataDiklat;
use App\Exercise;
use App\Encouter;
use App\VirtualClass;
use App\DaerahKbupaten;
use App\DaerahProvinsi;
use App\Dept;
use App\DiklatInstruktur;
use App\EncouterUser;
use App\MonitorLog;
use App\Quizz;
use App\SurveyFeedback;
use App\SurveyFeedbackInstruktur;
use App\SurveyFeedbackInstrukturUser;
use App\DiklatUser;
use App\EncouterDetailUser;
use App\SurveyFeedbackUser;
use App\Grade;
use App\Position;
use App\Postest;
use App\Pretest;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

// use App\PretestUser;
use Barryvdh\DomPDF\PDF;
// use Dompdf\Dompdf;

class MyCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if($user->username == 'pusdiklat_bkpm') {
            return redirect('/diklat');
        } else if (count($user->getDiklat) > 0) {
            // $diklat = $user->getDiklat->unique('id');
            $detail = $user->getDiklatDetail->unique('id');
            $data = [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'item_id' => 0,
                'type' => 'url',
                'type_detail' => 'LOG_MELIHAT_DIKLAT_SAYA',
            ];
            MonitorLog::insert($data);
            return view('frontend.my-course.index', compact('detail'));
        } else {
            session()->flash('info_msg', 'Anda belum memiliki diklat. Silahkan mendaftar Diklat terlebih dahulu');
            return redirect('/diklat');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
       public function show(Request $request, int $id)
    {
        $user = auth()->user();
        $diklat = Diklat::with('mataDiklat.sections.materials')->findOrFail($id);

        $pret = Pretest::whereDiklatId($id)->whereHas('users', function ($builder) use ($user) {
            $builder->where('users.id', $user->id);
        });
        $getPretestDetail = Pretest::whereDiklatId($id)->with('details')->whereHas('details', function ($builder) {
            $builder->orderBy('created_at');
        });
        $pretest = (!empty($getPretestDetail->get()->count()) && !empty($pret->get()->count())) ? null : $getPretestDetail->first();
        $postest = null;
        $post = Postest::whereDiklatId($id)->whereHas('users', function ($builder) use ($user) {
            $builder->where('users.id', $user->id);
        });
        $getPostestDetail = Postest::whereDiklatId($id)->with('details')->whereHas('details', function ($builder) {
            $builder->orderBy('created_at');
        });
        $postest = (!empty($getPostestDetail->get()->count()) && !empty($post->get()->count())) ? null : $getPostestDetail->first();
        $sur = SurveyFeedback::whereHas('users', function ($builder) use ($user, $id) {
            $builder->where('users.id', $user->id)->where('survey_feedback_users.diklat_id', $id);
        })->get()->count();
        $survey = ($sur > 0) ? null : SurveyFeedback::orderBy('created_at')->get();
        
        $suri = SurveyFeedbackInstruktur::whereHas('users', function ($builder) use ($user, $id) {
            // $get_instruktur = DiklatInstruktur::where('diklat_id',$id)->get();
            $builder->where('users.id', $user->id)->where('survey_feedback_instruktur_users.diklat_id', $id)->where('instruktur_id',99);
        })->get()->count();

        $survey_instruktur =  SurveyFeedbackInstruktur::orderBy('created_at')->get();
        
         $get_instruktur = DiklatInstruktur::where('diklat_id',$diklat->id)->get();

        // SurveyFeedbackInstruktur::where('user_id',$user->id)->where('diklat_id',$id)->get();



        
        if($user->username != 'pusdiklat_bkpm')
        {
            $diklat_detail = DiklatUser::whereDiklatId($diklat->id)->whereUserId($user->id)->firstOrFail();
            $diklat_detail->update([
                'progress' => $diklat->getProgress(),
            ]);  
        }
        // try {
        //     $nilai = $diklat->getScore($user->id);
        // } catch (\Throwable $th) {
        //     $nilai = 0;
        // }
        $get_nilai_fix = Certificate::where('user_id',$user->id)->where('diklat_id',$id)->first();
        if(empty($get_nilai_fix)){
            $nilai = 0;
        }else{
            $nilai = $get_nilai_fix->nilai;   
        }
        $data = [
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'item_id' => $diklat->id,
            'type' => 'url',
            'type_detail' => 'LOG_MELIHAT_DIKLAT_' . strtoupper(str_replace(' ', '_', $diklat->title))
        ];
        MonitorLog::insert($data);

        // $get_instruktur = DiklatInstruktur::where('diklat_id',$diklat->id)->where('status','yes')->get();
       
        
        return view('frontend.my-course.diklat.index', compact('diklat', 'pretest', 'postest', 'survey', 'nilai','survey_instruktur','get_instruktur'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $dept = Dept::orderByDesc('id')->get();
        $grade = Grade::orderByDesc('id')->get();
        $position = Position::orderByDesc('id')->get();
        $kab = DaerahKbupaten::orderByDesc('id')->get();
        $prov = DaerahProvinsi::orderByDesc('id')->get();
        return view('frontend.my-course.profile', compact('dept', 'grade', 'position', 'kab', 'prov', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->except('_token', '_method'));
        session()->flash('success_msg', 'Anda berhasil Mengupdate Data Anda!');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkNip(Request $request)
    {
        $user = User::where('username', $request->nip)->first();
        if (empty($user)) {
            return response()->json('not_found', 208);
        } else {
            $diklat = Diklat::with('diklatParent')->findOrFail($request->diklat);
            if (count($diklat->diklatParent) > 0) {
                $title = [];
                foreach ($diklat->diklatParent as $kd => $vd) {
                    array_push($title, $vd->title);
                }
                return response()->json($title, 207);
            } else {
                return response()->json('success', 200);
            }
        }
    }

    public function findLocation(Request $request)
    {
        if ($request->type == 'point') {
            $kab = DaerahKbupaten::where('nama', "{$request->val}")->first();
            if (!empty($kab)) {
                $prov = DaerahProvinsi::findOrFail($kab->daerah_provinsi_id);
                return response()->json($prov);
            }
        }
        if ($request->type == 'list') {
            if ($request->val == 'kota' || $request->val == 'kabupaten') {
                $kab = DaerahKbupaten::orderBy('nama')->get();
                return response()->json($kab);
            } else if ($request->val == 'provinsi') {
                $kab = DaerahProvinsi::orderBy('nama')->get();
                return response()->json($kab);
            }
        }
    }

    public function uploadPersyaratn(Request $request, $id)
    {
        $file = Storage::putFile('public/persyaratan', $request->file('file'));
        $d_detail = DiklatDetailUser::findOrFail($id);
        $d_detail->file = $file;
        $d_detail->status = 1;
        $d_detail->save();
        $data = [
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'item_id' => $d_detail->diklat_detail_id,
            'type' => 'url',
            'type_detail' => 'LOG_MENGUPLOAD_PERSYARATAN_ANGKATAN_' . strtoupper(str_replace(' ', '_', $d_detail->force)),
        ];
        MonitorLog::insert($data);
        session('success_msg', 'Berhasil mnupload persyaratn, silahkan tunggu konfirmasi dr admin.');
        return back();
    }

    // show material
    public function showMaterial(Request $request, $dt_id, $mt_id, $type, $section_id, $id)
    {
        $user = Auth::user();
        $diklat = Diklat::findOrFail($dt_id);
        $mata = MataDiklat::with('sections.materials', 'virtualClass', 'encounters')->findOrFail($mt_id);
        $quizzo = collect();
        $refer = collect();
        switch ($type) {
            case 'material':
                $data = Material::with('quizz')->findOrFail($id);
                $quizzo = $data->quizz->toJson();
                $refer = $mata->reference;
                $datas = [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'item_id' => $data->id,
                    'type' => $data->type,
                    'type_detail' => 'LOG_MELIHAT_MATERI_' . strtoupper(str_replace(' ', '_', $data->title)),
                ];
                MonitorLog::insert($datas);
                $data->users()->attach(auth()->id());
                break;
            case 'latihan':
                $data = Exercise::findOrFail($id);
                $datas = [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'item_id' => $data->id,
                    'type' => 'latihan',
                    'type_detail' => 'LOG_MELIHAT_LATIHAN_' . strtoupper(str_replace(' ', '_', $data->title)),
                ];
                MonitorLog::insert($datas);
                break;
            case 'modultambahan';
                $data = ModulTambahan::findOrFail($id);
                $datas = [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'item_id' => $data->id,
                    'type' => 'mudultambahan',
                    'type_detail' => 'LOG_MELIHAT_MODUL_TAMBAHAN_' . strtoupper(str_replace(' ', '_', $data->judul)),
                ];
                // MonitorLog::insert($datas);
                break;
            case 'ujian':
                $data = Encouter::with('details')->findOrFail($id);
                $datas = [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'item_id' => $data->id,
                    'type' => 'ujian',
                    'type_detail' => 'LOG_MELIHAT_UJIAN_' . strtoupper(str_replace(' ', '_', $data->title)),
                ];
                MonitorLog::insert($datas);
                break;
            case 'virtual-class':
                $data = VirtualClass::findOrFail($id);
                $datas = [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'item_id' => $data->id,
                    'type' => 'virtual_class',
                    'type_detail' => 'LOG_MELIHAT_VIRTUAL_CLASS_' . strtoupper(str_replace(' ', '_', $data->title)),
                ];
            
                MonitorLog::insert($datas);
                break;
                
        }
        $modulTambahan = ModulTambahan::where('mata_diklat_id',$mt_id)->get();
        return view('frontend.my-course.mata-diklat.index', compact('data', 'mata', 'quizzo', 'refer', 'user', 'diklat', 'type','modulTambahan'));
    }

    
    public function answerExercise(Request $request, $id)
    {
        $user = Auth::user();
        $exercise = Exercise::find($id);
        $detail = $exercise->details;
        $nilai = [];
        $benar = [];
        $answer = [];
        foreach ($detail as $k => $v) {
            $details = json_decode($v->details);
            $soal = ($v->key == 'soal') ? $v->id : 0;
            isset($details->is_true) ? array_push($benar, $details->is_true) : '';
            if ($request->has("answer{$soal}")) {
                $a = $request->post("answer{$soal}");
                $ja = array_shift($a);
                $v->users()->attach($user->id, [
                    'answer' => $ja,
                ]);
                array_push($answer, $ja);
            }
        }
        foreach ($answer as $ks => $vs) {
            if (strtolower($vs) == strtolower($benar[$ks])) {
                array_push($nilai, 1);
            } else {
                array_push($nilai, 0);
            }
        }
        $skor = array_sum(array_values($nilai)) / count($nilai) * 100;
      
        $exercise->users()->attach($user->id, [
            'assesment' => round($skor)
        ]);
        $datas = [
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'item_id' => $exercise->id,
            'type' => 'latihan',
            'type_detail' => 'LOG_MENJAWAB_LATIHAN_' . strtoupper(str_replace(' ', '_', $exercise->title)),
        ];
        MonitorLog::insert($datas);
        return back();
    }

    public function answerEncounter(Request $request, $id)
    {
        $user = Auth::user();
        $encounter = Encouter::findOrFail($id);
        $detail = $encounter->details;
        $answerPg = [];
        $answerEssay = [];
        foreach ($detail as $k => $v) {
            $details = json_decode($v->details);
            if ($v->type == 'pg') {
                if ($request->has("answer_{$v->id}")) {
                    if($request->post("answer_{$v->id}") != null)
                    {
                        $answerPg[] = [
                            'id' => $v->id,
                            'answer' => strtolower($request->post("answer_{$v->id}")),
                            'value' => strtolower($request->post("answer_{$v->id}")) === $details->is_true ? 100 : 0,
                            'status_ujian' => 1
                        ];   
                    }
                } else {
                    // if ($v->key == 'soal') {
                    //     $answerPg[] = [
                    //         'id' => $v->id,
                    //         'value' => 'z'
                    //     ];
                    // }
                }
            }
            if ($v->type == 'essay') {
                if ($request->has("answer_{$v->id}")) {
                    if($request->post("answer_{$v->id}") != null)
                    {
                        $answerEssay[] = [
                            'encouter_detail_id' => $v->id, 
                            'user_id' => $user->id,
                            'answer' => $request->post("answer_{$v->id}"),
                        ];
                        EncouterDetailUser::updateOrCreate(
                            ['encouter_detail_id' => $v->id, 'user_id' => $user->id,'encouter_id' => $id],
                            ['answer' => $request->post("answer_{$v->id}")]
                        );
                        EncouterUser::where('encouter_id',$id)->where('user_id',$user->id)->update(['status_ujian'=>'1']);
                    }
                }
            }
        }
        
        if(count($answerPg) + count($answerEssay) >= count($detail))
        {
            $nilai = [];
            for ($i = 0; $i < count($answerPg); $i++) {
                EncouterDetailUser::updateOrCreate(
                    ['encouter_detail_id' => $answerPg[$i]['id'], 'user_id' => $user->id,'encouter_id' => $id],
                    ['answer' => $answerPg[$i]['answer'], 'value' => $answerPg[$i]['value']]
                );
                array_push($nilai, $answerPg[$i]['value']);
            }
            if (empty($detail->where('type', 'essay')->first())) {
                $t = count($nilai) * 100;
                $nilai_sum = (array_sum(array_values($nilai)) / $t) * 100;
                EncouterUser::updateOrCreate(
                    ['encouter_id' => $encounter->id, 'user_id' => $user->id],
                    ['assesment' => round($nilai_sum)]
                );
                EncouterUser::where('encouter_id',$id)->where('user_id',$user->id)->update(['status_ujian'=>'1']);
            }

            $datas = [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'item_id' => $encounter->id,
                'type' => 'ujian',
                'type_detail' => 'LOG_MENJAWAB_LATIHAN_' . strtoupper(str_replace(' ', '_', $encounter->title)),
            ];
            MonitorLog::insert($datas);
            session()->flash('success_msg', 'Anda Berhasil Mengerjakan Ujian.');
            return back();
        }
        else
        {
            session()->flash('error_msg', 'Jawaban masih ada yang kosong.');
            return back();
        }
    }

    public function joinVirtualClass(Request $request, $id)
    {
        $user = Auth::user();
        $virtual = VirtualClass::findOrFail($id);
        if (!is_null($virtual)) {
            $datas = [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'item_id' => $virtual->id,
                'type' => 'virtual_class',
                'type_detail' => 'LOG_MENGIKUTI_VIRTUAL_CLASS_' . strtoupper(str_replace(' ', '_', $virtual->title)),
            ];
            MonitorLog::insert($datas);
            $virtual->users()->attach($user->id);
            return redirect("{$virtual->zoom_join}");
        }
    }

    public function answerPrePro(Request $request, $type, $diklat_id, $pretest_id)
    {
        // dd($request->all());
        $diklat = Diklat::findOrFail($diklat_id);
        $pretest = app("App\\" . ucwords($type))::findOrFail($pretest_id);
        $user = auth()->user();
        $type = ucwords($type);
        $model = app("App\\{$type}Detail");
        $answer = app("App\\{$type}User");
        $nilai = [];
        foreach ($request->all() as $k => $v) {
            if (substr($k, 0, 6) == 'answer') {
                $fData = $model->findOrFail((int) substr($k, 6));
                if (!is_null($fData)) {
                    $fData->users()->attach($user->id, [
                        'answer' => $v
                    ]);
                    (strtolower($v) == strtolower($fData->details->is_true)) ? array_push($nilai, 1) : array_push($nilai, 0);
                }
            }
        }
        $total = round(array_sum(array_values($nilai)) / count($nilai) * 100);
        $answer::updateOrCreate(
            [
                'user_id' => $user->id,
                strtolower($type) . '_id' => (!empty($pretest)) ? $pretest->id : $pretest_id,
            ],
            ['value' => $total]
        );
        $datas = [
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'item_id' => $diklat->id,
            'type' => strtolower($type),
            'type_detail' => 'LOG_MENJAWAB_' . strtoupper($type) . '_' . strtoupper(str_replace(' ', '_', $diklat->title)),
        ];
        MonitorLog::insert($datas);
        // session()->flash('success_msg', "Anda mendapat nilai {$total} dalam soal {$type} ini.");
        session()->flash('success_msg', "Anda Berhasil Mengerjakan  soal {$type} ini.");
        return back();
    }

    public function showCertificate(PDF $pdf, Request $request, $diklat_id, $user_id)
    {
        

        $user = User::findOrFail($user_id);
        if ($user->avatar == 'users/default.png') {
            session()->flash('warning_msg', "Anda harus mengganti foto profil anda terlebih dahulu.");
            return back();
        } else {
            try {
                if (!file_exists(storage_path('app/public/' . $user->avatar))) {
                    return dd("File photo user tidak ada, silahkan update terlebih dahulu photo user");
                }
                $diklat = Diklat::findOrFail($diklat_id);
                $detail = $user->getLatestDetailDiklat($diklat->id);
                $certificate = Certificate::whereDiklatId($diklat->id)
                    ->whereDiklatDetailId($detail->id)
                    ->whereUserId($user->id)
                    ->first();
                    $file = $certificate.'.pdf';
                    redirect(public_path('digital-signatures/',$file));
                // if (empty($certificate) || $certificate->no_certificate === 'empty') {
                //     return dd("Sertifikat anda belum tersedia, mohon menunggu konfirmasi dari admin.");
                // }
                $absensi = $certificate->no_absen;
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = 'LULUS';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $request->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 92.5 && (int) $request->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 85 && (int) $request->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 77.5 && (int) $request->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 70 && (int) $request->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = $status . ' dengan kualifikasi ' . strtoupper($nilai);
                    } elseif ((int) $request->nilai < 60) {
                        $nilai = 'Tidak Lulus';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else {
                    $kualifikasi = strtoupper($nilai);
                }
                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $kualifikasi,
                    'detail' => $detail,
                    'absensi' => $absensi,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
                // $dompdf = $pdf->loadView('frontend.sertificate.index', $data);
                // /* (Optional) Setup the paper size and orientation */
                // $dompdf->setPaper('a4', 'landscape');
                // /* Output the generated PDF to Browser */
                // return $dompdf->stream();
                // return view('frontend.sertificate.test', $data);
               return redirect(url('digital-signatures').'/'.$certificate->id.'.pdf');

            } catch (\Throwable $th) {
                dd($th);
                // return view('frontend.sertificate.test', $data);
            }
        }
    }

    public function showTranskip(PDF $pdf, Request $request, $diklat_id, $user_id)
    {
        $user = User::findOrFail($user_id);
        if ($user->avatar == 'users/default.png') {
            session()->flash('warning_msg', "Anda harus mengganti foto profil anda terlebih dahulu.");
            return back();
        } else {
            try {
                if (!file_exists(storage_path('app/public/' . $user->avatar))) {
                    return dd("File photo user tidak ada, silahkan update terlebih dahulu photo user");
                }
                $diklat = Diklat::findOrFail($diklat_id);
                $detail = $user->getLatestDetailDiklat($diklat->id);
                $certificate = Certificate::whereDiklatId($diklat->id)
                    ->whereDiklatDetailId($detail->id)
                    ->whereUserId($user->id)
                    ->first();
                if (empty($certificate) || $certificate->no_certificate === 'empty') {
                    return dd("Transkip nilai anda belum tersedia, mohon menunggu konfirmasi dari admin.");
                }
                $certificateSetting = CertificateSetting::first();
                $nilai = 'Telah Mengikuti';
                $status = 'LULUS';
                $kualifikasi = '';
                if ($diklat->category_id == 1) {
                    if ((int) $request->nilai >= 92.5) {
                        $nilai = 'Sangat Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 92.5 && (int) $request->nilai >= 85) {
                        $nilai = 'Memuaskan';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 85 && (int) $request->nilai >= 77.5) {
                        $nilai = 'Sangat Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 77.5 && (int) $request->nilai >= 70) {
                        $nilai = 'Baik';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $request->nilai <= 70 && (int) $request->nilai >= 60) {
                        $nilai = 'Cukup';
                        $kualifikasi = strtoupper($nilai);
                    } elseif ((int) $request->nilai < 60) {
                        $nilai = 'Tidak Lulus';
                        $kualifikasi = strtoupper($nilai);
                    }
                } else { $kualifikasi = strtoupper($nilai); }
                $data = [
                    'user' => $user,
                    'diklat' => $diklat,
                    'nilai' => $kualifikasi,
                    'detail' => $detail,
                    'certificate' => $certificate,
                    'certificateSetting' => $certificateSetting
                ];
                $dompdf = $pdf->loadView('frontend.sertificate.transkip', $data);
                /* (Optional) Setup the paper size and orientation */
                $dompdf->setPaper('a4');
                /* Output the generated PDF to Browser */
                return redirect(url('digital-signatures-transkip').'/'.$certificate->id.'.pdf');
                // return view('frontend.sertificate.test', $data);
            } catch (\Throwable $th) {
                dd($th);
                // return view('frontend.sertificate.test', $data);
            }
        }
    }

    public function uploadFoto(Request $request)
    {
        $user = User::findOrFail(auth()->id());
        if ($request->hasFile('file')) {
            $test = Storage::disk('local')->putFile('public/users', $request->file('file'));
            $user->avatar = str_replace('public/', '', $test);
            $user->save();
            session()->flash('success_msg', 'Anda Telah Berhasil Mengupdate Foto Profil.');
            return back();
        }
    }

    public function answerSurvey(Request $request, $id)
    {
        $user = auth()->user();
        foreach ($request->all() as $kr => $vr) {
           if($kr !== '_token'){
               SurveyFeedbackUser::insert(
     array(
            'user_id'     =>   $user->id, 
            'diklat_id'   =>   $id,
            'value'   =>   $vr,
            'survey_feedback_id' => $kr
     )

           
            
);
               }
           
          
           

        }
        session()->flash('success_msg', 'Terimakasih. Anda telah berhasil mengisi survey and feedback.');
        return back();

    }
    public function answerSurveyInstruktur(Request $request, $id)
    {
        
            
        // $user = auth()->user();
        
        // foreach ($request->all() as $kr => $vr) {
         

        //     if (substr($kr, 0, 4) == 'star') {
        //         $ex = explode('_', $kr);
        //         $ids = array_shift($ex);
        //         $survey = SurveyFeedbackInstruktur::findOrFail((int) substr($ids, 4));
        //         $instruktur_id = $request->instruktur_id;
              
        //         $survey->users()->attach($user->id, [
        //             'diklat_id' => $id,
        //             'value' => 10,
        //             'instruktur_id' => $instruktur_id
        //         ]);
        //     }
        // }
        DiklatInstruktur::where('diklat_id',$id)->where('instruktur_id',$request->instruktur_id)->update(['status'=>'no']);
         $user = auth()->user();
         foreach ($request->all() as $kr => $vr) {
            if($kr !== '_token' AND $kr !== 'instruktur_id'){
                SurveyFeedbackInstrukturUser::insert(
                    array(
                        'user_id'     =>   $user->id, 
                        'diklat_id'   =>   $id,
                        'value'   =>   $vr,
                        'survey_feedback_instruktur_id' => $kr,
                        'instruktur_id' => $request->instruktur_id
                    )



                );
            }
           
          
           

        }
        // $cek = DiklatInstruktur::where('diklat_id',$id)->where('instruktur_id',$request->instruktur_id)->first();
        // if()
        session()->flash('success_msg', 'Terimakasih. Anda telah berhasil mengisi survey and feedback.');
        return back();

    }
    function delWistyaHashedId($id)
    {
        
            $http = new Client;
            $response = $http->request('DELETE', "https://api.wistia.com/v1/medias/{$id}.json", [
                'debug' => true,
                'form_params' => [
                    'adminEmail' => 'babastudio.projects@gmail.com',
                    'api_password' => '056eb6c2b12015630a3649451fa30593bd70de820539cc733dd4b9fe14cf307e'
                ]
            ]);

            $res = json_decode($response->getStatusCode(), true);
            print_r($res);        
     
    }
    
}

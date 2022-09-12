<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Diklat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\DaerahKbupaten;
use App\DaerahProvinsi;
use App\User;

class DiklatsController extends Controller
{
    private $view;
    private $model;
    private $request;
    private $route;

    public function __construct(Diklat $diklat, Request $req)
    {
        $this->view = 'frontend.diklat';
        $this->model = $diklat;
        $this->request = $req;
        $this->route = 'diklat';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $diklat = $this->model->whereHas('mataDiklat', function(Builder $builder) {
            $builder;
        })->whereIsPublish(1)->orderByDesc('id')->paginate(9);
        return view("{$this->view}.all", compact('diklat', 'user'));
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
    public function show($id)
    {
        $user = Auth::user();
        $diklat = $this->model->with('diklatDetail', 'categoryDiklat', 'mataDiklat.sections.materials')->findOrFail($id);
        return view("{$this->view}.index", compact('diklat', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function edit(cr $cr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, cr $cr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function destroy(cr $cr)
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
}

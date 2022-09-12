<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Diklat;
use Illuminate\Database\Eloquent\Builder;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $diklat = Diklat::with('category')->whereHas('mataDiklat', function(Builder $builder){
            $builder;
        })->whereIsPublish(1)->orderByDesc('created_at')->take(7)->get();
        return view('frontend.home.index', compact(
            'diklat'
        ));
    }
}

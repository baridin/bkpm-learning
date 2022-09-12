<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Dept;
use App\Grade;
use App\Position;
use App\DaerahKbupaten;
use App\DaerahProvinsi;
use TCG\Voyager\Models\User;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        $dept = Dept::orderByDesc('id')->get();
        $grade = Grade::orderByDesc('id')->get();
        $position = Position::orderByDesc('id')->get();
        $kab = DaerahKbupaten::orderByDesc('id')->get();
        $prov = DaerahProvinsi::orderByDesc('id')->get();
        $user = null;
        if ($request->has('nip')) {
            $user = User::where('username', $request->get('nip'))->first();
            $view = view('auth.register-diklat', compact('dept', 'grade', 'position', 'kab', 'prov', 'user'));
        } // else will register asn or non asn
        else {
            $view = view('auth.register', compact('dept', 'grade', 'position', 'kab', 'prov', 'user'));
        }
        return $view;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        // if (Auth::check())
        //     Auth::logout();
        // else
        //     $this->guard()->login($user);
        //     Auth::logout();
        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}

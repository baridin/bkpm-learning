<?php

namespace App\Http\Controllers\Auth;

use App\AlumniUser;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth\RegistersUsers;
use Illuminate\Support\Facades\Crypt;
use App\Diklat;
use App\DiklatDetail;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $message = [
            'required' => 'Data :attribute harus di isi.',
            'unique' => 'Data :attribute sudah tersedia.',
            'string' => 'Data :attribute harus berupa text.'
        ];
        if (!array_key_exists('diklat_id', $data)) {
            $valid = [
                'name' => ['required', 'string'],
                'email' => ['required', 'string', 'unique:users'],
                'password' => ['required', 'string'],
                'kelamin' => ['required', 'string'],
                'birth_place' => ['required', 'string'],
                'birth_date' => ['required', 'string'],
                'mobile' => ['required', 'string'],
                'home_address' => ['required', 'string'],
                'home_city' => ['required', 'string'],
                'home_prov' => ['required', 'string'],
            ];
            if (array_key_exists('username', $data)) {
                $valid['username'] = ['required', 'string', 'unique:users'];
            }
            $validation = Validator::make($data, $valid, $message);
        } else {
            $validation =  Validator::make($data, [
                // 'diklat_id' => ['required', 'string'],
                // 'detail_id' => ['required', 'string'],
                'username' => ['required', 'string'],
                'name' => ['required', 'string'],
                'kelamin' => ['required', 'string'],
                'email' => ['required', 'string'],
                // 'password' => ['required', 'string'],
                'birth_place' => ['required', 'string'],
                'birth_date' => ['required', 'string'],
                'home_address' => ['required', 'string'],
                'home_city' => ['required', 'string'],
                'home_prov' => ['required', 'string'],
                'mobile' => ['required', 'string'],
                'boss_name' => ['required', 'string'],
                'boss_phone' => ['required', 'string'],
                'dept' => ['required', 'string'],
                'info_instansion' => ['required', 'string'],
                'info_instansion_detail' => ['required', 'string'],
                'office_address' => ['required', 'string'],
                'office_city' => ['required', 'string'],
                'office_prov' => ['required', 'string'],
                'office_phone' => ['required', 'string'],
                'position' => ['required', 'string'],
                'bagian' => ['required', 'string'],
                'grade' => ['required', 'string'],
            ], $message);
        }
        return $validation;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // dd($data);
        $user =  User::where('email', $data['email'])->first();
        if (array_key_exists('diklat_id', $data)) {
            $user = $this->updateUserDiklat($user, $data);
        } else {
            $user = $this->createNonAsn($data);
        }
        return $user;
    }

    function createNonAsn($data)
    {
        $cs = User::where('category_id', 0)->get()->count();
        $record = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'kelamin' => $data['kelamin'],
            'role_id' => 2,
            'password_encrypt' => Crypt::encryptString($data['password']),
            'home_address' => $data['home_address'],
            'home_city' => $data['home_city'],
            'home_prov' => $data['home_prov'],
            'birth_place' => $data['birth_place'],
            'birth_date' => "{$data['birth_date']}",
            'mobile' => $data['mobile'],
            'status' => 'normal'
        ];
        $type = '';
        if (array_key_exists('username', $data)) {
            $record['username'] = str_replace(' ', '', $data['username']);
            $record['category_id'] = 1;
            $type = 'NIP';
            $alumni = AlumniUser::whereUsername($data['username'])->orderByDesc('created_at')->first();
            if (!empty($alumni)) {
                $record['home_phone'] = $alumni->home_phone;
                $record['facebook'] = $alumni->facebook;
                // 'boss_name' = $alumni->boss_name;
                $record['boss_phone'] = $alumni->boss_phone;
                /* 'dept' = $alumni->dept;
                'info_instansion' = "{$alumni->info_instansion}",
                'info_instansion_detail' = "{$alumni->info_instansion_detail}", */
                $record['office_address'] = $alumni->office_address;
                // 'office_city' = $alumni->office_city;
                $record['office_prov'] = $alumni->office_prov;
                $record['office_phone'] = $alumni->office_phone;
                $record['office_fax'] = $alumni->office_fax;
                $record['website'] = $alumni->website;
                /* 'position' = $alumni->position;
                'bagian' = $alumni->bagian;
                'grade' = $alumni->grade; */
            }
        } else {
            $record['username'] = Carbon::now()->format('Ymd').str_pad(round($cs + 1), 3, '0', STR_PAD_LEFT);
            $record['category_id'] = 1;
            $type = 'Username';
        }

        $user = User::create($record);
        $mes = "<p><b>Silahkan gunakan {$type} dan Password tersebut untuk mendaftar Diklat E-Learning BKPM : </b><p>
        <p><b>{$type} : {$user->username}</b><p>
        <p><b>Password  : {$data['password']}</b><p>";
        Mail::send([], ['name', 'Admin Pendaftaran Diklat E-Learning BKPM'], function ($m) use ($user, $mes) {
            $m->to($user->email);
            $m->subject('Permintaan Pendaftaran Akun Website E-Learning BKPM Anda di Setujui.');
            $m->setBody((string)view('vendor.mail.custom-mail')->withMes($mes)->withUser($user), 'text/html');
        });
        session()->flash('success_msg', "Anda Berhasil melakukan pendaftaran akun website, Silahkan gunakan {$type} yang kami kirim ke email anda sebagai pendaftaran diklat.");
        return $user;
    }

    function updateUserDiklat($user, array $data)
    {
        $data['status'] = 'pending';
        $data['email_verified_at'] = now();
        $data['role_id'] = 2;
        $dataAdd = $data;
        $dataExcept = Arr::except($dataAdd, ["_token", "diklat_id", "detail_id"]);
        $user->update($dataExcept);
        if (empty($user->getDiklat()->where('diklat_id', (int)$data['diklat_id'])->count())) {
            $diklat = $user->getDiklat()->attach((int)$data['diklat_id']);
            $detail = $user->getDiklatDetail()->attach((int)$data['detail_id'], [
                'diklat_id' => $data['diklat_id'],
                'status' => 10,
                'file' => null
            ]);
            session()->flash('success_msg', 'Anda telah berhasil mendaftar Diklat E-Learning BKPM. Silahkan tunggu konfirmasi email pendaftaran dari panitia.');
        } else {
            session()->flash('info_msg', 'Anda sudah melakukan pengajuan untuk mengikuti diklat ini.');
        }
        return $user;
        // SELECT diklat_users.user_id, diklat_users.diklat_id, COUNT(diklat_id) AS ud FROM diklat_users GROUP BY diklat_users.user_id, diklat_users.diklat_id HAVING ud > 1
    }
}

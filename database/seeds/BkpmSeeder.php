<?php

use App\AlumniUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\User;
use App\Dept;
use App\Grade;
use App\Position;

class BkpmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dept = Storage::disk('local')->get('data\depts.json');
        $grade = Storage::disk('local')->get('data\grades.json');
        $post = Storage::disk('local')->get('data\positions.json');
        $alumni = Storage::disk('local')->get('data\dataalumni.json');
        // $alumni = Storage::disk('local')->get('data\datapeserta.json');

        foreach (json_decode($dept) as $kd => $vd) {
            Dept::firstOrNew([
                'title' => $vd->title
            ]);
        }

        foreach (json_decode($grade) as $kg => $vg) {
            Grade::firstOrNew([
                'title' => $vg->title
            ]);
        }

        foreach (json_decode($post) as $kp => $vp) {
            Position::firstOrNew([
                'title' => $vp->title
            ]);
        }
        // dd(json_decode($alumni));
        foreach (json_decode($alumni) as $ku => $vu) {
            if (!empty($vu->email) && $vu->email != '' && filter_var($vu->email, FILTER_VALIDATE_EMAIL) && !empty($vu->nama)) {
                $user = AlumniUser::updateOrCreate(
                    ['email' => $vu->email, 'username' => str_replace(' ', '', $vu->nip)],
                    [
                        'category_id' => 1,
                        'name' => $vu->nama,
                        'facebook' => $vu->facebook,
                        'birth_place' => null,
                        'birth_date' => now(),
                        'home_address' => $vu->rumah,
                        'home_city' => $vu->rumah_kota,
                        'home_prov' => $vu->rumah_prov,
                        'home_phone' => $vu->rumah_telp,
                        'mobile' => exploded($vu->hp_telp),
                        'boss_name' => null,
                        'boss_phone' => $vu->hpatasan,
                        'dept' => null,
                        'info_instansion' => null,
                        'office_address' => exploded($vu->kantor_alamat),
                        'office_city' => exploded($vu->kantor_kota),
                        'office_prov' => exploded($vu->kantor_prov),
                        'office_phone' => exploded($vu->kantor_telp),
                        'office_fax' => exploded($vu->fax),
                        'website' => exploded($vu->website),
                        'position' => null,
                        'bagian' => null,
                        'grade' => null,
                        'status' => 'normal'
                    ]
                );
            }
        }
    }
}

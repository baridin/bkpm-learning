<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\DaerahJenis;
use App\DaerahProvinsi;
use App\DaerahKbupaten;
use App\DaerahKcamatan;
use App\DaerahKlurahan;

class DaerahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenis = Storage::disk('local')->get('data\jenis.json', true);
        $prov = Storage::disk('local')->get('data\provinsi.json', true);
        $kab = Storage::disk('local')->get('data\kabupaten.json', true);
        $kec = Storage::disk('local')->get('data\kecamatan.json', true);
        $kel = Storage::disk('local')->get('data\kelurahan.json', true);

        foreach(json_decode($jenis) as $kj => $vj) {
            DaerahJenis::create([
                'nama' => $vj->nama
            ]);
        }

        foreach(json_decode($prov) as $kp => $vp) {
            $pv = DaerahProvinsi::create([
                'nama' => $vp->nama
            ]);
        }

        foreach(json_decode($kab) as $kb => $vb) {
            $p = DaerahProvinsi::whereNama($vb->pv_nama)->first();
            if (!empty($p)) {
                DaerahKbupaten::create([
                    'daerah_provinsi_id' => $p->id,
                    'nama' => $vb->kb_nama,
                    'daerah_jenis_id' => $vb->id_jenis
                ]);
            }
        }

        foreach(json_decode($kec) as $kc => $vc) {
            $k = DaerahKbupaten::whereNama($vc->kb_nama)->first();
            if (!empty($k)) {
                DaerahKcamatan::create([
                    'daerah_kbupaten_id' => $k->id,
                    'nama' => $vc->kc_nama
                ]);
            }
        }

        foreach(json_decode($kel) as $kl => $vl) {
            $c = DaerahKcamatan::whereNama($vl->kc_nama)->first();
            if (!empty($c)) {
                DaerahKlurahan::create([
                    'daerah_kcamatan_id' => $c->id,
                    'nama' => $vl->kl_nama,
                    'daerah_jenis_id' => $vl->id_jenis
                ]);
            }
        }
    }
}

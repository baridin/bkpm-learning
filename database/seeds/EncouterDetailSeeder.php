<?php

use App\Encouter;
use App\EncouterDetail;
use App\MataDiklat;
use Illuminate\Database\Seeder;

class EncouterDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $encouterDetails = EncouterDetail::orderBy('id')->get();
        $details = [];
        $myDetails = [];
        foreach ($encouterDetails as $kcd => $vcd) {
            if ($vcd->type !== 'essay') {
                if ($vcd->key === 'soal') {
                    $myDetails['id'] = $vcd->id;
                } else {
                    if ($vcd->key !== 'true') {
                        $myDetails['details']['options'][strtolower($vcd->key)] = $vcd->value;
                    } else {
                        $myDetails['details']['is_true'] = strtolower($vcd->value);
                        array_push($details, $myDetails);
                        $myDetails = [];
                    }
                    $encouterDetail = EncouterDetail::find($vcd->id);
                    if (!empty($encouterDetail->id)) $encouterDetail->delete();
                }
            }
        }
        foreach (collect($details) as $kd => $vd) {
            $encouterDetail = EncouterDetail::find($vd['id']);
            $encouterDetail->update(['details' => json_encode($vd['details'])]);
            dump("Success Update Encouter Detail ID : $encouterDetail->id");
        }
    }
}

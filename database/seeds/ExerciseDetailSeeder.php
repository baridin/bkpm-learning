<?php

use App\ExerciseDetail;
use Illuminate\Database\Seeder;

class ExerciseDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exerciseDetails = ExerciseDetail::orderBy('id')->get();
        $details = [];
        $myDetails = [];
        foreach ($exerciseDetails as $kcd => $vcd) {
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
                $encouterDetail = ExerciseDetail::find($vcd->id);
                if (!empty($encouterDetail->id)) $encouterDetail->delete();
            }
        }
        foreach (collect($details) as $kd => $vd) {
            $exerciseDetail = ExerciseDetail::find($vd['id']);
            $exerciseDetail->update(['details' => json_encode($vd['details'])]);
            dump("Success Update Exercise Detail ID : $exerciseDetail->id");
        }
    }
}

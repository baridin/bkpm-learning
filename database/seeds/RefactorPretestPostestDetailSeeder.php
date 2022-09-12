<?php

use App\BankSoal;
use App\BankSoalBackup;
use App\Encouter;
use App\Exercise;
use App\Postest;
use App\Pretest;
use Illuminate\Database\Seeder;

class RefactorPretestPostestDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // pretest postest
        /* $pretests = Pretest::orderByDesc('created_at')->with('details')->get();
        $postests = Postest::orderByDesc('created_at')->with('details')->get();

        $pretestDetails = [];
        foreach ($postests as $kpr => $vpr) {
            foreach ($vpr->details as $kprd => $vprd) {
                $pretestDetails[] = [
                    'pretest_id' => $vpr->id,
                    'question' => strip_tags($vprd->question),
                    'type_soal' => 'pg',
                    'details' => json_encode([
                        'options' => [
                            'a' => strip_tags($vprd->option_a),
                            'b' => strip_tags($vprd->option_b),
                            'c' => strip_tags($vprd->option_c),
                            'd' => strip_tags($vprd->option_d),
                        ],
                        'is_true' => strtolower($vprd->option_true)
                    ])
                ];
                $vprd->update([
                    'details' => json_encode([
                        'options' => [
                            'a' => strip_tags($vprd->option_a),
                            'b' => strip_tags($vprd->option_b),
                            'c' => strip_tags($vprd->option_c),
                            'd' => strip_tags($vprd->option_d),
                        ],
                        'is_true' => strtolower($vprd->option_true)
                    ]),
                    'type_soal' => 'pg',
                ]);
            }
        }
        
        $pretestDetailFilters = unique_multidim_array($pretestDetails, 'question');

        foreach ($pretestDetailFilters as $kpf => $vpf) {
            $data = (object)$vpf;
            BankSoal::create([
                'type_soal' => $data->type_soal,
                'type' => 'postest',
                'soal' => $data->question,
                'details' => $data->details,
            ]);
        } */

        /* $encouters = Encouter::orderByDesc('created_at')->with('details')->get();
        $exercises = Exercise::orderByDesc('created_at')->with('details')->get();

        $dataDetails = [];
        foreach ($exercises as $ven) {
            foreach ($ven->details as $vend) {
                $dataDetails[] = [
                    'soal' => strip_tags($vend->value),
                    'type' => 'pg',
                    'details' => $vend->details
                ];
            }
        }

        $dataDetailFilters = unique_multidim_array($dataDetails, 'soal');

        foreach ($dataDetailFilters as $vpf) {
            $data = (object)$vpf;
            BankSoal::create([
                'type_soal' => $data->type,
                'type' => 'latihan',
                'soal' => $data->soal,
                'details' => $data->details,
            ]);
        } */
    }
}

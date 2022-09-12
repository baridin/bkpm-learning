<?php

use App\BankSoal;
use App\Encouter;
use App\EncouterDetail;
use App\Exercise;
use App\ExerciseDetail;
use Illuminate\Database\Seeder;

class RefactorBankSoalWithMataDiklat extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bankSoalModels = BankSoal::query();
        $bankSoals = $bankSoalModels->orderByDesc('created_at')->get();
        foreach ($bankSoals->groupBy('type') as $kb => $vb) {
            if ($kb === 'latihan') {
                foreach ($vb as $vs) {
                    $this->migrateLatihan($vs);
                }
            }
            else if ($kb === 'ujian') {
                foreach ($vb as $vs) {
                    $this->migrateUjian($vs);
                }
            }
        }
    }

    protected function migrateUjian(BankSoal $bankSoal)
    {
        $encouterModels = Encouter::query();
        $encouterDetailModels = EncouterDetail::query();
        $findSoal = $encouterDetailModels->whereValue($bankSoal->soal)->first();
        if (!empty($findSoal)) {
            $findParent = $encouterModels->find($findSoal->encouter_id);
            if (!empty($findParent)) {
                $updateData = array_merge($bankSoal->toArray(), [
                    'mata_diklat_id' => $findParent->mata_diklat_id
                ]);
                $bankSoal->update($updateData);
                dump("Success Ujian update mata_diklat_id: {$findParent->mata_diklat_id}");
            }
        }
    }

    protected function migrateLatihan(BankSoal $bankSoal)
    {
        $exerciseModels = Exercise::query();
        $exerciseDetailModels = ExerciseDetail::query();
        $findSoal = $exerciseDetailModels->whereValue($bankSoal->soal)->first();
        if (!empty($findSoal)) {
            $findParent = $exerciseModels->find($findSoal->exercise_id);
            if (!empty($findParent)) {
                $updateData = array_merge($bankSoal->toArray(), [
                    'mata_diklat_id' => $findParent->mata_diklat_id
                ]);
                $bankSoal->update($updateData);
                dump("Success Latihan update mata_diklat_id: {$findParent->mata_diklat_id}");
            }
        }
    }
}

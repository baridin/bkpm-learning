<?php

use App\Certificate;
use Illuminate\Database\Seeder;

class RemoveDuplicateCertificate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $certificateModels = Certificate::query();
        $certificates = $certificateModels->orderByDesc('created_at')->get();
        $certificateGroups = $certificates->groupBy('no_certificate');
        foreach ($certificateGroups as $vc) {
            if ($vc->count() > 1) {
                $lastItem = $vc->first();
                foreach ($vc as $value) {
                    if ($value->id !== $lastItem->id) {
                        $value->delete();
                        dump($value->id, 'deleted');
                    }
                }
            }
        }
    }
}

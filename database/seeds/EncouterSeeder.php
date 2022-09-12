<?php

use App\Encouter;
use Illuminate\Database\Seeder;

class EncouterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $encouterIds = Encouter::orderBy('id')->distinct('mata_diklat_id')->pluck('mata_diklat_id')->toArray();
        foreach ($encouterIds as $ke => $ve) {
            $findIdByMId = Encouter::whereMataDiklatId($ve)->first();
            if ($findIdByMId) $findIdByMId->delete();
        }
    }
}

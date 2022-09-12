<?php

namespace App\Imports;

use App\Certificate;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Diklat;

use App\DiklatDetail;
use App\CertificateSetting;
class NoCertificateImport implements ToCollection, WithHeadingRow
{
    /**
     * @var int $diklat_id
     */
    private $diklat_id;

    /**
     * @var int $diklat_detail_id
     */
    private $diklat_detail_id;

    /**
    * @param int $diklat_id
    * @param int $diklat_detail_id
    */
    public function __construct(int $diklat_id, int $diklat_detail_id) {
        $this->diklat_id = $diklat_id;
        $this->diklat_detail_id = $diklat_detail_id;
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)  {
            $nip = isset($row['nip']) ? $row['nip'] : Arr::first($row);
            $nomor_sertifikat = isset($row['nomor_sertifikat']) ? $row['nomor_sertifikat'] : Arr::last($row);
            $user = User::whereUsername($nip)->first();
            if (!empty($user)) {
                $certificate = Certificate::whereDiklatId($this->diklat_id)
                    ->whereDiklatDetailId($this->diklat_detail_id)
                    ->whereUserId($user->id)
                    ->first();
                // $sertif = Certificate::where('diklat_id','=',$this->diklat_id)
                //     ->where('diklat_detail_id','=',$this->diklat_detail_id)
                //     ->where('user_id','=',$user->id)
                //     ->first();
                
                if (!empty($certificate)) {
                    
                    $certificate->update(['no_certificate' => $nomor_sertifikat]);
                                        
                    
                }
            }
        }
    }
     
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Certificate;
use App\CertificateSetting;
use App\Diklat;
use App\DiklatDetail;
use Illuminate\Http\Request;
use App\Page;
use App\User;
use Illuminate\Support\Facades\Crypt;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $page = Page::whereSlug($slug)->first();
        return view('frontend.pages.index', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkCertificate(string $no)
    {
        try {
            $idCer = Crypt::decryptString($no);
            $certificate = Certificate::findOrFail($idCer);
            $user = User::findOrFail($certificate->user_id);
            $diklat = Diklat::findOrFail($certificate->diklat_id);
            $detail = DiklatDetail::findOrFail($certificate->diklat_detail_id);
            $certificateSetting = CertificateSetting::first();
            $nilai = 'Telah Mengikuti';
            $status = 'LULUS';
            $kualifikasi = '';
            if ($diklat->category_id == 1) {
                if ((int)$certificate->nilai >= 92.5) {
                    $nilai = 'Sangat Memuaskan';
                    $kualifikasi = $status.' dengan kualifikasi '.strtoupper($nilai);
                } elseif ((int)$certificate->nilai <= 92.5 && (int)$certificate->nilai >= 85) {
                    $nilai = 'Memuaskan';
                    $kualifikasi = $status.' dengan kualifikasi '.strtoupper($nilai);
                } elseif ((int)$certificate->nilai <= 85 && (int)$certificate->nilai >= 77.5) {
                    $nilai = 'Sangat Baik';
                    $kualifikasi = $status.' dengan kualifikasi '.strtoupper($nilai);
                } elseif ((int)$certificate->nilai <= 77.5 && (int)$certificate->nilai >= 70) {
                    $nilai = 'Baik';
                    $kualifikasi = $status.' dengan kualifikasi '.strtoupper($nilai);
                } elseif ((int)$certificate->nilai <= 70 && (int)$certificate->nilai >= 60) {
                    $nilai = 'Cukup';
                    $kualifikasi = $status.' dengan kualifikasi '.strtoupper($nilai);
                } elseif ((int)$certificate->nilai < 60) {
                    $nilai = 'Tidak Lulus';
                    $kualifikasi = strtoupper($nilai);
                }
            } else {
                $kualifikasi = strtoupper($nilai);
            }
            $data = [
                'user' => $user,
                'diklat' => $diklat,
                'nilai' => $kualifikasi,
                'detail' => $detail,
                'absensi' => $certificate->no_absen,
                'certificate' => $certificate,
                'certificateSetting' => $certificateSetting
            ];
            // // $dompdf = $pdf->loadView('frontend.sertificate.index', $data);
            // // (Optional) Setup the paper size and orientation 
            // // $dompdf->setPaper('legal', 'landscape'); 
            // // Output the generated PDF to Browser        
            // // return $dompdf->stream();
            return view('frontend.sertificate.index', $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

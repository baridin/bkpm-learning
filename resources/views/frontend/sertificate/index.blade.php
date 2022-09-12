
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{$certificate->no_certificate}}</title>
    {{-- <link rel="stylesheet" href="style.css" media="all" /> --}}
    <style media="all">
        @page {
          margin-top: 25px;
          margin-bottom: 0px;
        }
        .clearfix:after {
          content: "";
          display: table;
          clear: both;
        }

        a {
          color: #5D6975;
          text-decoration: underline;
        }

        body {
          position: relative;
          width: 25cm;
          height: 15cm;
          margin: 0 auto;
          color: #000000;
          background: #FFFFFF;
          font-family: Arial, sans-serif;
          font-size: 12px;
          font-family: Arial;
        }

        header {
          padding: 10px 0;
          margin-bottom: 30px;
        }

        #logo {
          text-align: center;
          margin-bottom: 10px;
        }

        #logo img {
          width: 150px;
        }

        h1 {
          color: #000;
          font-size: 2.4em;
          line-height: 1.4em;
          font-weight: normal;
          text-align: center;
          margin: 0 0 20px 0;
          background: url(dimension.png);
        }

        #project {
          float: left;
        }

        #project span {
          color: #5D6975;
          text-align: right;
          width: 52px;
          margin-right: 10px;
          display: inline-block;
          font-size: 0.8em;
        }

        #company {
          float: right;
          text-align: right;
        }

        #project div,
        #company div {
          white-space: nowrap;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
          margin-bottom: 20px;
        }

        /* table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        } */

        table th,
        table td {
          text-align: center;
        }

        table th {
          padding: 5px 20px;
          white-space: nowrap;
          font-weight: normal;
        }

        table .service,
        table .desc {
          text-align: left;
        }

        table td {
          padding: 5px;
          text-align: left;
        }

        table td.service,
        table td.desc {
          vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
          font-size: 1.2em;
        }

        table td.grand {
          border-top: 1px solid #5D6975;
        }

        #notices .notice {
          color: #5D6975;
          font-size: 1.2em;
        }

        footer {
          color: #5D6975;
          width: 100%;
          height: 30px;
          position: absolute;
          bottom: 0;
          border-top: 1px solid #C1CED9;
          padding: 8px 0;
          text-align: center;
        }
        .text-center {
          text-align: center;
        }
        .img-fluid {
          width: 110px;
          float: left;
          vertical-align: middle;
          padding-left: 20px;
        }
        .float-left {
          float: left;
        }
        .text-weight-bold {
          font-weight: bold;
        }
        .clearfix {
          clear: both;
        }
        .header-ttd {
          margin-bottom: 10px
        }
        .header-ttd::after {
          content: 'TTD';
        }
        .page-break {
          page-break-after: always;
        }
        .grid-container {
  display: grid;
  grid-template-columns: auto auto auto;
  background-color: #2196F3;
  padding: 10px;
}
.grid-item {
  background-color: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(0, 0, 0, 0.8);
  padding: 20px;
  font-size: 30px;
  text-align: center;
}
    </style>
  </head>
  <body>


    @php
      
 
 
    
        $qr_url = route('check-certificate', Crypt::encryptString($certificate->id));
    @endphp
    <header class="clearfix" style="padding: 0 50px;">
      <div id="logo">
        <table style="width: 100%;">
          <tr style="justify-content: center;">
            <td style="width: 10%;"></td>
            <td style="width: 80%;"><center><img  src="{{imagetobase(public_path('storage/'.$certificateSetting->logo))}}"></center></td>
            <td style="width: 10%;"></td>
          </tr>
        </table>
        
        
      </div>
      <h2 class="text-center" style="margin-top:-25px">SERTIFIKAT</h2>
      <h3 class="text-center"><i>No: {{$certificate->no_certificate}}</i></h3>
      <h3 class="text-center">Diberikan Kepada:</h3>
      <div id="project">
        <table>
          <thead>
            <tr>
              <td>
                  <img src="{{imagetobase(storage_path('app/public/'.$user->avatar))}}" alt="Peserta" class="img-fluid">
              </td>
              <td>
                <table style="margin-top: -10px; font-size: 14px;" class="text-weight-bold">
                  <thead>
                    <tr>
                      <td style="text-align: left;">Nama</td>
                      <td>:</td>
                      <td style="text-align: left;">{{ucwords($user->name)}}</td>
                    </tr>
                    {{-- Hide NIP if user is asn --}}
                    @if ($user->category_id === 1) 
                      <tr>
                        <td style="text-align: left;">NIP</td>
                        <td>:</td>
                        <td style="text-align: left;">{{$user->username}}</td>
                      </tr>
                    @endif
                    <tr>
                      <td style="text-align: left;">Instansi</td>
                      <td>:</td>
                      <td style="text-align: left;">{{ strtoupper($user->dept).' '.strtoupper($user->info_instansion).' '. str_replace('KAB. ', '', str_replace('KAB ', '', str_replace('KOTA ', '', str_replace('KOTA ADM. ', '', str_replace('KAB. ADM. ', '', strtoupper($user->info_instansion_detail)))))) }} </td>
                    </tr>
                    <tr>
                      <td style="text-align: left;">Pangkat/Golongan</td>
                      <td>:</td>
                      <td style="text-align: left;">{{strtoupper($user->grade)}}</td>
                    </tr>
                    <tr>
                      <td style="text-align: left;">Jabatan</td>
                      <td>:</td>
                      <td style="text-align: left;">{{strtoupper($user->position)}}</td>
                    </tr>
                  </thead>
                </table>
              </td>
            </tr>
          </thead>
        </table>
      </div>
      <div class="clearfix"></div>
      <div class="text-center">
       
          
        

        <h2 style="margin-top: -25px;" class="text-weight-bold">
          
          Lulus dengan kualifikasi


       </h2>
       <h2 style="margin-top: -5px;" class="text-weight-bold">
          
          {{ $nilai }}


       </h2>

       @php 
       $tanggal1 = Tanggal::indo_full(\Carbon\Carbon::parse($detail->start_at)->format('Y-m-d'));
       $pecah1 =  explode(' ', $tanggal1);
       
       $tanggal2 = Tanggal::indo_full(\Carbon\Carbon::parse($detail->end_at)->format('Y-m-d'));
        $pecah2 =  explode(' ', $tanggal2);
       
       @endphp

        <p style="font-size: 16px; margin: 0px 20px; text-align: center;">
          
          Pada {{ucwords($diklat->title)}}  Angkatan Ke- {{integerToRoman($detail->force)}}</b> yang
          diselenggarakan {{$diklat->custom_name()}} oleh Pusat Pendidikan dan Pelatihan Kementerian
          Investasi/BKPM
          pada tanggal
          @if($pecah1[1] == $pecah2[1])
           {{ Tanggal::tanggal_saja(\Carbon\Carbon::parse($detail->start_at)->format('Y-m-d'))}} 
          @elseif($pecah1[2] == $pecah2[2])
          {{ Tanggal::tanggal_bulan(\Carbon\Carbon::parse($detail->start_at)->format('Y-m-d'))}} 
          
          @else
           {{ Tanggal::indo_full(\Carbon\Carbon::parse($detail->start_at)->format('Y-m-d'))}}
           @endif
            s.d. {{ Tanggal::indo_full(\Carbon\Carbon::parse($detail->end_at)->format('Y-m-d'))}}  dengan {{$diklat->duration}} Jam Pelajaran.
        </p>
      </div>
      <table  style="margin-top: 0px; margin-bottom: 0px;">
        <tbody>
          <tr>
            <td style="vertical-align: baseline; padding-top: 25px;">
              <div class="">
                <p style="font-size: 11px;font-family: sans-serif;">Berdasarkan Akreditasi LAN No. {{$certificateSetting->berdasar_akreditasi}}<br>Dokumen ini telah ditandatangani secara elektronik<br> menggunakan sertifikat elektronik yang diterbitkan oleh BSrE-BSSN</p>
                <img width="200" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvyO253dMyI_AIzMFrWY2d9Vh7g-pUiCZTxgM2Yj7IMsgsLxpFYROb8BsW1hAYMorzN7k&usqp=CAU">
              </div>
            </td>
            <td>
              <div class="text-center">
                <div style="vertical-align: middle;">
                  <p style="font-size: 13px;font-family: sans-serif;">Jakarta, {{ Tanggal::indo_full(\Carbon\Carbon::parse($detail->end_at)->format('Y-m-d'))}}</p>
                  
                  <p style="font-weight: 600;font-size: 13px;font-family: sans-serif; margin-top: -12px;">KEPALA  PUSAT PENDIDIKAN DAN PELATIHAN<br>KEMENTERIAN INVESTASI/BKPM</p>
                  {{-- <p style="font-weight: 600;font-size: 13px;font-family: sans-serif;"></p> --}}
                </div>
                <div>
                  <img style="width: 65px;"  src="data:image/png;base64,{{DNS2D::getBarcodePNG("$qr_url/", "QRCODE")}}" />
                </div>
                <div>
                  <p style="font-size: 13px;text-decoration: underline;margin-bottom: 2px;font-weight: 600;font-family: sans-serif;">{{$certificateSetting->kepala_pusdiklat}}</p>
                  <p style="font-size: 13px;margin-bottom: 0px;font-weight: 600;margin-top: 0px;font-family: sans-serif;">NIP. {{$certificateSetting->nip_kepala_pusdiklat}}</p>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="page-break"></div>
      <h1 style="padding: 30px;">DAFTAR MATA DIKLAT</h1>
      <table style="padding: 30px;">
        <tbody>
          @php $i = 0; @endphp
          @foreach ($diklat->mataDiklat as $m)
            <tr>
              <td style="text-align: left; padding: 12px;">
                <span class="text-weight-bold" style="font-size: 20px; margin: 5px auto;">{{++$i}}. {{ucwords($m->title)}}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </header>
    {{-- <div class="page-break"></div> --}}
  </body>
</html>

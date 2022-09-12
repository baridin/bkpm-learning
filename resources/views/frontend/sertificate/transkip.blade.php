<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transkip Nilai {{$certificate->no_certificate}}</title>
  <style media="all">
    @page {
      margin-top: 0px;
      margin-bottom: 0px;
    }
    body {
      margin: 0 auto;
      color: #000000;
      background: #FFFFFF;
      font-family: Arial, sans-serif;
      font-size: 12px;
      font-family: Arial;
      background-image: url("{{asset('frontend/images/bg-transkip-nilai.png')}}");
      background-repeat: no-repeat;
      background-position: center;
    }

    .debug {
      border: 1px solid red;
    }
    #app {
      padding-top: 15px;
      padding-bottom: 0px;
    }
    #title-doc {
      padding-top: 10px;
      text-align: center;
      line-height: 10px;
    }

    .padding-y-10 {
      padding: 10px 0px;
    }
    .padding-y-20 {
      padding: 20px 0px;
    }
    .padding-y-30 {
      padding: 30px 0px;
    }
    #identity {
      align-content: center;
      align-items: center;
      text-align: center;
    }
    #identity-left {
      float: left;
      width: 50%;
    }
    #identity-left table td {
      vertical-align: top;
    }
    #identity-right {
      margin-left: 50%;
    }
    #identity-right table td {
      vertical-align: top;
    }
    #table-data table,
    #table-data table th,
    #table-data table td
    {
      border-collapse: collapse;
      border: 1px solid black;
      padding: 5px;
    }

    #table-data table th {
      background-color: gainsboro;
    }
    .space-1 {
      padding: 0px 10px;
    }

    .text-center {
      text-align: center;
    }
    .text-justify {
      text-align: justify;
    }
    .font-bold {
      font-weight: bold;
    }
    /*.header-ttd {
      margin-bottom: 10px
    }*/
    .header-ttd::after {
      /*content: 'TTD';*/
    }
    #logo {
      text-align: center;
    }

    #logo img {
      width: 125px;
    }
    #header-doc {
      display: flex;
      align-content: center;
      align-items: center
    }
    .text-head {
      line-height: 3px;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 10px;
      color: #3a5e8c;
    }
    .title-head {
      font-family: Arial, Helvetica, sans-serif;
      color: #3a5e8c;
    }
    .clearfix {
      overflow: auto;
    }
    .page-break {
      page-break-after: always;
    }
    .page-break-active {
      padding-top: 150px;
    }
  </style>
</head>
<body>
  <div id="app">
    <div id="logo">
      <img src="{{imagetobase(public_path('storage/'.$certificateSetting->logo_transkip))}}">
    </div>
    <div id="title-doc">
      <h2>Transkip Nilai</h2>
      <h5>No.: {{$certificate->no_certificate}}</h5>
    </div>
    <div id="body-doc" class="padding-y-10">
      <section class="padding-y-10">
        <table width="100%">
        {{--   <thead style="text-align: left;">
            <tr>
              <th style="width: 10%; font-weight: normal;">Nama</th>
              <th style="width: 5%; font-weight: normal;">:</th>
              <th style="width: 30%; font-weight: normal;">{{$user->name}}</th>
              <th style="width: 10%; font-weight: normal;">Metode</th>
              <th style="width: 5%; font-weight: normal;">:</th>
              <th style="width: 30%; font-weight: normal;">{{$diklat->method()}}</th>
            </tr>
          </thead> --}}
          <tbody>
            <tr style="vertical-align: middle;">
              <td>Nama</td>
              <td>:</td>
              <td>{{$user->name}}</td>
              <td>Metode</td>
              <td>:</td>
              <td>{{$diklat->custom_name()}}</td>
            </tr>
            @if ($user->category_id === 1)
            <tr style="vertical-align: middle;">
              <td>NIP</td>
              <td>:</td>
              <td>{{$user->username}}</td>
              <td>Pelatihan</td>
              <td>:</td>
              <td>{{"$diklat->title $detail->title"}}</td>
            </tr>
            @endif
            <tr style="vertical-align: middle;">
              <td>Instansi</td>
              <td>:</td>
              <td>{{ ucwords(strtolower($user->dept)).' '.ucwords(strtolower($user->info_instansion).' '. str_replace('kab. ', '', str_replace('kab ', '', str_replace('kota ', '', str_replace('kota adm. ', '', str_replace('kab. adm. ', '', strtolower($user->info_instansion_detail))))))) }}</td>
              @if ($user->category_id !== 1)
              <td>Pelatihan</td>
              <td>:</td>
              <td>{{"$diklat->title $detail->title"}}</td>
              @endif
            </tr>
          </tbody>
        </table>
      </section>
      <div class="clearfix"></div>
      <section id="table-data" class="padding-y-10">
        <table style="width: 100%;">
          <thead style="text-align: left;">
            <tr>
              <th style="width: 5%;">No.</th>
              <th style="width: 70%;">Aspek Penguasaan Materi</th>
              <th>JP</th>
              <th>Nilai</th>
            </tr>
          </thead>
          <tbody>
             @php $sum = 0; $nomor=1;  @endphp
            @php $i = 0; @endphp
            @foreach ($diklat->mataDiklat as $mataDiklat)
              <tr>
                <td>{{++$i}}</td>
                <td>{{$mataDiklat->title}} </td>
                <td>{{$mataDiklat->duration}}</td>
                @php
                  $encouter = App\Encouter::where('mata_diklat_id',$mataDiklat->id)->where('diklat_id',$diklat->id)->first();
                  $cek_remedial = App\EncouterUser::where('user_id',$user->id)->where('encouter_id',$encouter->id)->first();
                @endphp
                
                
                 <!--  <td>{{ $cek_remedial->assesment }}</td> -->
                  <td>{!! $cek_remedial->assesment !!}</td>


                @php 
                        $sum+=       $cek_remedial->assesment;
                        $jumlah = $sum / $nomor++;

                        @endphp
                
              </tr>
            @endforeach
            <tr>
              <td colspan="3" class="text-center font-bold">Jumlah</td>
              
              <td class="font-bold">
                {{$jumlah}}
              </td>
            </tr>
          </tbody>
        </table>
      </section>
      <section id="detail-data">
        <table>
          <tbody>
            <tr>
              <td>Jumlah</td>
              <td class="space-1">:</td>
              {{-- <td>{{$diklat->getNilai($user->id)}}</td> --}}
              <td>{{$jumlah}}</td> 
            </tr>
            <tr>
              <td>Kualifikasi Kelulusan</td>
              <td class="space-1">:</td>
              <td>{{$nilai}}</td>
            </tr>
            <tr>
              <td>Tanggal Kelulusan</td>
              <td class="space-1">:</td>
              <td>{{strftime('%d %B %Y', strtotime($detail->end_at))}}</td>
            </tr>
          </tbody>
        </table>
        <h4>Keterangan</h4>
        <table>
          <tbody>
            <tr>
              <td>Metode</td>
              <td class="space-1">:</td>
              <td style="width: 60%;">{{$diklat->custom_name()}}</td>
            </tr>
            <tr>
              <td>JP</td>
              <td class="space-1">:</td>
              <td>Jam Pelajaran</td>
            </tr>
          </tbody>
        </table>
        @if (count($diklat->mataDiklat) > 9)
          <div class="page-break"></div>
        @endif
        <div class="{!!count($diklat->mataDiklat) > 9 ? 'page-break-active' : ''!!}">
          <h4>Kualifikasi Kelulusan</h4>
          <table>
            <tbody>
              <tr>
                <td>a)</td>
                <td>Lulus Sangat Memuaskan (Skor: 92,5–100)</td>
              </tr>
              <tr>
                <td>b)</td>
                <td>Lulus Memuaskan (Skor: 85,0–92,4)</td>
              </tr>
              <tr>
                <td>c)</td>
                <td>Lulus Sangat Baik (Skor 77,5-84,9)</td>
              </tr>
              <tr>
                <td>d)</td>
                <td>Lulus Baik (Skor: 70,0–77,4)</td>
              </tr>
              <tr>
                <td>e)</td>
                <td>Lulus Cukup (Skor: 60,0–69,9)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
       @php
      
 
 
    
        $qr_url = route('check-transkip', Crypt::encryptString($certificate->id));
    @endphp
    <div id="footer" class="padding-y-10">
      <table style="width: 100%;">
        <tbody>
          <tr>
            <td style="vertical-align: baseline; padding-top: 45px;">
              <div class="">
                <p style="font-size: 11px;font-family: sans-serif;">Berdasarkan Akreditasi LAN No. {{$certificateSetting->berdasar_akreditasi}}<br>Dokumen ini telah ditandatangani secara elektronik<br> menggunakan sertifikat elektronik yang diterbitkan oleh BSrE-BSSN</p>
               <img width="200" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvyO253dMyI_AIzMFrWY2d9Vh7g-pUiCZTxgM2Yj7IMsgsLxpFYROb8BsW1hAYMorzN7k&usqp=CAU">
              </div>
            </td>
            {{-- <td style="width: 10%;"></td> --}}
            <td >
              <div class="text-center">
                <div class="header-ttd" style="vertical-align: middle;">
                  
                  <p style="margin-top: 0;margin-bottom: 2px;font-family: sans-serif;">Jakarta, {{ Tanggal::indo_full(\Carbon\Carbon::parse($detail->end_at)->format('Y-m-d'))}}</p>
                  <p style="font-weight: 600;font-size: 11px;font-family: sans-serif;margin-bottom: 2px;">KEPALA PUSAT PENDIDIKAN DAN PELATIHAN</p>
                  <p style="font-weight: 600;font-size: 11px;font-family: sans-serif;margin: 0px; margin-bottom: 10px;">KEMENTERIAN INVESTASI/BKPM</p>
                  
                  <img src="data:image/png;base64,{{DNS2D::getBarcodePNG("$qr_url/", "QRCODE")}}" style="width: 65px;" />
                  <br>
                </div>
                <div>
                  <p style="margin-top: 5px; font-size: 11px;text-decoration: underline;margin-bottom: 2px;font-weight: 600;font-family: sans-serif;">{{$certificateSetting->kepala_pusdiklat}}</p>
                  <p style="font-size: 11px;margin-bottom: 0px;font-weight: 600;margin-top: 0px;font-family: sans-serif;">NIP. {{$certificateSetting->nip_kepala_pusdiklat}}</p>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
@extends('frontend.main')

@section('content')
<div class="detail-courses">
    <div class="all-course">
        <div class="breadcrumb">
            <div class="container">
                <h2>{{ucwords($diklat->title)}}</h2>
                <p>
                    @if ($diklat->category_id == 1)
                        ASN
                    @else
                        ASN dan Non ASN
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="inner__detail">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-12">
                    <div class="inner__content">
                        <div class="inner__desc">
                            <h4 class="curiculum">Jadwal dan Waktu Pelaksanaan Diklat</h4>
                            <div class="expand_accordion">
                                <div class="panel-group" id="accordions" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-default">
                                            @foreach ($diklat->diklatDetail as $dd)
                                            @php
                                                $st = \Carbon\Carbon::parse($dd->online_at)->year;
                                                $sc = \Carbon\Carbon::now();
                                                $sn = $sc->year;
                                                // ->subDays(1)
                                                $sb = \Carbon\Carbon::parse($dd->online_at);

                                                if (($sc < $sb) && ($sn == $st)==true) {
                                                    $clas = 'info';
                                                    $txt = 'pendaftaran terbuka';
                                                    $ds = '';
                                                    $btn = 'Daftar';
                                                } else {
                                                    $clas = 'danger';
                                                    $txt = 'pendaftaran tutup';
                                                    $ds = 'disabled';
                                                    $btn = 'Daftar';
                                                }

                                                if(($user->username == 'pusdiklat_bkpm')) {
                                                    $clas = 'info';
                                                    $txt = 'lihat diklat';
                                                    $ds = '';
                                                    $btn = 'Lihat';
                                                }
                                            @endphp
                                            <div class="panel-heading" role="tab" id="heading{{$dd->id}}">
                                                <h3 class="panel-title">
                                                    <a class="accordion-toggle collapsed" role="button">
                                                        {{ucwords($dd->title)}}
                                                        <span class="badge badge-{{$clas}} pull-right">{{ucwords($txt)}}</span>
                                                    </a>
                                                </h3>
                                            </div>
                                            <div id="materi{{$dd->id}}" class="panel-collapse in" role="tabpanel"
                                                aria-labelledby="heading58">
                                                <div class="panel-body">
                                                    <ul class="materi__listings">
                                                        <li class="watching">
                                                            <ul class="inner__listing">
                                                                <li class="font-weight-bold"><i class="fa fa-calendar-o"
                                                                        aria-hidden="true"></i></li>
                                                                <li style="width: 20%;" class="font-weight-bold"><a
                                                                        href="javascript:" class="course-modul-videos"
                                                                        data-id="58"><span
                                                                            class="text-center">Pelaksanaan</span><br>
                                                                            {{\Carbon\Carbon::parse($dd->start_at)->format('d M Y')}} - {{\Carbon\Carbon::parse($dd->end_at)->format('d M Y')}}</a></li>
                                                                <li style="width: 25%;" class="font-weight-bold"><a
                                                                        href="javascript:" class="course-modul-videos"
                                                                        data-id="58"><span
                                                                            class="text-center">Akhir Pendaftaran</span><br>{{\Carbon\Carbon::parse($dd->online_at)->format('d M Y')}}</a></li>
                                                                <li style="width: 10%; vertical-align: middle;"
                                                                    class="font-weight-bold"><a href="javascript:"
                                                                        class="course-modul-videos" data-id="58"><span
                                                                            class="text-center">Kuota</span><br>{{round($dd->kuota)}}</a>
                                                                </li>
                                                                <li style="width: 10%; vertical-align: middle;"
                                                                    class="font-weight-bold"><a href="javascript:"
                                                                        class="course-modul-videos" data-id="58"><span
                                                                            class="text-center">Angkatan</span><br>{{integerToRoman($dd->force)}}</a>
                                                                </li>
                                                                <li style="width: 10%; vertical-align: middle;"
                                                                    class="font-weight-bold"><a href="javascript:"
                                                                        class="course-modul-videos" data-id="58"><span
                                                                            class="text-center">Peserta</span><br>{{$dd->users()->wherePivot('status', 2)->count()}}</a>
                                                                </li>
                                                                <li style="width: 10%;"
                                                                    class="font-weight-bold btn-daftar">
                                                                    <a  
                                                                        @php
                                                                            $talbe = 0;
                                                                            if(auth()->check())
                                                                            {
                                                                                $talbe = \Illuminate\Support\Facades\DB::table('diklat_detail_user_sementara')->where('user_id', $user->id)
                                                                                    ->where('diklat_id', $dd->diklat_id)
                                                                                    ->where('diklat_detail_id', $dd->id)
                                                                                    ->count();
                                                                            }
                                                                        @endphp
                                                                        @if($user->username == 'pusdiklat_bkpm')
                                                                            href="{{route('my-course.show', [$diklat->id])}}"
                                                                        @endif
                                                                        @if($talbe > 0)
                                                                            onclick="swal('Gagal!', 'Anda Sudah Pernah Mengikuti diklat ini', 'error')"

                                                                            @php
                                                                                $ds = 'disabled';    
                                                                            @endphp
                                                                        @else
                                                                            @if ($diklat->category_id == 1)
                                                                                @if (auth()->check())
                                                                                    @if (auth()->user()->category_id == 1)
                                                                                        href="{{route('register', ['nip' => auth()->user()->username, 'diklat' => $diklat->id, 'detail' => $dd->id])}}"
                                                                                    @else
                                                                                        onclick="swal('Gagal!', 'Diklat Ini Hanya Bisa di Ambil Peserta ASN', 'error')"
                                                                                    @endif
                                                                                @else
                                                                                    href="{{route('login')}}"
                                                                                @endif
                                                                            @endif
                                                                            @if ($diklat->category_id == 0)
                                                                                @if (auth()->check())
                                                                                    href="{{route('register', ['nip' => auth()->user()->username, 'diklat' => $diklat->id, 'detail' => $dd->id])}}"
                                                                                @else
                                                                                    href="{{route('login')}}"
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                        {{-- onclick="show_nip(event, {{$diklat->id}}, {{$dd->id}})" --}}
                                                                        class=" btn btn-primary btn-sm text-light {{$ds}}">{{$btn}}</a>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="inner__desc">
                            <h4>Deskripsi</h4>
                            {!!$diklat->description!!}
                        </div>
                        <div class="inner__desc">
                            <h4>Syarat mengikuti diklat ini</h4>
                            <div class="terms">
                                {!!$diklat->requirement!!}
                            </div>
                        </div>
                        <div class="inner__desc">
                            <h4>Hasil yang didapat dari diklat ini</h4>
                            <div class="terms">
                                {!!$diklat->can_be!!}
                            </div>
                        </div>
                        <div class="inner__desc">
                            <h4 class="curiculum">Curriculum</h4>
                            <ul class="title-time">
                                {{-- <li><a href="#">Expand All</a></li> --}}
                                <li><span class="lect">165 Lectures</span></li>
                                <li><span class="time_">10:01:41</span></li>
                            </ul>
                            <div class="expand_accordion">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        @foreach ($diklat->mataDiklat as $dm)
                                            <div class="panel-heading" role="tab" id="headingOne">
                                                <h3 class="panel-title">
                                                    <a class="accordion-toggle collapsed" role="button"
                                                        data-toggle="collapse" data-parent="#accordion" href="#materi{{$dm->id}}"
                                                        aria-expanded="true" aria-controls="materi{{$dm->id}}">
                                                        {{ucwords($dm->title)}}
                                                    </a>
                                                </h3>
                                            </div>
                                            <div id="materi{{$dm->id}}" class="panel-collapse collapse in" role="tabpanel"
                                            aria-labelledby="headingOne">
                                                <div class="panel-body">
                                                    @foreach ($dm->sections as $ds)
                                                    <ul class="materi__listings">
                                                        <li class="watching">
                                                            <ul class="inner__listing">
                                                                <li>
                                                                    <ion-icon name="book"></ion-icon>
                                                                </li>
                                                                <li><a href="#">{{ucwords($ds->title)}}</a></li>
                                                                {{-- <li><a href="#" data-toggle="modal"
                                                                        data-target="#videoPreview">Preview</a></li> --}}
                                                                {{-- <li><span>02:42</span></li> --}}
                                                            </ul>
                                                        </li>
                                                        @foreach ($ds->materials as $dm)
                                                        <li class="{{($dm->status == 1 && $dm->type == 'video')?'watching':'membership'}}">
                                                            <ul class="inner__listing">
                                                                <li>
                                                                    <ion-icon name="play-circle"></ion-icon>
                                                                </li>
                                                                <li><a href="#" data-toggle="{{($dm->status == 1 && $dm->type == 'video')?'modal':''}}"
                                                                        data-target="{{($dm->status == 1 && $dm->type == 'video')?'#videoPreview':''}}">{{ucwords($dm->title)}}</a></li>
                                                                <li><a href="#" data-toggle="{{($dm->status == 1 && $dm->type == 'video')?'modal':''}}"
                                                                        data-target="{{($dm->status == 1 && $dm->type == 'video')?'#videoPreview':''}}">Preview</a></li>
                                                                {{-- <li><span>02:42</span></li> --}}
                                                            </ul>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade video" id="videoPreview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="blue">Course Preview:</span> Learn HTML5
                    Programming From Scratch</h4>
            </div>
            <div class="modal-body">
                <div class="video">
                    <iframe width="100%" height="341"
                        src="https://www.youtube.com/embed/8eDuupJ9Uus?autoplay=0&amp;loop=0&amp;showinfo=0&amp;theme=dark&amp;color=red&amp;controls=1&amp;modestbranding=0&amp;start=0&amp;fs=1&amp;iv_load_policy=1&amp;wmode=transparent&amp;rel=1"
                        frameborder="0" allow="autoplay; encrypted-media" frameborder="0" allowfullscreen=""></iframe>
                </div>
                <div class="list_video">
                    <h3 class="title">Free sample videos:</h3>
                    <div class="list__videos">
                        <ul>
                            <li>
                                <ul class="inner__list">
                                    <li class="vid">
                                        <iframe width="100%" height="101"
                                            src="https://www.youtube.com/embed/8eDuupJ9Uus?ecver=1" frameborder="0"
                                            allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                    </li>
                                    <li class="info__video">
                                        <p>
                                            <ion-icon name="play-circle"></ion-icon> Learn HTML5 Programming From
                                            Scratch
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <ul class="inner__list">
                                    <li class="vid">
                                        <iframe width="100%" height="101"
                                            src="https://www.youtube.com/embed/8eDuupJ9Uus?ecver=1" frameborder="0"
                                            allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                    </li>
                                    <li class="info__video">
                                        <p>
                                            <ion-icon name="play-circle"></ion-icon> Basic of Facebook Ads
                                        </p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNip" tabindex="-1" role="dialog" aria-labelledby="modalNipTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNipTitle">Check NIP Anda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="number" min="0" class="form-control" name="nip" maxlength="8"
                                placeholder="00000000">
                        </div>-
                        <div class="col-md-3">
                            <input type="number" min="0" class="form-control" name="nip1" maxlength="6"
                                placeholder="000000">
                        </div>-
                        <div class="col-md-2">
                            <input type="number" min="0" class="form-control" name="nip2" maxlength="1" placeholder="0">
                        </div>-
                        <div class="col-md-3">
                            <input type="number" min="0" class="form-control" name="nip3" maxlength="3"
                                placeholder="000">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cek_nip" data-id="" data-eid="" class="btn btn-primary">Cek NIP</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('push_js')
    <script type="text/javascript">
        $(function(){
            $('#cek_nip').on('click', ()=>{
                $('#modalNip').modal('hide')
                let nip = `${$('input[name="nip"]').val()}${$('input[name="nip1"]').val()}${$('input[name="nip2"]').val()}${$('input[name="nip3"]').val()}`
                // console.log($('input[name="nip"]').val());

                let id = $('#cek_nip').attr('data-id')
                let e_id = $('#cek_nip').attr('data-eid')
                let formData = new FormData()
                formData.append('nip', nip)
                formData.append('diklat', id)
                // console.log(nip);
            
                $.ajax({
                    type: "POST",
                    url: `{!!url('my-course/check-nip')!!}`,
                    data: formData,
                    success: function (xml, data, xhr) {
                        console.log(xml, data, xhr);
                        if (xhr.status == 200) {
                            swal("NIP Anda Di Temukan!", "Silahkan ubah data pribadi anda di langkah berikutnya. Klik OKE untuk melanjutkan.", "info").
                            then((val)=>{
                                window.open(`{!!url('register')!!}?diklat_id=${id}&detail_id=${e_id}&nip=${nip}`, `_blank`)
                            })
                        } else if (xhr.status == 208) {
                            swal("NIP Anda Belum Terdaftar!", `Silahkan menambahkan data pribadi anda di langkah berikutnya. Klik OKE untuk melanjutkan.`, "info").
                            then((val)=>{
                                window.open(`{!!route('register')!!}`, `_blank`)
                            })
                        } else if (xhr.status == 207) {
                            swal("Diklat Harus Berurutan!", `Anda harus mengerjakan diklat-diklat berikut terlebih dahulu: ${xml.toString()}`, "warning")
                        }
                    },
                    error: function (error) {
                        swal("Gagal !", "Sistem Error!", "error");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            })
        })
        function show_nip(e, id, e_id)
        {
            e.preventDefault()
            $('#cek_nip').attr('data-id', id)
            $('#cek_nip').attr('data-eid', e_id)
            $('#modalNip').modal('show')
        }
    </script>
@endpush

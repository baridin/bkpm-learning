@extends('frontend.main')

@section('content')
<div class="page course">
    <div class="container">
        <div class="certificate__box-top">
            <div class="certificate__text">
                <h5><a href="detail-kursus.html">{{ucwords($mata->title)}}</a></h5>
                {{-- <span>2 of 5 items complete</span> --}}
            </div>
            <div class="icon-quiz">
                <a href="#" data-toggle="modal" data-target="#quiz">
                    <img src="{{asset('frontend/assets/img/icon-quiz.png')}}" alt="" class="img-fluid">
                    <span class="text">Detail</span>
                </a>
            </div>
            <div class="certificate__info">
                <p class="vid">Materi : {{($mata->countMaterial($user->id) > $mata->countMaterial())?$mata->countMaterial():$mata->countMaterial($user->id)}}/{{$mata->countMaterial()}}</p>
                {{-- <p class="date">Kuis : {{($mata->countQuizz($user->id) > $mata->countQuizz())?$mata->countQuizz():$mata->countQuizz($user->id)}}/{{$mata->countQuizz()}}</p> --}}
            </div>
            <div class="certificate__info">
                <p class="vid">Latihan : {{($mata->countExercise($user->id) > $mata->countExercise())?$mata->countExercise():$mata->countExercise($user->id)}}/{{$mata->countExercise()}}</p>
                <p class="date">Virtual Class : {{($mata->countVirtualClass($user->id, 'user', $diklat->id) > $mata->countVirtualClass($user->id, null, $diklat->id))?$mata->countVirtualClass($user->id, null, $diklat->id):$mata->countVirtualClass($user->id, 'user', $diklat->id)}}/{{$mata->countVirtualClass($user->id, null, $diklat->id)}}</p>
            </div>
            <div class="certificate__info">
                <p class="date">Ujian : {{($mata->countEncounter($user->id, 'user', $diklat->id) > $mata->countEncounter($user->id, null, $diklat->id))?$mata->countEncounter($user->id, null, $diklat->id):$mata->countEncounter($user->id, 'user', $diklat->id)}}/{{$mata->countEncounter($user->id, null, $diklat->id)}}</p>
            </div>
            <div class="certificate__info">
                
                @php
                    $data_array = [];    
                @endphp

                @if($type == 'virtual-class')
                    @php
                        $absen = new \App\VirtualClassAbsent;
                        $vclass = new \App\MonitorLog;
                        $vt = new \App\VirtualClass;
                        $get_data = $vt->where('id',$data->id)->first();
                                $type_detail1 =    'LOG_MENGIKUTI_VIRTUAL_CLASS_' . strtoupper(str_replace(' ', '_', $get_data->title));

                       $cek_gabung = $vclass->where('item_id',$data->id)->where('user_id',$user->id)->where('type_detail',$type_detail1)->first();

                        



                        



                        $cek = $absen->where('virtual_class_id', $data->id)->where('user_id', $user->id)->first();

                        $data_array = ['cek' => $cek, 'absensi' => $data->absensi];
                    @endphp

                    <button id="absensi-btn" class="btn btn-primary"
                       @if(isset($cek_gabung))
                            
                                {{' onclick=openmodal() '}}
                            
                        @else
                             
                        @if($data->absensi > 0)
                            @if(empty($cek))
                                {{' onclick=openmodal() '}}
                            @else
                                {{'disabled'}}
                            @endif
                        @else
                            {{'disabled'}}
                        @endif
                        {{'disabled'}}
                        @endif
                     >Absensi</button>
                @endif
            </div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: {{$mata->status($user->id, $diklat->id)}}%" aria-valuenow="{{$mata->status($user->id, $diklat->id)}}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

        <div class="content__page">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="sidebar__content">
                        <div class="sidebar__title">
                            <h5>Video</h5>
                        </div>
                        @include('frontend.my-course.mata-diklat.sidebar')
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="video__tron">
                         @includeWhen(get_class($data) == 'App\\Material', 'frontend.my-course.mata-diklat.partial.material')
                        @includeWhen(get_class($data) == 'App\\Exercise', 'frontend.my-course.mata-diklat.partial.latihan')
                        @includeWhen(get_class($data) == 'App\\ModulTambahan', 'frontend.my-course.mata-diklat.partial.modultambahan')
                        @includeWhen(get_class($data) == 'App\\Encouter', 'frontend.my-course.mata-diklat.partial.ujian')
                        @includeWhen(get_class($data) == 'App\\VirtualClass', 'frontend.my-course.mata-diklat.partial.virtualclass', $data_array)
                    </div>
                    @includeWhen(get_class($data) == 'App\\Material', 'frontend.my-course.mata-diklat.partial.reference', ['refer' => $refer])
                </div>
            </div>
        </div>
    </div>
</div>
<div id="place_quiz"></div>
@endsection

@push('push_js')

<div class="modal fade" id="modal-absen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-absen">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Absen {{ $mata->title }} {{ date('Y-m-d') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama"> Nama</label>
                                <input type="text" class="form-control" id="nama" placeholder="Nama" readonly value="{{ $user->name }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="jabatan"> Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" placeholder="Jabatan" readonly value="{{ $user->position }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="instansi"> Instansi</label>
                                <input type="text" class="form-control" id="instansi" placeholder="Instansi" readonly value="{{ $user->dept }} - {{ $user->office_city }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tandatangan"> Tanda Tangan</label>
                                <div class="text-center">
                                    <canvas id="signature" style="border: 1px solid black;"></canvas>
                                    <br>
                                    <button type="button" onclick="clearCanvas()" class="btn btn-danger">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Hadir</button>
                </div>
            </form>
        </div>
    </div>
</div>
 <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <img src="..." class="rounded mr-2" alt="...">
      <strong class="mr-auto">Bootstrap</strong>
      <small>11 mins ago</small>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="{{asset('signature/js/signature_pad.umd.js')}}"></script>
<script type="text/javascript">

    function openmodal()
    {
        $('#modal-absen').modal('show')
    }

    let signature = document.getElementById('signature')
    let signaturePad = new SignaturePad(signature, {
        backgroundColor: 'rgb(255, 255, 255)'
    })

    function clearCanvas()
    {
        signaturePad.clear()
    }


    function dataURLToBlob(dataURL) 
    {
        var parts = dataURL.split(';base64,');
        var contentType = parts[0].split(":")[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;
        var uInt8Array = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], { type: contentType });
    }

    $('#form-absen').submit(e =>
    {
        e.preventDefault()

        let file = signaturePad.toDataURL("image/jpeg")
        file = dataURLToBlob(file)
        file = new File([file], 'signature.jpeg')

        let fd = new FormData()

        fd.append('_token', $('meta[name="csrf-token"]').attr('content'))
        @if($type == 'virtual-class')
            fd.append('virtual_class_id', {{ $data->id }})
            fd.append('mata_diklat_id', '{{ $data->mata_diklat_id }}')
            fd.append('diklat_detail_id', '{{ $data->diklat_detail_id }}')
        @endif

        fd.append('diklat_id', {{ $diklat->id }})
        fd.append('file', file)
        
        $.ajax({
            url: '{!!route('voyager.absen.hadir')!!}',
            method: 'post',
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            async: false,
            success(data)
            {
                // alert('Absen Berhasil Dikirim')
                location.reload()
            },
            error($xhr)
            {
                console.log($xhr)
            }
        })
    })
</script>
@endpush

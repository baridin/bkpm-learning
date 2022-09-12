@extends('frontend.main')

@push('push_css')

<link rel="stylesheet" type="text/css" href="{{ asset('css/rating.css') }}">

<style type="text/css">
.m-progress-bar {
    min-height: 1em;
    background: #c12d2d;
    width: 5%;
}
}
.rate {
    float: left;
    height: 46px;
    padding: 0 10px;
}
.rate:not(:checked) > input {
    position:absolute;
    top:-9999px;
}
.rate:not(:checked) > label {
    float:right;
    width:1em;
    overflow:hidden;
    white-space:nowrap;
    cursor:pointer;
    font-size:30px;
    color:#ccc;
}
.rate:not(:checked) > label:before {
    content: '★ ';
}
.rate > input:checked ~ label {
    color: #ffc700;    
}
.rate:not(:checked) > label:hover,
.rate:not(:checked) > label:hover ~ label {
    color: #deb217;  
}
.rate > input:checked + label:hover,
.rate > input:checked + label:hover ~ label,
.rate > input:checked ~ label:hover,
.rate > input:checked ~ label:hover ~ label,
.rate > label:hover ~ input:checked ~ label {
    color: #c59b08;
}

</style>


@endpush


@section('content')
<div class="package">
    <div class="container">
        <div class="title">
            <h2>{{ucwords($diklat->title)}}</h2>
        </div>
        <div class="box package__time_course">
            <div class="author">
                <img 
                @if (auth()->user()->avatar == 'users/default.png')
                    src="{{Avatar::create(auth()->user()->name)->toBase64()}}"
                @else
                    src="{{asset('storage/'.auth()->user()->avatar)}}"
                @endif
                class="rounded-circle img-fluid" alt="authot" />
            </div>
            <div class="package__description_bar">
                <span class="name">{{ucwords(auth()->user()->name)}}</span>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="{{$diklat->getProgress()}}" aria-valuemin="0"
                        aria-valuemax="100" style="width: {{$diklat->getProgress()}}%;">
                        {{$diklat->getProgress()}}%
                    </div>
                </div>
            </div>
            <div class="button__package">
                <button type="button" name="button">Nilai: {{$nilai}}</button>
            </div>
            @php 
            $get_angkatan = App\DiklatDetailUser::where('diklat_id',$diklat->id)->where('user_id',auth()->user()->id)->first(); 
            $get_sertif = App\Certificate::where('user_id',auth()->user()->id)->where('diklat_id',$diklat->id)->where('diklat_detail_id',$get_angkatan->diklat_detail_id)->first();

            @endphp
            <div class="button__package btn-group">
                @if(!empty($get_sertif))
                    @if($get_sertif->status == 1)
                        <button
                        class="btn dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        type="button" name="button"><i class="fa fa-download" aria-hidden="true"></i> Download</button>
                    @else

                    @endif
                 
                @else


                @endif
               
                <ul class="dropdown-menu" style="padding: 0px 10px;">
                    <li>
                        <a href="javascript:"
                            @if (is_null($survey) && $nilai > 0 && auth()->user()->avatar != 'users/default.png')
                                onclick="document.getElementById('generate-certificate').submit()"
                            @else
                                class="disabled" onclick="swal( 'Info!' ,  'Maaf anda belum menyelesaikan diklat ini atau belum mengupload foto terbaru anda.' ,  'info' )"
                            @endif
                        >Sertifikat</a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="javascript:"
                            @if (is_null($survey) && $nilai > 0 && auth()->user()->avatar != 'users/default.png')
                                onclick="document.getElementById('generate-transkip').submit()"
                            @else
                                class="disabled" onclick="swal( 'Info!' ,  'Maaf anda belum menyelesaikan diklat ini atau belum mengupload foto terbaru anda.' ,  'info' )"
                            @endif
                        >Transkip Nilai</a>
                    </li>
                </ul>
            </div>
        </div>
        <form id="generate-certificate" action="{{route('show.certificate', [$diklat->id, auth()->id()])}}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="nilai" value="{{$nilai}}">
        </form>
        <form id="generate-transkip" action="{{route('show.transkip', [$diklat->id, auth()->id()])}}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="nilai" value="{{$nilai}}">
        </form>
        <div class="box_package__step">
            <div class="box step1">
                <p>Di bawah ini adalah urutan Mata Diklat yang harus anda pelajari</p>
            </div>
            <div class="box step3">
                <p class="text-right">{{count($diklat->mataDiklat)}} Materi</p>
                <div class="slider-section inner__desc">
                    <div class="container">
                        <div class="slider__content">
                            <div class="slider__item">
                                <div id="slider__item_list" class="slider-class">
                                    <div class="owl-carousel slider__low_item_package owl-theme">
                                        @php
                                            $i = 0;
                                            $next = [];
                                            $n_title = [];
                                        @endphp
                                        @foreach ($diklat->mataDiklat as $k => $m)
                                            @if (count($m->sections)>0 && count($m->sections->first()->materials)>0)
                                                <div class="item">
                                                    <ul class="step_by_step">
                                                        <li><span class="number">{{++$i}}<span></li>
                                                    </ul>
                                                    {{-- <ul class="step_by_step">
                                                        <li class="play"><span class="icon number"><i class="fa fa-check"
                                                                    aria-hidden="true"></i></span></li>
                                                    </ul> --}}
                                                    <ul class="list__item_materi">
                                                        <li>
                                                            <div class="box__materi">
                                                                <div class="image">
                                                                    <img src="{{asset('storage/'.$m->image)}}" alt="materi"
                                                                        class="img-fluid">
                                                                </div>
                                                                <div class="box__description">
                                                                    <span class="info">by BKPM | <span
                                                                            class="glyphicon glyphicon-time"></span> {{$m->created_at->format('d M Y')}}</span>
                                                                    <p class="title">{{ucwords($m->title)}}</p>
                                                                    <div class="progress">
                                                                        <div class="progress-bar half-process"
                                                                            role="progressbar" aria-valuenow="100"
                                                                            aria-valuemin="0" aria-valuemax="{{$m->status(null, $diklat->id)}}"
                                                                            style="width: {{$m->status(null, $diklat->id)}}%;">
                                                                            <span class="sr-only">{{$m->status(null, $diklat->id)}}% Complete</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="button">
                                                                        @if ($m->status(null, $diklat->id) < 100)
                                                                            <button onclick="window.location.href = `{{route('showMaterial', [$diklat->id, $m->id, 'material', $m->sections->first()->id, $m->sections->first()->materials->first()->id])}}`" type="button" name="button" class="btn-progress half-process">Belajar</button>
                                                                        @else
                                                                            <button onclick="window.location.href = `{{route('showMaterial', [$diklat->id, $m->id, 'material', $m->sections->first()->id, $m->sections->first()->materials->first()->id])}}`" type="button" name="button" class="btn-progress half-process">Selesai</button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        {{-- <div class="item">
                                            <ul class="step_by_step">
                                                <li><span class="number">2<span></li>
                                            </ul>
                                            <ul class="list__item_materi">
                                                <li>
                                                    <div class="box__materi">
                                                        <div class="image">
                                                            <img src="assets/img/list_content_materi.jpg" alt="materi"
                                                                class="img-fluid">
                                                        </div>
                                                        <div class="box__description">
                                                            <span class="info">by Babastudio | <span
                                                                    class="glyphicon glyphicon-time"></span> 10 Juli
                                                                2018</span>
                                                            <p class="title">HTML & CSS</p>
                                                            <p class="info__course"><i class="fa fa-users"
                                                                    aria-hidden="true"></i> 4,491 murid</p>
                                                            <div class="progress">
                                                                <div class="progress-bar half-process"
                                                                    role="progressbar" aria-valuenow="100"
                                                                    aria-valuemin="0" aria-valuemax="70"
                                                                    style="width: 70%;">
                                                                    <span class="sr-only">60% Complete</span>
                                                                </div>
                                                            </div>
                                                            <div class="button">
                                                                <button type="button" name="button"
                                                                    class="btn-progress half-process">Belajar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div> --}}
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
@if (!is_null($pretest) && $pretest->details->count()>0)
    <!-- modal test -->
    <div class="modal modal-test fade hide" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <div class="desktop bg-post-test">
                <div class="tagline">PRETEST</div>
              </div>
            </div>
            <form class="needs-validation" novalidate action="{{route('answer.prepost', ['pretest', $diklat->id, $pretest->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-body postest">
                <div class="row">
                @php $i = 0; @endphp
                @foreach ($pretest->details as $e)
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    
                    <div class="media">
                      <div class="media-body">
                        <div style="display: flex;">
                            <span class="pull-left mr-2">{{++$i}}. </span>
                            <div>{!!$e->question!!}</div>
                        </div>
                        <div class="">
                             
                              
                            @foreach ($e->details->options as $ke => $ve)
                                        
                            @if($ve !== null)                                
                                <div class="form-check" >
                                    
                                    <input id="validationFormCheck2" class="form-check-input" required=""   type="radio" name="answer{{$e->id}}"  value="{{$ke}}" >
                                    <label for="validationFormCheck2" class="form-check-label" >
                                        {!!$ve!!}
                                    </label>
                                    
                                     <div class="invalid-feedback">Jawaban tidak boleh kosong</div>
                                </div>
                                @endif


                                


                               
                                
                            @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
                </div>
                <div class="btnSubmit colfeedback" style="padding: 15px 0;">
                  <button class="btnFeedback" type="text">KIRIM</button>
                </div>
            </div>
            </form>
          </div>
        </div>
    </div>
    <!-- end -->
    @endif
    @if (is_null($pretest) && !is_null($postest) && $postest->details->count() > 0 && $diklat->getProgress() >= 100 && $diklat->getScore() > 60)
    <!-- modal test -->
    <div class="modal modal-test fade hide" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <div class="desktop bg-post-test">
                <div class="tagline">POSTEST</div>
              </div>
            </div>
            <form class="needs-validation" novalidate action="{{route('answer.prepost', ['postest', $diklat->id, $postest->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-body postest">
                <div class="row">
                @php $i = 0; @endphp
                @foreach ($postest->details as $o)
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="media">
                      <div class="media-body">
                        <div style="display: flex;">
                            <span class="pull-left mr-2">{{++$i}}. </span>
                            <div id="sapa">{!!$o->question!!} 
                                
                            </div>
                        </div>
                        <div class="">
                            
                             @foreach ($o->details->options as $ke => $ve)
                             @if($ve !== null)        
                                <div class="form-check">
                                    <input id="validationFormCheck2" class="form-check-input clasanswera{{$o->id}}" required="" type="radio" name="answer{{$o->id}}" id="answer{{$o->id}}" value="{{$ke}}" >
                                    <label class="form-check-label" for="validationFormCheck2">
                                        {!!$ve!!}
                                    </label>
                                    <div class="invalid-feedback">Jawaban tidak boleh kosong</div>
                                   
                                </div>
                                @endif
                                                       
                            @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
                </div>
                <div class="btnSubmit colfeedback">
                  <button class="btnFeedback" type="text">Kirim</button>
                </div>
            </div>
            </form>
          </div>
        </div>
    </div>
    <!-- end -->
    @endif
    
     @if (is_null($postest) && !empty($survey) && $diklat->getProgress() >= 100 && $diklat->getScore() > 60)
        <!-- modal test -->
        <div class="modal modal-test fade hide" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document" style="max-width: 82%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="desktop bg-post-test">
                            <div class="tagline">EVALUASI PENYELENGGARA</div>
                        </div>
                    </div>
                    <form  action="{{route('answer.survey', [$diklat->id])}}" method="post"  enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body postest">
                            
                                <div class="row ratings">
                                     <p id="testinglagi"></p>
                                    @php $ss = 1; @endphp
                                    @forelse ($survey as $s)
                                    @php $nomor3 =  $ss++; @endphp
                                    <div class="col-lg-6">
                                        <div class="media">
                                             
                                            <h3>{{$nomor3}}.</h3>
                                            <div class="media-body">
                                                <p>{!!$s->question!!}</p>
                                                
                                                <br>
                                                <div class="form-field">
                                                     @php
                                                            $kata = [10 => 'memuaskan', 9 => 'memuaskan', 8 => 'baik sekali', 7 => 'baik', 6 => 'cukup', 5 => 'kurang', 4 => 'kurang', 3 => 'kurang', 2 => 'kurang', 1 => 'kurang'];
                                                        @endphp
                                                   
                                                    <select required="" oninvalid="InvalidMsg(this);" oninput="InvalidMsg(this);" id="glsr-ltr" name="{{$s->id}}" class="star-rating" >
                                                        <option value="">Pilih Rating</option>

                                                        <option value="10">Memuaskan</option>
                                                        <option value="9">Memuaskan</option>
                                                        <option value="8">Baik Sekali</option>
                                                        <option value="7">Baik</option>
                                                        <option value="6">Cukup</option>
                                                        <option value="5">Kurang</option>
                                                        <option value="4">Kurang</option>
                                                        <option value="3">Kurang</option>
                                                        <option value="2">Kurang</option>
                                                        <option value="1">Kurang</option>
                                                    </select>
                                                   
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    @empty
                                    @endforelse
                                </div>
                                
                                <div class="btnSubmit colfeedback">
                                    <button type="submit" id="submite" class="btnFeedback">KIRIM</button>
                                   
                                </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end -->
    @endif

     @if (is_null($postest) && is_null($survey) && !empty($survey_instruktur)  && $diklat->getProgress() >= 100 && $diklat->getScore() > 60)
        @if(!empty($get_instruktur))
       @php $no = 1; @endphp
        @foreach($get_instruktur as $gi)




            @php 

                $cek_instruktur = App\SurveyFeedbackInstrukturUser::where('diklat_id',$gi->diklat_id)->where('instruktur_id',$gi->instruktur_id)->where('user_id',auth()->user()->id)->first();

                
            @endphp

        @if(empty($cek_instruktur))
           
            
           <div class="modal modal-test fade hide" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document" style="max-width: 82%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="desktop bg-post-test">
                            <div class="tagline">EVALUASI PENGAJAR</div>
                        </div>
                    </div>
                    <form  action="{{route('answer.survey_instruktur', [$diklat->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body postest">
                            <div class="row">
                                @php $ss = 0; @endphp
                                     @php $nomor = $no++;
        
                         $get_nama = App\Instruktur::where('id',$gi->instruktur_id)->first();
                         $instansi = App\InstansiInstruktur::where('id',$get_nama->instansi_instruktur_id)->first();
                          @endphp
                          <input type="hidden" name="instruktur_id" value="{{ $gi->instruktur_id }}">
                          <div class="col-lg-12">
                            <center>Nama Instruktur : {{ $get_nama->name }}</center><nr>
                                <center>Unit Kerja : {{ $instansi->nama }}</center>
                            <br>
                        </div>
                                <div class="row ratings">
                                    
                                     <p id="testinglagi"></p>
                                    
                                      @foreach($survey_instruktur as $se)

                                    <div class="col-lg-6">
                                        <div class="media">
                                             
                                            <h3>{{++$ss}}.</h3>
                                            <div class="media-body">
                                                <p>{!!$se->question!!}</p>
                                                
                                                <br>
                                                <div class="form-field">
                                                     @php
                                                            $kata = [10 => 'memuaskan', 9 => 'memuaskan', 8 => 'baik sekali', 7 => 'baik', 6 => 'cukup', 5 => 'kurang', 4 => 'kurang', 3 => 'kurang', 2 => 'kurang', 1 => 'kurang'];
                                                        @endphp
                                                   
                                                    <select oninvalid="InvalidMsg(this);" oninput="InvalidMsg(this);" required="" id="glsr-ltr" name="{{$se->id}}" id="star" class="star-rating">
                                                        <option >Pilih Rating</option>

                                                        <option value="10">Memuaskan</option>
                                                        <option value="9">Memuaskan</option>
                                                        <option value="8">Baik Sekali</option>
                                                        <option value="7">Baik</option>
                                                        <option value="6">Cukup</option>
                                                        <option value="5">Kurang</option>
                                                        <option value="4">Kurang</option>
                                                        <option value="3">Kurang</option>
                                                        <option value="2">Kurang</option>
                                                        <option value="1">Kurang</option>
                                                    </select>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @endforeach
                                </div>
                                
                                <div class="btnSubmit colfeedback">
                                    <button type="submit" id="btn_survey" class="btnFeedback">KIRIM</button>
                                   
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
       
    @endif   
      @endforeach
    @endif   
    @endif
    

    
@endsection

@push('push_js')
<script type="text/javascript" src="{{ asset('js/rating.js') }}"></script>
<script>
    $('.gl-star-rating--stars').text("Pilih Jawaban");
    function InvalidMsg(textbox) {
    if (textbox.value == '') {
        textbox.setCustomValidity('Jawaban tidak boleh kosong');
    }
    else if (textbox.validity.typeMismatch){
        textbox.setCustomValidity('Jawaban tidak boleh kosong');
    }
    else {
       textbox.setCustomValidity('');
    }
    return true;
}
        var destroyed = false;
        var starratingPrebuilt = new StarRating('.star-rating-prebuilt', {
            prebuilt: true,
            maxStars: 5,
        });
        var starrating = new StarRating('.star-rating', {
            stars: function (el, item, index) {
                el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
            },
        });
        var starratingOld = new StarRating('.star-rating-old');
        document.querySelector('.toggle-star-rating').addEventListener('click', function () {
            if (!destroyed) {
                starrating.destroy();
                starratingOld.destroy();
                starratingPrebuilt.destroy()
                destroyed = true;
            } else {
                starrating.rebuild();
                starratingOld.rebuild();
                starratingPrebuilt.rebuild()
                destroyed = false;
            }
        });
    </script>
    
<script type="text/javascript">
    // $('#btn_survey').on('click',function(){
    //     alert("as");
    // })
   
    $('.rating').on('change', function () {
            this.setCustomValidity('');
            $(this).removeAttr('required');
            $(this).removeAttr('oninvalid');
        });

    var modals = $('.modal.modal-test');

    modals.each(function(idx, modal) {
        var $modal = $(modal);
        var $bodies = $modal.find('div.modal-body');
        var total_num_steps = $bodies.length;
        var $progress = $modal.find('.m-progress');
        var $progress_bar = $modal.find('.m-progress-bar');
        var $progress_stats = $modal.find('.m-progress-stats');
        var $progress_current = $modal.find('.m-progress-current');
        var $progress_total = $modal.find('.m-progress-total');
        var $progress_complete  = $modal.find('.m-progress-complete');
        var reset_on_close = $modal.attr('reset-on-close') === 'true';

        function reset() {
            $modal.find('.step').hide();
            $modal.find('[data-step]').hide();
        }

        function completeSteps() {
            $progress_stats.hide();
            $progress_complete.show();
            $modal.find('.progress-text').animate({
                top: '-2em'
            });
            $modal.find('.complete-indicator').animate({
                top: '-2em'
            });
            $progress_bar.addClass('completed');
        }

        function getPercentComplete(current_step, total_steps) {
            return Math.min(current_step / total_steps * 100, 100) + '%';
        }

        function updateProgress(current, total) {
            $progress_bar.animate({
                width: getPercentComplete(current, total)
            });
            if (current - 1 >= total_num_steps) {
                completeSteps();
            } else {
                $progress_current.text(current);
            }

            $progress.find('[data-progress]').each(function() {
                var dp = $(this);
                if (dp.data().progress <= current - 1) {
                    dp.addClass('completed');
                } else {
                    dp.removeClass('completed');
                }
            });
        }

        function goToStep(step) {
            reset();
            var to_show = $modal.find('.step-' + step);
            if (to_show.length === 0) {
                // at the last step, nothing else to show
                return;
            }
            to_show.show();
            var current = parseInt(step, 10);
            updateProgress(current, total_num_steps);
            findFirstFocusableInput(to_show).focus();
        }

        function findFirstFocusableInput(parent) {
            var candidates = [parent.find('input'), parent.find('select'),
                              parent.find('textarea'),parent.find('button')],
                winner = parent;
            $.each(candidates, function() {
                if (this.length > 0) {
                    winner = this[0];
                    return false;
                }
            });
            return $(winner);
        }

        function bindEventsToModal($modal) {
            var data_steps = [];
            $('[data-step]').each(function() {
                var step = $(this).data().step;
                if (step && $.inArray(step, data_steps) === -1) {
                    data_steps.push(step);
                }
            });

            $.each(data_steps, function(i, v) {
                window.addEventListener('next.m.' + v, function (evt) {
                    goToStep(evt.detail.step);
                }, false);
            });
        }

        function initialize() {
            reset();
            updateProgress(1, total_num_steps);
            $modal.find('.step-1').show();
            $progress_complete.hide();
            $progress_total.text(total_num_steps);
            bindEventsToModal($modal, total_num_steps);
            $modal.data({
                total_num_steps: $bodies.length,
            });
            if (reset_on_close){
                //Bootstrap 2.3.2
                $modal.on('hidden', function () {
                    reset();
                    $modal.find('.step-1').show();
                })
                //Bootstrap 3
                $modal.on('hidden.bs.modal', function () {
                    reset();
                    $modal.find('.step-1').show();
                })
            }
        }

        initialize();
    })

    var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
$('.owl-carousel.slider__low_item_package').owlCarousel({
    loop:false,
    margin:8,
    nav:true,
    responsiveClass:true,
    autoplay: true,
    autoplayHoverPause: true,
    autoplayTimeout:5000,
    responsive:{
        0:{
            items:1,
        },
        600:{
            items:3,
        },
        1000:{
            items:5,
        }
    }
})
sendEvent = function(sel, step) {
    var sel_event = new CustomEvent('next.m.' + step, {detail: {step: step}});
    window.dispatchEvent(sel_event);
}

</script>
@endpush
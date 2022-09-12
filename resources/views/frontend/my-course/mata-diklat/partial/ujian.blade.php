@php
    $user_assesment = auth()->user()->assesmentEncounter($data->id)->first();
    $user_answer = count($data->details) > 0 ? $data->details->first()->users(auth()->id())->first() : null;
    $nows = \Carbon\Carbon::now() <= \Carbon\Carbon::parse($data->start_at)->addMinutes($data->duration);
   $nilai =  $diklat->getScore($user->id);
@endphp
<form id="formLatihan"  class="needs-validation" novalidate action="{{route('answer.encounter', [$data->id])}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="dashboard">
        <h5><u>{{ucwords($mata->title)}}</u></h5>
        @if ($nows)
            {{-- @if (!empty($user_answer) && $nilai < 60) --}}
            @if (!empty($user_answer))
                <div class="header__notif text-center text-white" style="background: url('{!!asset('frontend/assets/img/bg-abstract-2.png')!!}') no-repeat">
                    <h4 id="t-hd" class="py-4">Anda Telah Mengikuti Ujian.</h4>
                </div>
            @else
                <div class="notification" id="Notify">
                    <div class="header__notif text-center text-white">
                        <h4 id="t-hd" class="py-4"></h4>
                        <div id="flipdown" class="flipdown my-2 mx-auto"></div>
                        <div id="start-ujian" class="flipdown my-2 mx-auto"></div>
                        {{-- <h4 id="t-hd" class="py-4">Ujian anda telah di nilai oleh instruktur, silahkan melanjutkan pembelajaran.</h4>
                        <h4 id="t-hd" class="py-4">Ujian Anda Sedang Di Nilai.</h4> --}}
                    </div>
                    <div class="notif__text py-2" id="answer-area" style="display: none;">
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($data->details as $dd)
                            @switch($dd->type)
                                @case('pg')
                                        @php $soal = $dd->id; $details = json_decode($dd->details); @endphp
                                        @if ($dd->key == 'soal')
                                            <p class="my-2">{{++$i.'). '.$dd->value}}</p>
                                        @endif
                                        @foreach ((array)$details->options as $kd => $vd)
                                            @if (!empty($vd))
                                                <div class="form-check my-2">
                                                    <input required id="validationFormCheck2" class="form-check-input" type="radio" name="answer_{{$soal}}" value="{{strtolower($kd)}}">
                                                    <label for="validationFormCheck2" class="form-check-label">
                                                        {{$vd}}
                                                    </label>
                                                    <div class="invalid-feedback">Jawaban tidak boleh kosong</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @break
                                @case('essay')
                                        @if ($dd->key == 'soal')
                                            <div class="my-3">
                                                <p class="mb-2">{{++$i}}). {!!$dd->value!!}</p>
                                                <div class="input-group">
                                                    <textarea oninvalid="this.setCustomValidity('Mohon diisi pertanyaan ini')" oninput="setCustomValidity('')" required="" name="answer_{{$dd->id}}" id="" class="form-control" aria-label="With textarea" cols="30" rows="10"></textarea>
                                                </div>
                                            </div>
                                        @endif
                                    @break
                                @default

                            @endswitch
                        @endforeach
                    </div>
                    <div id="foot" class="footer__notif">
                        <p>Ujian Akan Di Laksanakan Pada {{\Carbon\Carbon::parse($data->start_at)->format('d M Y H:i')}}</p>
                    </div>
                </div>
            @endif
        @else
            @if (!empty($user_answer))
            <div class="header__notif text-center text-white" style="background: url('{!!asset('frontend/assets/img/bg-abstract-2.png')!!}') no-repeat">
                <h4 id="t-hd" class="py-4">Ujian Berakhir, Anda Telah Mengikuti Ujian.</h4>
            </div>
            @else
            <div class="header__notif text-center text-white" style="background: url('{!!asset('frontend/assets/img/bg-abstract-2.png')!!}') no-repeat">
                <h4 id="t-hd" class="py-4">Ujian Berakhir, Anda Tidak Mengikuti Ujian Ini.</h4>
            </div>
            @endif
        @endif
    </div>
</form>

@push('push_css')
    <link rel="stylesheet" href="{{asset('frontend/styles/flipdown.css')}}">
@endpush

@push('push_js')
    @if ($nows && empty($user_answer))
    <script type="text/javascript" src="{{asset('frontend/scripts/flipdown.js')}}"></script>
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', () => {
            $('.header__notif').css('background', 'url("{!!asset('frontend/assets/img/bg-abstract-3.png')!!}") no-repeat')
            $('#t-hd').text('Akan di laksanakan dalam')
            let $time = '{!!\Carbon\Carbon::parse($data->start_at)->format('Y/m/d H:i')!!}'
            // Unix timestamp (in seconds) to count down to
            var twoDaysFromNow = (new Date($time).getTime()/ 1000);
            // console.log(twoDaysFromNow);
            // Set up FlipDown
            var flipdown = new FlipDown(twoDaysFromNow, {
                theme: 'dark'
            })

            // Start the countdown
            .start()

            // Do something when the countdown ends
            .ifEnded(() => {
                $('#flipdown').css('display', 'none')
                $('#answer-area').css('display', 'block')
                $('.header__notif').css('background', 'url("{!!asset('frontend/assets/img/bg-abstract-1.png')!!}") no-repeat')
                $('#foot').html('<p>Ujian Sedang Berlangsung <a> </a><input class="btn btn-primary" type="submit" name="" value="Kirim"></p>')
                $time = '{!!\Carbon\Carbon::parse($data->start_at)->addMinutes($data->duration)->format('Y/m/d H:i')!!}'
                startUjian($time)
                // console.log('The countdown has ended!');
            });
        });
        function startUjian($time)
        {
            $('#t-hd').text('Ujian sedang berlangsung, berakhir dalam..')
            var twoDaysFromNow = (new Date($time).getTime()/ 1000);
            console.log(twoDaysFromNow);
            // Set up FlipDown
            var flipdown = new FlipDown(twoDaysFromNow, 'start-ujian')

            // Start the countdown
            .start()

            // Do something when the countdown ends
            .ifEnded(() => {
                $('.header__notif').css('background', 'url("{!!asset('frontend/assets/img/bg-abstract-2.png')!!}") no-repeat')
                document.getElementById('formLatihan').submit()
                console.log('The countdown has ended!');
            });
        }


            var forms = document.querySelectorAll('.needs-validation')

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
        
    </script>
    @endif
@endpush

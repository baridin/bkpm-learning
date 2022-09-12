{{-- <form id="formLatihan" action="{{route('answer.encounter', [$data->id])}}" method="post" enctype="multipart/form-data"> --}}
  @php
    $user_assesment = $data->users(auth()->id())->first();
    // if nows is true virtual class runing, else virtual class ended
    $nows = \Carbon\Carbon::now() <= \Carbon\Carbon::parse($data->start_at)->addMinutes($data->duration);
  @endphp

  <div class="dashboard">
    <h5><u>{{ucwords($data->title)}}</u></h5>
    <div class="notification" id="Notify">
      @if ($nows)
        <div class="header__notif text-center text-white">
          <h4 id="t-hd" class="py-4"></h4>
          <div id="flipdown" class="flipdown my-2 mx-auto"></div>
          <div id="start-ujian" class="flipdown my-2 mx-auto"></div>
        </div>
        <div class="notif__text py-2" id="answer-area" style="display: none;">
          <h5><u>{{$data->title}}</u></h5>
          <p>Mulai Pada: {{\Carbon\Carbon::parse($data->start_at)->format('d M Y H:i')}}</p>
          <p>Durasi: {{$data->duration}} Menit</p>
          <p>Password: {{$data->password}}</p>
          <p>Detail: </p>
          {!!(!empty($data->detail))?$data->detail:'-'!!}
        </div>
        <div id="foot" class="footer__notif">
          <p>Virtual Class Akan Di Laksanakan Pada {{\Carbon\Carbon::parse($data->start_at)->format('d M Y H:i')}}</p>
        </div>
      @else
        @if (!empty($user_assesment))
        <div class="header__notif text-center text-white" style="background: url('{!!asset('frontend/assets/img/bg-abstract-2.png')!!}') no-repeat">
            <h4 id="t-hd" class="py-4">Anda Telah Mengikuti Virtual Class.</h4>
          </div>
        @else
        <div class="header__notif text-center text-white" style="background: url('{!!asset('frontend/assets/img/bg-abstract-2.png')!!}') no-repeat">
            <h4 id="t-hd" class="py-4">Anda Tidak Mengikuti Virtual Class Ini.</h4>
          </div>
        @endif
      @endif
    </div>
  </div>
{{-- </form> --}}

@push('push_css')
  <link rel="stylesheet" href="{{asset('frontend/styles/flipdown.css')}}">
@endpush

@push('push_js')
  @if ($nows)
  <script type="text/javascript" src="{{asset('frontend/scripts/flipdown.js')}}"></script>
  <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', () => {
        $('.header__notif').css('background', 'url("{!!asset('frontend/assets/img/bg-abstract-3.png')!!}") no-repeat')
        $('#t-hd').text('Virtual Class akan di laksanakan dalam')
          let $time = '{!!\Carbon\Carbon::parse($data->start_at)->format('Y/m/d H:i') !!}'
          // Unix timestamp (in seconds) to count down to
          var twoDaysFromNow = (new Date($time).getTime()/ 1000);
          console.log(twoDaysFromNow);
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
              $('#foot').html('<p>Virtual CLass Sedang Berlangsung <a id="penambah" href="{!!route('join.virtual-class', [$data->id])!!}" class="btn btn-blue" target="_blank">Bergabung</a></p>')
              $time = '{!!\Carbon\Carbon::parse($data->start_at)->addMinutes($data->duration)->format('Y/m/d H:i')!!}'

              startUjian($time)
              // console.log('The countdown has ended!');
          });
      });
      function startUjian($time)
      {

        $('#t-hd').text('Virtual Class sedang berlangsung, berakhir dalam..')
          var twoDaysFromNow = (new Date($time).getTime()/ 1000);
          console.log(twoDaysFromNow);
          // Set up FlipDown
          var flipdown = new FlipDown(twoDaysFromNow, 'start-ujian')

          // Start the countdown
          .start()

          // Do something when the countdown ends
          .ifEnded(() => {
            $('.header__notif').css('background', 'url("{!!asset('frontend/assets/img/bg-abstract-2.png')!!}") no-repeat')
            $('#t-hd').text('Virtual class telah berkhir')
            $('#answer-area').css('display', 'none')
            $('a#penambahan').css('display', 'none')
            $('#foot').html('<p>Virtual CLass Telah Berakhir.. <a id="penambah" href="javascript:" class="btn btn-red disabled">Telah Berakhir</a></p>')
          });
      }
  </script>
  @endif
@endpush

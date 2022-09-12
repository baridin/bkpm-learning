@if ($data->type == 'video')
    {{-- <video id="video" width="750" height="440" class="video-js vjs-default-skin" controls>
                 <source src="{{ asset($data->wistia_hashed_link) }}" type="application/x-mpegURL"/> 
                  <source src="{{ asset('video/') }}{{ $data->wistia_hashed_link }}" type="application/x-mpegURL"/> 
                 </video> --}}
                 <iframe width="750" class="video-stream" height="440" src="https://www.youtube.com/embed/{{ $data->video }}?controls=1&modestbranding=0" title="YouTube video player" ></iframe>
@elseif ($data->type == 'pdf')
      @foreach(json_decode($data->file) as $file)
          <div class="PDFFlip" id="PDFF" source="{{asset('storage/'.$file->download_link)}}" style="height: 425px;"></div>
          <br/>
          <a href="{{asset('storage/'.$file->download_link)}}" download="{{asset('storage/'.$file->download_link)}}" class="pull-right btn btn-sm btn-success">Download materi PDF</a>
      @endforeach
@endif
@push('push_css')
  <link href="{{asset('js/PDF-Flip/pflip/css/pdfflip.css')}}" rel="stylesheet" type="text/css">
@endpush

@push('push_js')
  @if ($data->type == 'video')
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/video.js@6.6.3/dist/video-js.css">
    <script src="https://cdn.jsdelivr.net/npm/video.js@6.6.3/dist/video.min.js"></script>

    <!-- Brightcove DVRUX plugin -->
    <link href="//players.brightcove.net/videojs-live-dvrux/1/videojs-live-dvrux.min.css" rel="stylesheet">
    <script src="//players.brightcove.net/videojs-live-dvrux/1/videojs-live-dvrux.min.js"></script>

    <!-- Brightcove quality picker -->
    <link href="//players.brightcove.net/videojs-quality-menu/1/videojs-quality-menu.css" rel="stylesheet">
    <script src="//players.brightcove.net/videojs-quality-menu/1/videojs-quality-menu.min.js"></script>

    <script src="https://cdn.streamroot.io/videojs-hlsjs-plugin/1/stable/videojs-hlsjs-plugin.js"></script>
    <script async src="//www.googletagmanager.com/gtag/js?id=UA-172837283-1"></script>
    <script>
      document.getElementsByTagName('iframe')[0].contentWindow.getElementsByClassName('ytp-watch-later-button')[0].style.display = 'none';
        var options = {
            plugins: {
               
                streamrootHls: {
                    hlsjsConfig: {
                        // Your Hls.js config
                    },
                   
                },
                qualityMenu: {
                    useResolutionLabels: true
                }
            }
        };
        var player = videojs('video', options);
        player.qualityMenu();
        player.dvrux();
    </script>         

  @elseif ($data->type =='pdf')
    <script src="{{asset('js/PDF-Flip/pflip/js/pdfflip.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/PDF-Flip/settings.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/PDF-Flip/toc.js')}}" type="text/javascript"></script>
  @endif
  <style type="text/css">
    a.ytp-impression-link {
      display: none;
    }
    .ytp-chrome-top.ytp-show-cards-title{
      display: none;
    }
  </style>

  <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
  <script type="text/javascript">
    document.getElementsByClassName("ytp-chrome-top").style.display = "none";
  var advideo = false;
 
setTimeout(function() {
    console.log("Removing All Youtbe Ad's!!");
}, 4000);
 
function removead() {
    $(".video-stream").attr("src", "");
}
 
(function(){
    if ($(".videoAdUiRedesign")[0]){
    advideo = true;
} else {
    advideo = false;
}
    $("#player-ads").remove();
    $("#masthead-ad").remove();
    $("#offer-module").remove();
    $(".video-ads").remove();
    $("#pyv-watch-related-dest-url").remove();
 
    if (advideo == true) {
        removead();
    }
    setTimeout(arguments.callee, 1000);
})(1000);
</script>
  <script type="text/javascript">
      $(function () {
          var videoKu = {};
          window._wq = window._wq || [];
          _wq.push({ id: '{{$data->wistia_hashed_link}}', options: {playbar: true, playButton: false, smallPlayButton: false}, onReady: function(video) {
              // // console.log("I got a handle to the video!");
              // // console.log(secondsToHms(video.duration()))
              video.play();
              video.bind("secondchange", function(s) {
                var quiz_collect = {!! $quizzo !!}

                var material_id = {!! $data->id !!}

                var iconic = document.querySelectorAll('#babaicon')
                
                for (let index = 0; index < iconic.length; index++) {
                  const element = iconic[index];

                  if (element.dataset.material == {!! $data->id !!}) {
                    element.name = 'eye'
                    element.setAttribute('style', 'color: red !important;font-size: 18px;')
                    element.classList.remove('side-icon')
                  }
                }

                for (let index = 0; index < quiz_collect.length; index++) {
                  switch (s) {
                    case Math.floor((quiz_collect[index].minute * 60)+quiz_collect[index].second):
                    
                      buildModalQuiz(quiz_collect[index])
                      $("#quizq").modal({backdrop: 'static', keyboard: false})
                      video.pause();

                      $("#quizq").on('hidden.bs.modal', function (){
                        video.play()
                      })
                      break;
                  }
                }
                // console.log(s);
                
                if (s == Math.round(video.duration()-1)) {
                  // var token = $('meta[name="csrf-token"]').attr('content')

                  // var $formData = new FormData()
                  // $formData.append('_token', token)
                  // $formData.append('material_id', {!! $data->id !!})
                  
                  // $.ajax({
                  //   url: '/monitoring/video/view',
                  //   type: 'POST',
                  //   data: $formData,
                  //   success: function (data) {
                  //     var material_id = {!! $data->id !!}

                  //     var iconic = document.querySelectorAll('#babaicon')
                      
                  //     for (let index = 0; index < iconic.length; index++) {
                  //       const element = iconic[index];

                  //       if (element.dataset.material == {!! $data->id !!}) {
                  //         element.name = 'checkmark-circle'
                  //         element.classList.add('text-success')
                  //         element.classList.remove('side-icon')
                  //       }
                  //     }
                  //   },
                  //   cache: false,
                  //   contentType: false,
                  //   processData: false
                  // });
                  
                  var video_moved = document.getElementById('moved_video_challenge')

                  var $movedTo = video_moved.dataset.moved
                  console.log($movedTo);
                  
                }
              });
              video.bind("seek", function(currentTime, lastTime) {
                console.log("Whoa, you jumped " + Math.abs(lastTime - currentTime) + " seconds!");
              });
          }});

          function htmlEntities(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
          }

          function buildModalQuiz(dataQuiz) {
            $html = `<div class="modal quiz fade" id="quizq" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-head"><img src="{!!asset('frontend/assets/img/icon-quiz.png')!!}"><span class="text">Quiz</span></div><div class="modal-body"><form id="quizzForm"><div class="question"><p>${dataQuiz.question}</p><div class="custom-control custom-radio"><input type="radio" id="customRadio11" name="answer" class="custom-control-input" value="A"><label class="custom-control-label" for="customRadio11">${htmlEntities(dataQuiz.option_a)}</label></div><div class="custom-control custom-radio"><input type="radio" id="customRadio22" name="answer" class="custom-control-input" value="B"><label class="custom-control-label" for="customRadio22">${htmlEntities(dataQuiz.option_b)}</label></div><div class="custom-control custom-radio"><input type="radio" id="customRadio33" name="answer" class="custom-control-input" value="C"><label class="custom-control-label" for="customRadio33">${htmlEntities(dataQuiz.option_c)}</label></div><div class="custom-control custom-radio"><input type="radio" id="customRadio44" name="answer" class="custom-control-input" value="D"><label class="custom-control-label" for="customRadio44">${htmlEntities(dataQuiz.option_d)}</label></div></div><div class="button"><button type="button" onclick="postAnswerQuiz(${dataQuiz.id}); return false;" name="button">Kirim</button></div></form></div></div></div></div>`

            $("#place_quiz").html($html)
          }

          function secondsToHms(d) {
            d = Number(d);
            var h = Math.floor(d / 3600);
            var m = Math.floor(d % 3600 / 60);
            var s = Math.floor(d % 3600 % 60);

            var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
            var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
            var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
            return hDisplay + mDisplay + sDisplay; 
          }
      });
      function postAnswerQuiz(id) {
          var answer = $('input[name=answer]:checked').val();;

          var formData = new FormData();
          formData.append('id', id);
          formData.append('answer', answer);
          formData.append('log', 'LOG_ANSWER_QUIZ');
          $.ajax({
              url: `{!!route('answer.quizz')!!}`,
              type: 'POST',
              data: formData,
              success: function (data) {
                  swal("Berhasil !", "Jawaban Benar", "success");
                  $('#quizq').modal('hide');
              },
              error: function (data) {
                  swal("Salah !", "Jawaban Salah", "error");
                  $('#quizq').modal('hide');
              },
              cache: false,
              contentType: false,
              processData: false
          });

      }
  </script>
@endpush
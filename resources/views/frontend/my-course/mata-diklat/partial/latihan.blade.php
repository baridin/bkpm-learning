<form id="formLatihan"  class="needs-validation" novalidate action="{{route('answer.exercise', [$data->id])}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="dashboard">
        <div class="notification" id="Notify">
            {{-- <button type="button" name="button" class="close-btn" id="closeBtn">×</button> --}}
            <div class="header__notif text-center text-white">
                <h4 class="py-4">Latihan Pilihan Ganda {{$data->line}}</h4>
            </div>
            @php $user_assesment = auth()->user()->assesmentExercises($data->id)->first(); @endphp
            @if (empty($user_assesment))
                <div class="notif__text py-2">
                    @php $i = 0; @endphp
                    @foreach ($data->details as $ek => $ev)
                        <div class="mt-4">
                            @php $soal = $ev->id; @endphp
                            @if ($ev->key == 'soal')
                                <p class="my-2">{{++$i.'). '.$ev->value}}</p>
                            @endif
                            @if (!empty(json_decode($ev->details)))
                                @foreach ((array)json_decode($ev->details)->options as $ky => $vy)
                                    @if (!empty($vy))
                                        <div class="form-check my-2">
                                            <input required="" class="form-check-input" id="validationFormCheck2" type="radio" name="answer{{$soal}}[]" value="{{strtolower($ky)}}" />
                                            <label for="validationFormCheck2" class="form-check-label">{{$vy}}</label>
                                            <div class="invalid-feedback">Jawaban tidak boleh kosong</div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="footer__notif">
                    <p>Yakin Anda Sudah Selesai Menjawab Semua Pertanyaan? <a> </a><input class="btn btn-primary" type="submit" name="" value="Ya, Kirim Jawaban"></p>
                </div>
            @else
                <div class="notif__text py-2">
                    Nilai Anda :  {{$user_assesment->assesment}}
                
                </div>
            @endif
        </div>
    </div>
</form>

@push('push_js')

<script type="text/javascript">
    // Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
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
})()

</script>

@endpush
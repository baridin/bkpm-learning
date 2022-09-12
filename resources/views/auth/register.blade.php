@extends('frontend.main')

@section('content')
<style type="text/css">
    :root {
  --color-first: #65587f;
  --color-second: #f18867;
  --color-third: #e85f99;
  --color-forth: #50bda1;
  --block-width: 350px;
  --block-height: 470px;
  --border-width: .625rem;
  --border-radius-outer: 8px;
  --border-radius-inner: calc(var(--border-radius-outer) / 2);
  --font-plain: "IBM Plex Sans", sans-serif;
  --font-special: "Fredoka One", sans-serif;
  box-sizing: border-box;
  line-height: 1.4;
}


.rainbow {
  width: 100%;
  height: 100%;
  -webkit-animation: o-rotate-360 linear 8s infinite;
          animation: o-rotate-360 linear 8s infinite;
}
.rainbow span {
  display: block;
  width: 100%;
  height: 100%;
  position: relative;
  transform: translate(-50%, -50%);
}
.rainbow span:after {
  display: block;
  content: "";
  width: 100%;
  height: 100%;
  position: absolute;
  left: 100%;
}
.rainbow span:first-child {
  background: var(--color-first);
}
.rainbow span:first-child:after {
  background: var(--color-second);
}
.rainbow span:last-child {
  background: var(--color-third);
}
.rainbow span:last-child:after {
  background: var(--color-forth);
}

.c-subscribe-box {
  width: var(--block-width);
  height: var(--block-height);
  overflow: hidden;
  position: relative;
  box-shadow: 0 10px 40px -10px rgba(0, 64, 128, 0.2);
  border-radius: var(--border-radius-outer);
}
.c-subscribe-box__wrapper {
  width: calc(100% - var(--border-width));
  height: calc(100% - var(--border-width));
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #00A1E0;
  padding: 1.2rem 1rem 1.8rem;
  display: flex;
  flex-direction: column;
  border-radius: var(--border-radius-inner);
}
.c-subscribe-box__title {
  font-size: 1.6rem;
}
.c-subscribe-box__desc {
  font-size: 0.935rem;
  margin: 0.7rem auto 1.8rem;
  max-width: 240px;
}
.c-subscribe-box__form {
  margin-top: auto;
}

.c-form--accent input:hover, .c-form--accent input:active, .c-form--accent input:focus {
  border-color: var(--color-third);
  box-shadow: 0 0 0 3px rgba(232, 94, 152, 0.25);
}
.c-form--accent [type=submit] {
  background: var(--color-third);
  border-color: var(--color-third);
  color: #fff;
}

@-webkit-keyframes o-rotate-360 {
  0% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes o-rotate-360 {
  0% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}
[type=submit] {
  margin-bottom: 0;
  font-family: var(--font-special);
  font-weight: normal;
  letter-spacing: 0.015em;
  font-size: 1.1rem;
}
[type=submit]:active {
  transform: scale(0.97);
}

input {
  font-family: inherit;
  color: inherit;
  outline: none;
  font-size: 93%;
  transition: all 300ms ease;
}

h3 {
  margin: 0;
  letter-spacing: -0.015em;
  font-family: var(--font-special);
  font-weight: normal;
  line-height: 1.4;
}

.u-align-center {
  text-align: center;
}
.coltextLeft p{
    font-size: 18px;
}
.syaratText{
 font-size: 18px;   
}
</style>
<div class="signup signupAsn">

    <div class="textLeft">
        <div class="c-subscribe-box u-align-center">
  <div class="rainbow"><span></span><span></span></div>
   <div class="c-subscribe-box__wrapper">
        <div class="coltextLeft">
            <h2>Syarat Foto</h2>
            <p>Setelah melakukan pendaftaran, silahkan upload foto anda untuk keperluan sertifikat</p>
            <div  style="text-align: left;" class="syaratText">
                <p>Syarat & Ketentuan</p>
                <ul style="text-align: left;">
                    <li>1. Berwarna</li>
                    <li>2. Background Merah</li>
                    <li>3. Pakaian Bebas Rapih</li>
                    <li>4. Foto berukuran 4X6</li>
                    <li>5. Ukuran Maks 500kb</li>
                </ul>
            </div>
        </div>
        </div>
    </div>
</div>
    <div class="container py-5">
        <ul class="nav nav-tabs flex-column flex-sm-row" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="flex-sm-fill text-sm-center nav-link active" id="home-tab" data-toggle="tab" href="#home"
                    role="tab" aria-controls="home" aria-selected="true">ASN</a>
            </li>
            <li class="nav-item">
                <a class="flex-sm-fill text-sm-center nav-link" id="profile-tab" data-toggle="tab" href="#profile"
                    role="tab" aria-controls="profile" aria-selected="false">NON ASN</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="heading my-5">
                    <h2>Form Registrasi ASN</h2>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="formSignup">

                            <div class="forms">
                                <form method="POST" action="{{route('register')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>NIP <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username">
                                                @error('username')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                <div class="choose">
                                                    <input class="form-check-input" name="kelamin" type="radio" name="exampleRadios" value="L">
                                                    <label class="form-check-label" for="exampleRadios1">
                                                        Laki-laki
                                                    </label>
                                                    <input class="form-check-input" name="kelamin" type="radio" name="exampleRadios" value="M">
                                                    <label class="form-check-label" for="exampleRadios1">
                                                        Perempuan
                                                    </label>
                                                </div>
                                                @error('kelamin')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Tempat, Tanggal Lahir <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <select class="form-control @error('birth_place') is-invalid @enderror select2" name="birth_place" id="" required>
                                                            <option selected>Select</option>
                                                                @foreach ($kab as $k)
                                                                    <option value="{{$k->nama}}">{{ucwords($k->nama)}}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('birth_place')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="input-group date">
                                                            <input type="text" class="form-control @error('birth_date') is-invalid @enderror datepicker" id="datepicker" name="birth_date">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                        @error('birth_date')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email">
                                                @error('email')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Kata Sandi <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" aria-label="Sizing example input" name="password"
                                                    aria-describedby="inputGroup-sizing-sm" onkeyup="cryptPass(this.value, 'password_encrypt')">
                                                <input type="hidden" class="form-control" id="password_encrypt" aria-label="Sizing example input" name="password_encrypt"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                @error('password')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label>Nomor Handphone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" aria-label="Sizing example input" name="mobile"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                @error('mobile')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat Rumah <span class="text-danger">*</span></label>
                                                <textarea name="home_address" class="form-control @error('home_address') is-invalid @enderror" rows="7"></textarea>
                                                @error('home_address')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="inputState">Kota atau Kabupaten <span class="text-danger">*</span></label>
                                                <select class="form-control @error('home_city') is-invalid @enderror select2" name="home_city" id="" onchange="findLocation(event, this.value, `point`, 'home_prov_asn')">
                                                    <option selected>Select</option>
                                                    @foreach ($kab as $k)
                                                        <option value="{{$k->nama}}">{{ucwords($k->nama)}}</option>
                                                    @endforeach
                                                </select>
                                                @error('home_city')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="inputState">Provinsi <span class="text-danger">*</span></label>
                                                <input type="text" name="home_prov" id="home_prov_asn" class="form-control @error('home_prov') is-invalid @enderror">
                                                @error('home_prov')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="colbtn">
                                        <button type="submit">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="heading my-5">
                    <h2>Form Registrasi Non ASN</h2>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="formSignup">
                            <div class="forms">
                                <form method="POST" action="{{route('register')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Lengkap <span class="text-danger"><span class="text-danger">*</span></span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                <div class="choose">
                                                    <input class="form-check-input" name="kelamin" type="radio" name="exampleRadios" value="L">
                                                    <label class="form-check-label" for="exampleRadios1">
                                                        Laki-laki
                                                    </label>
                                                    <input class="form-check-input" name="kelamin" type="radio" name="exampleRadios" value="M">
                                                    <label class="form-check-label" for="exampleRadios1">
                                                        Perempuan
                                                    </label>
                                                </div>
                                                @error('kelamin')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Tempat, Tanggal Lahir <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <select class="form-control @error('birth_place') is-invalid @enderror select2" name="birth_place" id="" required>
                                                            <option selected>Select</option>
                                                                @foreach ($kab as $k)
                                                                    <option value="{{$k->nama}}">{{ucwords($k->nama)}}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('birth_place')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <div class="input-group date">
                                                            <input type="text" class="form-control @error('birth_date') is-invalid @enderror datepicker" id="datepicker" name="birth_date">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                        @error('birth_date')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email">
                                                @error('email')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" aria-label="Sizing example input" name="password"
                                                    aria-describedby="inputGroup-sizing-sm" onkeyup="cryptPass(this.value, 'password_encrypt')">
                                                <input type="hidden" class="form-control" id="password_encrypt" aria-label="Sizing example input" name="password_encrypt"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                @error('password')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label>Mobile <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" aria-label="Sizing example input" name="mobile"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                @error('mobile')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat Rumah <span class="text-danger">*</span></label>
                                                <textarea name="home_address" class="form-control @error('home_address') is-invalid @enderror" rows="7"></textarea>
                                                @error('home_address')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="inputState">Kota atau Kabupaten <span class="text-danger">*</span></label>
                                                <select class="form-control @error('home_city') is-invalid @enderror select2" name="home_city" id="" onchange="findLocation(event, this.value, `point`, 'home_prov_non')">
                                                    <option selected>Select</option>
                                                    @foreach ($kab as $k)
                                                        <option value="{{$k->nama}}">{{ucwords($k->nama)}}</option>
                                                    @endforeach
                                                </select>
                                                @error('home_city')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="inputState">Provinsi <span class="text-danger">*</span></label>
                                                <input type="text" name="home_prov" id="home_prov_non" class="form-control @error('home_prov') is-invalid @enderror" value="{{old('home_prov')}}">
                                                @error('home_prov')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="colbtn">
                                        <button type="submit">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('push_css')
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
@endpush

@push('push_js')
    <script>
        $(document).ready(function(){
            
        })
        function cryptPass(val, target)
        {            
            return $(`#${target}`).val(val)
        }
        function findLocation(e, val, type, id)
        {
            let formData = new FormData()
            formData.append('type', type)
            formData.append('val', val)
            if (val != 'pusat') {
                $.ajax({
                url: `{!!url('find-locations')!!}`,
                type: 'POST',
                data: formData,
                dataType: "JSON",
                success: function (data) {
                    if (type == 'point') {
                    $(`#${id}`).val(data.nama)
                    }
                    if (type == 'list') {
                    let html = '<option value="" selected> -- Pilih -- </option>'
                    data.forEach(element => {
                        html += `<option value="${element.nama}" selected> ${element.nama} </option>`
                    });
                    $(`#${id}`).html(html)
                    }
                },
                error: function (error) {
                    swal("Gagal !", "Terjadi kesalahan silahkan refresh", "error")
                    .then((val)=>{
                        window.location.reload()
                    })
                },
                cache: false,
                contentType: false,
                processData: false
                });
            } else {
                $(`#${id}`).val('')
            }
        }
    </script>
@endpush
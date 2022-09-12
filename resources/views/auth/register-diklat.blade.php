@extends('frontend.main')

@section('content')
<div class="signup signupAsn">
    <div class="textLeft">
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
    <div class="container py-5">
        <div class="heading mb-5">
            <h2>Form Pendaftaran Diklat</h2>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="formSignup">
                    <div class="forms">
                        <form method="POST" action="{{route('register')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{request()->get('diklat')}}" name="diklat_id" readonly>
                            <input type="hidden" value="{{request()->get('detail')}}" name="detail_id" readonly>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="headingForms">
                                        <h2>Biodata Diri</h2>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">NIP/Username <span class="text-danger"><span class="text-danger">*</span></span></label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{$user->username}}" readonly required>
                                        @error('username')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$user->name}}" required>
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <div class="choose">
                                            <input class="form-check-input" type="radio" name="kelamin" value="L" {{($user->kelamin == 'L')?'checked':''}}>
                                            <label class="form-check-label" for="kelamin">
                                                Laki-laki
                                            </label>
                                            <input class="form-check-input" type="radio" name="kelamin" value="M" {{($user->kelamin == 'M')?'checked':''}}>
                                            <label class="form-check-label" for="kelamin">
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
                                                            <option value="{{$k->nama}}" {{($k->nama == $user->birth_place)?'selected':''}}>{{ucwords($k->nama)}}</option>
                                                        @endforeach
                                                </select>
                                                @error('birth_place')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control @error('birth_date') is-invalid @enderror datepicker" id="datepicker" name="birth_date" value="{{$user->birth_date}}">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('birth_date')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$user->email}}">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Nomor Handphone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{$user->mobile}}">
                                        @error('mobile')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Telepon Rumah </label>
                                        <input type="tel" class="form-control @error('home_phone') is-invalid @enderror" name="home_phone" value="{{$user->home_phone}}">
                                        @error('home_phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Alamat <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('home_address') is-invalid @enderror" name="home_address" rows="5" required>{{$user->home_address}}</textarea>
                                        @error('home_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="home_city">Kota atau Kabupaten <span class="text-danger">*</span></label>
                                        <select id="home_city" class="form-control @error('home_city') is-invalid @enderror select2" name="home_city" onchange="findLocation(event, this.value, `point`, 'home_prov')">
                                            <option selected disabled>Select</option>
                                            @foreach ($kab as $k)
                                                <option value="{{$k->nama}}" {{($k->nama == $user->home_city)?'selected':''}}>{{ucwords($k->nama)}}</option>
                                            @endforeach
                                        </select>
                                        @error('home_city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputState">Provinsi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('home_prov') is-invalid @enderror" name="home_prov" id="home_prov" value="{{$user->home_prov}}" required readonly>
                                        @error('home_prov')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Facebook</label>
                                        <input type="text" class="form-control" name="facebook" value="{{$user->facebook}}">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="headingForms">
                                        <h2>Data Instansi</h2>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Instansi <span class="text-danger">*</span></label>
                                        <select class="form-control @error('dept') is-invalid @enderror select2" name="dept" id="">
                                            <option value="" selected>-- Pilih --</option>
                                            @foreach ($dept as $d)
                                                <option value="{{$d->title}}" {{($user->dept == $d->title)?'selected':''}}>{{ucwords($d->title)}}</option>
                                            @endforeach
                                        </select>
                                        @error('dept')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Alamat Instansi <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm mb-3">
                                                    <select class="form-control @error('info_instansion') is-invalid @enderror select2" name="info_instansion" onchange="findLocation(event, this.value, `list`, 'info_instansion_detail')" onload="findLocation(event, `{!!$user->info_dept!!}`, `list`, 'address_instantion_detail')">
                                                        <option value="" disabled>-- Pilih --</option>
                                                        @foreach (['kota', 'kabupaten', 'provinsi', 'pusat'] as $d)
                                                            <option value="{{$d}}" {{($d == $user->info_instansion)?'selected':''}}>{{ucwords($d)}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('info_instansion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm mb-3">
                                                    <select class="form-control @error('info_instansion_detail') is-invalid @enderror select2" aria-label="Sizing example input" name="info_instansion_detail" id="info_instansion_detail">
                                                        <option value="" disabled>-- Pilih --</option>
                                                        @if (!empty($user->info_instansion_detail))
                                                            <option value="{{$user->info_instansion_detail}}" selected>{{$user->info_instansion_detail}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                @error('info_instansion_detail')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Alamat Kantor <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address_instantion') is-invalid @enderror" rows="4" name="office_address">{{$user->office_address}}</textarea>
                                        @error('address_instantion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputState">Kota atau Kabupaten <span class="text-danger">*</span></label>
                                        <select class="form-control @error('office_city') is-invalid @enderror select2" name="office_city" onchange="findLocation(event, this.value, `point`, 'office_prov')" required>
                                            <option value="" selected>-- Pilih --</option>
                                            @foreach ($kab as $k)
                                                <option value="{{$k->nama}}" {{($k->nama == $user->office_city)?'selected':''}}>{{ucwords($k->nama)}}</option>
                                            @endforeach
                                        </select>
                                        @error('office_city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputState">Provinsi <span class="text-danger">*</span></label>
                                        <input type="text" name="office_prov" id="office_prov" class="form-control @error('office_prov') is-invalid @enderror" required value="{{$user->office_prov}}" readonly>
                                        @error('office_prov')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Telepon Kantor <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('office_phone') is-invalid @enderror" aria-label="Sizing example input" name="office_phone" value="{{$user->office_phone}}" aria-describedby="inputGroup-sizing-sm">
                                        @error('office_phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Fax</label>
                                        <input type="text" class="form-control @error('office_fax') is-invalid @enderror" aria-label="Sizing example input" name="office_fax" value="{{$user->office_fax}}" aria-describedby="inputGroup-sizing-sm">
                                        @error('office_fax')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Website</label>
                                        <input type="text" class="form-control" aria-label="Sizing example input" name="website" value="{{$user->website}}" aria-describedby="inputGroup-sizing-sm">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputState">Jabatan Peserta <span class="text-danger">*</span></label>
                                        <select class="form-control @error('position') is-invalid @enderror select2" name="position" id="position">
                                            <option value="" selected>-- Pilih --</option>
                                            @foreach ($position as $p)
                                                <option value="{{$p->title}}" {{($p->title == $user->position)?'selected':''}}>{{ucwords($p->title)}}</option>
                                            @endforeach
                                        </select>
                                        @error('position')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Bagian / Bidang Unit Kerja <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('bagian') is-invalid @enderror" aria-label="Sizing example input" name="bagian" value="{{$user->bagian}}" aria-describedby="inputGroup-sizing-sm">
                                        @error('bagian')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputState">Golongan Peserta <span class="text-danger">*</span></label>
                                        <select class="form-control @error('grade') is-invalid @enderror select2" name="grade" id="">
                                            <option value="" selected>-- Pilih --</option>
                                            @foreach ($grade as $g)
                                                <option value="{{$g->title}}" {{($g->title == $user->grade)?'selected':''}}>{{ucwords($g->title)}}</option>
                                            @endforeach
                                        </select>
                                        @error('grade')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Nama Atasan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('boss_name') is-invalid @enderror" name="boss_name" value="{{$user->boss_name}}" required>
                                        @error('boss_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail4">Nomor Handphone Atasan <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('boss_phone') is-invalid @enderror" required name="boss_phone" value="{{$user->boss_phone}}">
                                        @error('boss_phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="colbtn">
                                <button type="submit">KIRIM</button>
                            </div>
                        </form>
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
                let html = '<option value="-" selected> - </option>'
                $(`#${id}`).html(html)
            }
        }
    </script>
@endpush

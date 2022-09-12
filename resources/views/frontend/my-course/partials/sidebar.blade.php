<div class="col-md-3 col-sm-3 col-xs-12">
    <div class="sidebar__event">
        <div class="box title">
            <p><img src="assets/img/point.png" class="img-fluid inline-block"> Gambar Profil</p>
        </div>
        <div class="box profile text-center">
            <form id="formUpload" action="{{route('upload.foto')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="user__profile">
                    {{-- <img src="{{Avatar::create(auth()->user()->name)->toBase64()}}" class="img-fluid rounded-circle"> --}}
                    <div class="avatar-upload">
                        <div class="avatar-edit">
                            <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" name="file" />
                            <label for="imageUpload"></label>
                        </div>
                        <div class="avatar-preview">
                            <div id="imagePreview"
                                @if (auth()->user()->avatar == 'users/default.png')
                                    style="background-image: url({{Avatar::create(auth()->user()->name)->toBase64()}});"
                                @else
                                    style="background-image: url({{asset('storage/'.auth()->user()->avatar)}});"
                                @endif
                            >
                            </div>
                        </div>
                    </div>
                </div>
                <div id="btn-submit"></div>
            </form>
            <br>
            <div class="user__desc">
                <p class="hallo"><a href="{{route('my-course.edit', auth()->id())}}" target="_blank">Selamat Datang, <br><span class="name">{{auth()->user()->name}}</span></a></p>
                <p class="status"><span class="online">	&bull;</span> Online</p>
            </div>
        </div>
        <div class="box title">
            <p class="tit"><i class="fa fa-tachometer" aria-hidden="true"></i> TIMELINE</p>
        </div>
        <div class="list__sidebar">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="">
                                <ion-icon name="contact"></ion-icon> <span>Kursus Saya</span> </i>
                            </a>
                        </h4>
                    </div>
                    {{-- <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="{{route('all.certificate')}}">
                            <ion-icon name="document"></ion-icon> <span>Sertifikat</span> </i>
                            </a>
                        </h4>
                    </div> --}}
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="">
                                <ion-icon name="pie"></ion-icon> <span>Peforma</span> </i>
                            </a>
                        </h4>
                    </div>
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="">
                                <ion-icon name="megaphone"></ion-icon> <span>Forum</span> </i>
                            </a>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    @push('push_css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
    <style type="text/css">
        .avatar-upload {
            position: relative;
            max-width: 205px;
            margin: 50px auto;
        }
        .avatar-upload .avatar-edit {
            position: absolute;
            right: 12px;
            z-index: 1;
            top: 10px;
        }
        .avatar-upload .avatar-edit input {
            display: none;
        }
        .avatar-upload .avatar-edit input + label {
            display: inline-block;
            width: 34px;
            height: 34px;
            margin-bottom: 0;
            border-radius: 100%;
            background: #FFFFFF;
            border: 1px solid transparent;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
            cursor: pointer;
            font-weight: normal;
            transition: all 0.2s ease-in-out;
        }
        .avatar-upload .avatar-edit input + label:hover {
            background: #f1f1f1;
            border-color: #d6d6d6;
        }
        .avatar-upload .avatar-edit input + label:after {
            content: "\f040";
            font-family: 'FontAwesome';
            color: #757575;
            position: absolute;
            top: 10px;
            left: 0;
            right: 0;
            text-align: center;
            margin: auto;
        }
        .avatar-upload .avatar-preview {
            width: 192px;
            height: 192px;
            position: relative;
            border-radius: 100%;
            border: 6px solid #F8F8F8;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
        }
        .avatar-upload .avatar-preview > div {
            width: 100%;
            height: 100%;
            border-radius: 100%;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
@endpush

@push('push_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.dropify').dropify()
            $("#imageUpload").change(function() {
                readURL(this);
                $('#btn-submit').html('<input type="submit" value="upload" class="btn btn-sm btn-primary">')
            });
        })
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
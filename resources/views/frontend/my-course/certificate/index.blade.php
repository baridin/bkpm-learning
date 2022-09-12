@extends('frontend.main')

@section('content')
<div class="event my-course">
    <div class="container">
        <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12">
                        <div class="sidebar__event">
                        <div class="box title">
                            <p><img src="assets/img/point.png" class="img-fluid inline-block"> Gambar Profil</p>
                        </div>
                        <div class="box profile text-center">
                            <div class="user__profile">
                                {{-- <img src="{{Avatar::create(auth()->user()->name)->toBase64()}}" class="img-fluid rounded-circle"> --}}
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" />
                                        <label for="imageUpload"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        <div id="imagePreview" style="background-image: url({{Avatar::create(auth()->user()->name)->toBase64()}});">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="user__desc">
                                <p class="hallo"><a href="#">Selamat Datang, <br><span class="name">{{auth()->user()->name}}</span></a></p>
                                <p class="status"><span class="online">	&bull;</span> Online</p>
                            </div>
                        </div>
                        <div class="box title">
                            <p class="tit"><i class="fa fa-tachometer" aria-hidden="true"></i> TIMELINE</p>
                        </div>
                        <div class="list__sidebar">
                            @include('frontend.my-course.partials.sidebar')
                        </div>
                        </div>
                    </div>
            <div class="col-md-9 col-12">
                <div class="all-course certificate">
                    <h2 class="inner">All Certificate</h2>
                    <div class="content__box">
                        <div class="certificate__download">
                            <div class="text">
                                <p>HTML & CSS</p>
                            </div>
                            <div class="btn-download">
                                <a href="#" class="btn btn-full" data-toggle="modal"
                                    data-target="#downloadCertificate"><i class="fa fa-download" aria-hidden="true"></i>
                                    <span>Download</span></a>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="pagination-bottom text-center">
                        <ul class="pagination">
                            <li><a href="#">
                                    <</a> </li> <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                            <li><a href="#">></a></li>
                        </ul>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection

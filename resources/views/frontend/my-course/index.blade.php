@extends('frontend.main')

@section('content')
<div class="event my-course">
    <div class="container">
        <div class="row">

        @include('frontend.my-course.partials.sidebar')

        <div class="col-md-9 col-sm-9 col-xs-12">
            <div class="all-course">
            <h2 class="inner">Kursus Saya</h2>
            <div class="course__list">
                <ul class="list__item">
                @if (auth()->user()->status == 'active' || auth()->user()->status == 'finish' || auth()->user()->status == 'finish' || auth()->user()->status == 'pending' )
                  
                    @forelse ($detail as $key => $det)
                        @php $diklats = $det->getDiklat; @endphp
                        @if (!empty($diklats))
                            <li class="">
                                <div class="course__box">
                                    @if(auth()->user()->avatar != 'users/default.png')
                                    <a href="{{route('my-course.show', [$diklats->id])}}">
                                    @else
                                    <a href="javascript:" class="disabled" onclick="swal( 'Info!' ,  'Harap mengunggah foto anda terlebih dahulu.' ,  'info' )">
                                    @endif
                                        <div class="image">
                                            <img src="{{asset('storage/'.$diklats->image)}}" class="img-fluid" alt="content course">
                                        </div>
                                        <div class="course__desc">
                                            <span class="info">by BKPM | <span class="glyphicon glyphicon-time"></span>  {{$diklats->created_at->format('d M Y')}}</span>
                                            <p class="title">{{ucwords($diklats->title)}}</p>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="{{$diklats->getProgress(auth()->user()->id)}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$diklats->getProgress(auth()->user()->id)}}%;"></div>

                                            </div>
                                            
            @php $matadiklat = App\DiklatMataDiklat::where('diklat_id',$det->diklat_id)->get(); 

                                              
                                               @endphp
                                               @foreach($matadiklat as $mata)
                                                @php 
                                                $get_mata_diklat = App\MataDiklat::where('id',$mata->id)->first();

                                                 @endphp
                                                 
                                               @endforeach 
                                            
                                        </div>
                                    </a>
                                </div>
                                @if ($det->pivot->status == 0)
                                
                                    <!-- hover layout -->
                                    <div class="hover-box" style="opacity: 1;" data-toggle="modal" data-target="#modalPersyaratan{{$det->pivot->id}}">
                                    <a href="#!">
                                        <div class="button-link">
                                        <div class="info text-center p-0">
                                            <p class="text">Dokumen</p>
                                            <p class="doll">Unggah Persyaratan Dokumen</p>
                                        </div>
                                        </div>
                                    </a>
                                    </div>
                                    <div class="modal fade" id="modalPersyaratan{{$det->pivot->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalCenterTitle">Form Unggah</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{url('my-course/upload-persyaratn/'.$det->pivot->id)}}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                @foreach(json_decode($diklats->file_requirment) as $file)
                                                                    <a class="btn btn-primary btn-block" href="{{ asset('storage/'.$file->download_link) ?: '' }}" target="_blank" download="{{ asset('storage/'.$file->download_link) ?: '' }}">
                                                                        Unduh File Persyaratan
                                                                    </a>
                                                                    <br/>
                                                                @endforeach
                                                                <div class="form-group">
                                                                    <label for="">Unggah Persyaratan Anda Di Sini</label>
                                                                    <input type="file" name="file" id="file" class="dropify form-control" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($det->pivot->status == 1)
                                
                                    <!-- hover layout -->
                                    <div class="hover-box" style="opacity: 1;">
                                        <a href="#">
                                            <div class="button-link">
                                                <div class="info text-center p-0">
                                                    <p class="text">Menunggu</p>
                                                    <p class="doll">Silahkan Tunggu Persetujuan Admin</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @elseif ($det->pivot->status == 10)
                                
                                    <!-- hover layout -->
                                    <div class="hover-box" style="opacity: 1;">
                                        <a href="#">
                                            <div class="button-link">
                                                <div class="info text-center p-0">
                                                    <p class="text">Menunggu</p>
                                                    <p class="doll">Silahkan Tunggu Persetujuan Admin</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @elseif (\Carbon\Carbon::parse($det->start_at)->addDays($det->online_at) >= \Carbon\Carbon::now())
                                    <!-- hover layout -->
                                    <div class="hover-box" style="opacity: 1;">
                                        <a href="#">
                                            <div class="button-link">
                                                <div class="info text-center p-0">
                                                    <p class="text">Belum Di Mulai</p>
                                                    <p class="doll">Diklat Online Akan Di Mulai Pada {{\Carbon\Carbon::parse($det->start_at)->addDays($det->online_at)->format('d M Y H:i')}}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </li>
                        @else
                            <li>
                                <h3 style="margin: 25px 0; text-align: center;">Diklat tidak di temukan</h3>
                            </li>
                        @endif
                    @empty
                        <li>
                            <h3 style="margin: 25px 0; text-align: center;">Tidak memiliki diklat</h3>
                        </li>
                    @endforelse
                @endif
                </ul>
                <div class="pagination-bottom text-center">
                    {{-- {{ $diklat->links('vendor.pagination.simple-bootstrap-4') }} --}}
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
    
@endsection

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
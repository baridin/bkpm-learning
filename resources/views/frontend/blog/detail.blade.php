@extends('frontend.main')

@section('content')


<style type="text/css">
    
</style>

<div class="detail-courses">
    <div class="all-course">
        <div class="breadcrumb" style="">
            <div class="container">
                <h2>Pilihan Belajar (PIJAR)</h2>
                 
            </div>
        </div>
    </div>

    <div class="inner__detail">
        <div class="container">
           <section class="section">

        <div class="container">
            
            <div class="row">
                <div class="col-lg-12">                    
                    <div class="border-bottom">
                        <nav class="pb-sm-3 pb-4" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('pijar') }}">Pijar</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $blog->title }}</li>
                            </ol>
                        </nav>
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mt-4">
                    <!-- Post-->
                    <article class="post position-relative">
                        
                        <div class="post-preview mb-4">    
                        <h2 class="fs-22 fw-medium my-3 post-title">
                            <center><a href="" style="color: black;">{{ $blog->title }}</a></h2> <br>                        </center>
                            <center><a href="blog-single.html"><img src="{{ filter_var($blog->image, FILTER_VALIDATE_URL) ? $blog->image : Voyager::image( $blog->image ) }}" style="width:100%; height: 400px;"  alt="" class="img-fluid rounded"></a></center>  
                        </div>
                        <div class="post">
                            
                            
                            <p class="text-muted">{!! htmlspecialchars_decode($blog->body) !!}</p>
                        
                            @if($blog->pdf !== "[]")
                            <div class="container">
                                @php 
                                $link = $blog->pdf;


                                $satu =  explode($blog->pdf,"/");
                                $data = json_decode($link);
                                $file =  $data[0]->download_link;
                                $nama =  $data[0]->original_name;
                                if(isset($data[1])){
                                    $file_pdf2 =  $data[1]->download_link;
                                $nama_pdf2 =  $data[1]->original_name;
                                }
                                @endphp

                              <center>  <a target="_blank" href=" {{ Voyager::image( $file ) }}" class="btn btn-info">{{ $nama }}</a>
                                &nbsp;
                                @if(isset($data[1]))
                                <a target="_blank" href=" {{ Voyager::image( $file_pdf2 ) }}" class="btn btn-info">{{ $nama_pdf2 }}</a>

                                @endif
                                </center>


                            </div>
                            
                            @endif
                                <br>
                            <div style="height: 10px;"></div>

                            @if($blog->video !==  "[]") 
                            <div class="container">
                                @php 
                                $link1 = $blog->video;


                                $satu1 =  explode($blog->video,"/");
                                $data1 = json_decode($link1);
                                $video =  $data1[0]->download_link;
                                @endphp

                                
                                <iframe style="width: 100%;" height="315"
                                src="{{ Voyager::image( $video ) }}">
                            </iframe>
                            </div>
                            
                            @endif
                            
                            <div style="height: 10px;"></div>

                        <div class="container">
                                @if($blog->youtube)
                                <iframe style="width: 100%;" height="315"
                                src="https://www.youtube.com/embed/{{ $youtube }}">
                                </iframe>
                            @endif

                        </div>



                        </div>  
                        <ul class="list-unstyled list-inline mt-5 border-bottom py-3">
                            <li class="list-inline-item"><h5 class="text-uppercase me-2">Bagikan :</h5></li>
                            <li class="list-inline-item border-end pe-3"> <a href="https://www.facebook.com/sharer/sharer.php?u={{ env('APP_URL') . '/pijar/' . $blog->slug  }}"><i class="fa fa-facebook"></i> Facebook</a></li>
                            <li class="list-inline-item border-end pe-3"><a href="#"><i class="fa fa-twitter"></i> Twitter</a></li>
                            
                            <li class="list-inline-item"><a href="#"><i class="fa fa-instagram"></i> Instagram</a></li>
                        </ul>

                    </article>
                    <!-- Post end-->
                </div>
                <div class="col-lg-4 mt-4">
                    <div  class="sidebar ps-lg-4">
                        <!-- Categories widget-->
                        

                        <div class="border rounded">
                            <div style="background-color: #034e97;" class="p-3  rounded">
                                <h5 class="mb-0 text-white">Berita Lainya</h5>                        
                            </div>
                            <div class="container">
                            <ul class="list-unstyled ms-3 my-3">
                                @foreach($blog_list as $get)

                                <li class="d-flex mb-3 pb-3 border-bottom">                                           
                                    <a class=" w-25 me-3" href="{{ url('pijar',$get->slug) }}"><img src="{{ filter_var($get->image, FILTER_VALIDATE_URL) ? $get->image : Voyager::image( $get->image ) }}" style="width:100%" alt="" class="img-fluid rounded"></a>
                                    <div class="container">                       
                                    <div class="flex-1"><a href="{{ url('pijar',$get->slug) }}" class="text-dark">{!!\Illuminate\Support\Str::limit($get->title,30)!!}</a> 
                                    <span class="d-block text-muted">{{ date('d-m-Y', strtotime($get->created_at)) }}</span></div>
                                </div>
                                </li>
                                @endforeach
                                
                            </ul>            
                            </div>             
                        </div>



                    </div>
                </div>
            </div>
            <!-- end row -->            
        </div>
    </section>
    <br>
        </div>
    </div>
</div>

@endsection
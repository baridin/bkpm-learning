@extends('frontend.main')

@section('content')


<style type="text/css">

.mb-0.text-uppercase{
    font-size: 12px;
}
.fs-20.fw-medium.my-3.post-title{
    font-size: 17px;
}
.flex-1{
    padding-left: 10px;
}


/*Clearing Floats*/
.cf:before, .cf:after{
    content:"";
    display:table;
}

.cf:after{
    clear:both;
}

.cf{
    zoom:1;
}    
/* Form wrapper styling */

.search-wrapper {
    
    /*margin: 150px auto 50px auto;*/
    
  background: transparent;
    box-shadow: 0 4px 20px -2px #e9e9e9;
}

/* Form text input */

.search-wrapper input {
  padding-left: 20px;
    width: 330px;
    height: 20px;
    padding: 20px 15px;
    float: left;   
    
    border: 0;
    background: #fff;
    
    border-top-style: none;
}

.search-wrapper input:focus {
    outline: 0;
    background: #fff;
    box-shadow: 0 0 2px rgba(0,0,0,0.8) inset;
}

.search-wrapper input::-webkit-input-placeholder {
   color: #999;
   font-weight: normal;
   font-style: italic;



}

.search-wrapper input:-moz-placeholder {
    
    color: #999;
    font-weight: normal;
    font-style: italic;
}

.search-wrapper input:-ms-input-placeholder {
    color: #999;
    font-weight: normal;
    font-style: italic;
  border-style: none;
}   

/* Form submit button */
.search-wrapper button {
    overflow: visible;
    position: relative;
    float: right;
    border: 0;
    padding: 0;
    cursor: pointer;
    height: 40px;
    width: 110px;
    font: 13px/40px 'lucida sans', 'trebuchet MS', 'Tahoma';
    color: #fff;
    text-transform: uppercase;
    background: #198cff;
    
    text-shadow: 0 -1px 0 rgba(0, 0 ,0, .3);
}  

.search-wrapper button:hover{    
/*     background: #e54040; */
}  

.search-wrapper button:active,
.search-wrapper button:focus{  
    background: #198cff;
    outline: 0;  
}

.search-wrapper button:focus:before,
.search-wrapper button:active:before{
        border-right-color: #c42f2f;
}     

.search-wrapper button::-moz-focus-inner { /* remove extra button spacing for Mozilla Firefox */
    border: 0;
    padding: 0;
}   
    
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
                    <div class="border-bottom text-center">
                          <h4 class="pb-sm-3 pb-4">Pilihan Belajar (PIJAR)</h4>
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">                    
                    <div class="border-bottom">
                        <nav class="pb-sm-3 pb-4" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                            
                            <li class="breadcrumb-item active" aria-current="page">Pijar</li>
                            </ol>
                        </nav>
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4 mt-5">
                    <div class="section-title border-start border-primary position-relative">
                        <h4 class="fw-medium lh-base mb-3"></h4>
                        <div class="row">
                        <div class="col-lg-6 mb-4 pb-2">
                        <aside class="widget mb-4 pb-2 fs-14">
                             <form  action="/pijar?filter=" id="filter">
                            <select name="filter" class="form-control" id="sortby" onchange="this.form.submit()">
                            @foreach ($filters as $item)
                                @if ($item['key'] == $filter)
                                    <option value="{{ $item['key']}}" selected>{{ $item['value']}}</option>  
                                @else
                                    <option value="{{ $item['key']}}">{{ $item['value']}}</option>
                                @endif
                                
                            @endforeach
                        </select>
                    </form>
                        </aside>
                    </div>
                    <div class="col-lg-6 mb-4 pb-2">
                        <aside class="widget mb-4 pb-2 fs-14">
                            {{-- <form class="position-relative" action="pijar?query=">
                                <input class="form-control border rounded" name="query" placeholder="Search...">
                                <button class="search-button" type="submit"><span class="mdi mdi-magnify"></span></button>
                            </form> --}}
                            <form action="pijar?query=" class="search-wrapper cf">
        <input type="text" name="query" placeholder="Pencarian Pijar" required style="box-shadow: none">
        <button type="submit"><i style="color: white; font-size: 20px;" class="fa fa-search"></i></button>
    </form>
                        </aside>
                    </div>

                        </div>                
                    </div>                                       
                </div>                
            </div>
            <div class="row">
                <div class="col-lg-12 ">
                    <!-- Post-->
                    <div class="row">
                        @foreach($newsList as $blog)
                        <div class="col-lg-4 ">
                    <article class="post position-relative pb-5">
                        <div class="date-box rounded shadow text-center bg-white position-absolute top-0 start-0 p-2 ms-3 mt-3">
                            <h5 class="text-uppercase mb-0">{{ date('d M Y', strtotime($blog->created_at)) }}</h5>
                        </div>
                        <div class="post-preview mb-4">                            
                            <a href="{{ url('pijar',$blog->slug) }}"><img src="{{ filter_var($blog->image, FILTER_VALIDATE_URL) ? $blog->image : Voyager::image( $blog->image ) }}" style="width:100%" alt="" class="img-fluid rounded"></a>     

                        </div>
                        <div class="post">
                            @php
                            $body = filter_var($blog->body, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                            @endphp
                           
                            <h3 class="fs-20 fw-medium my-3 post-title"><a href="{{ url('pijar',$blog->slug) }}">{{ $blog->title }}</a></h3> 
                            <p class="text-muted"> {!!\Illuminate\Support\Str::limit($body,180,"...")!!}</p>
                            <a href="{{ url('pijar',$blog->slug) }}" class="fw-medium">Lihat Selengkapnya 
                                <i class="mdi mdi-arrow-right"></i></a>

                        </div> 

                    </article>
                </div>
                @endforeach
                </div>
                    <!-- Post end-->
                    <!-- Post-->
                   
                    <!-- Post end-->
                </div>
                
                
            </div>
            <!-- end row -->
            <div class="row">
                <div class="col-lg-12 mt-sm-0 mt-4">
                    {{ $newsList->links() }}
                </div>
            </div>
        </div>
    </section>
    <br>
        </div>
    </div>
</div>

@endsection
@extends('frontend.main')

@section('content')
<div class="detail-courses">
    <div class="all-course">
        <div class="breadcrumb" style="background-image: url({!!asset('storage/'.$page->image)!!});">
            <div class="container">
                <h2>{{ucwords($page->title)}}</h2>
                <p>{{ucwords($page->excerpt)}}</p>
            </div>
        </div>
    </div>

    <div class="inner__detail">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="inner__content">
                        <div class="inner__desc">
                            {!!$page->body!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
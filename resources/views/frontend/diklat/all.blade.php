@extends('frontend.main')

@section('content')
<div class="all-course">
    <div class="breadcrumb">
        <div class="container">
            <h2>E-Learning BKPM</h2>
        </div>
    </div>
    <div class="container">
        <h2 class="inner my-4">Semua Diklat</h2>
        
        <div class="course__list">
            <ul class="list__item">
                @forelse ($diklat as $d)
                <li>
                    <div class="course__box" onclick="window.open(`{!!route('diklat.show', $d->id)!!}`)">
                        <a href="#">
                            <div class="image">
                                <img src="{{asset('storage/'.$d->image)}}" class="img-responsive" alt="content course">
                            </div>
                            <div class="course__desc">
                                <span class="info">by BKPM | <span class="glyphicon glyphicon-time"></span> {{$d->created_at->format('d M Y')}}</span>
                                <p class="title">{{ucwords($d->title)}}</p>
                            </div>
                        </a>
                    </div>
                </li>
                @empty
                @endforelse
            </ul>
            <div class="pagination-bottom text-center">
                {{$diklat->links('vendor.pagination.bootstrap-4')}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('push_js')
    
@endpush

@push('push_css')
    
@endpush
@extends('voyager::master')

@section('content')
    <div class="page-content">
        <div class="clearfix container-fluid row">
            @forelse ($widgets as $kw => $vw)
                <div class="col-md-3 col-sm-12">
                    <div class="d-block wrapper-block text-center" style="border: 1px solid#22a7f0; padding: 25px;">
                        <h4 class="font-weight-bold" style="font-size: x-large;">{{ ucwords((string)$kw) }}</h4>
                        <div style="margin: 25px 0;">
                            <div class="pill" style="width: 50px; height: 50px; background-color: rgba(0,0,0,.2); border-radius: 50px; margin: 0 auto;">
                                <div class="d-flex" style="display: flex; justify-content: center; align-items: center; height: inherit; flex-wrap: wrap;">
                                    <p class="font-weight-bold" style="color: #000; margin: 0; font-size: large;">{{ $vw->count }}</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route($vw->route) }}" class="btn btn-primary">Lihat</a>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
@stop

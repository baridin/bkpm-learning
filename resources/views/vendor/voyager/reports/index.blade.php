@extends('voyager::master')

@section('page_title', 'Sistem Laporan')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ __('icon') }}"></i> {{ __('Sistem Laporan') }}
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            @php
                $count = count($reports);
                $classes = [
                    'col-xs-12',
                    'col-sm-'.($count >= 2 ? '6' : '12'),
                    'col-md-'.($count >= 3 ? '4' : ($count >= 2 ? '6' : '12')),
                ];
                $class = implode(' ', $classes);
                $prefix = "<div class='{$class}'>";
                $surfix = '</div>';
            @endphp
            @foreach ($reports as $r)
                <div class="{{$class}}">
                    <div class="panel widget center bgimage" style="margin-bottom:0;overflow:hidden;background-image:url('{{ voyager_asset('images/widget-backgrounds/03.jpg') }}');">
                        <div class="dimmer"></div>
                        <div class="panel-content">
                            <i class='{{ __('voyager-file-text') }}'></i>
                            <h4>{{ str_replace("Users","Peserta",ucwords(str_replace('-', ' ', $r)))}}</h4>
                            {{--  --}}
                            <a href="{{route('voyager.reports.show', ($r == 'absensi') ? 'virtual-class-absents' : $r)}}" class="btn btn-primary">{{__('Lihat Laporan')}}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop


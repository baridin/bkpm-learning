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
        <div class="panel widget center bgimage" style="margin-bottom:0;overflow:hidden;background-image:url('{{ voyager_asset('images/widget-backgrounds/03.jpg') }}');">
            <div class="dimmer"></div>
            <div class="panel-content">
                <div class="row container">
                    <div class="col-md-1">
                        <h5 style="color: white">Laporan</h5>
                    </div>
                    <div class="col-md-11">
                        <select class="form-control" name="tipe-laporan" id="tipe-laporan" required>
                            <option value="">--Pilih Laporan</option>
                            @foreach($reports as $r)
                                <option value="{{ $r }}">{{ ucwords(str_replace('-', ' ', $r)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script type="text/javascript">
        
    </script>
@stop
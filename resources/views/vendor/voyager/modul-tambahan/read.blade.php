@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('voyager::compass.includes.styles')
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/SnackBar-master/dist/snackbar.min.css') }}">
@stop



@section('page_header')
    <h1 class="page-title">
        <i class="voyager-params"></i>
        Detail Modul Tambahan
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')

<div class="page-content read container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-bordered" style="padding-bottom:5px;">
                <!-- form start -->
                <div class="panel-heading" style="border-bottom:0;">
                    <h3 class="panel-title">Mata Diklat Id</h3>
                </div>

                <div class="panel-body" style="padding-top:0;">
                    <p>{{ $get_id->mata_diklat_id }}</p>
                </div><!-- panel-body -->

                <hr style="margin:0;">
                <div class="panel-heading" style="border-bottom:0;">
                    <h3 class="panel-title">Judul</h3>
                </div>

                <div class="panel-body" style="padding-top:0;">
                    <p>{{ $get_id->judul }}</p>
                </div><!-- panel-body -->
                <hr style="margin:0;">
                <div class="panel-heading" style="border-bottom:0;">
                    <h3 class="panel-title">Link</h3>
                </div>
                <div class="panel-body" style="padding-top:0;">
                    <p>{{ $get_id->link }}</p>
                </div><!-- panel-body -->
                <hr style="margin:0;">
                <div class="panel-heading" style="border-bottom:0;">
                    <h3 class="panel-title">Dibuat pada</h3>
                </div>
                <div class="panel-body" style="padding-top:0;">
                    <p>{{ $get_id->created_at }}</p>
                </div><!-- panel-body -->

               

            </div>
        </div>
    </div>
</div>

@endsection
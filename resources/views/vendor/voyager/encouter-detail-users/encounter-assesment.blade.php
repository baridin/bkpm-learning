@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' Pengumpulan Jawaban Ujian')

@section('page_header')
    <h1 class="page-title">
        <i class="{{ __('voyager-eyes') }}"></i> {{ __('Menilai Ujian').' '.$use->name }}&nbsp;
        {{-- <a href="" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>{{ __('Tambahkan Nilai') }}</span>
        </a> --}}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
<form id="post-assesment" action="{{url('admin/post-encounter-assesment')}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <!-- form start -->
                    <input type="hidden" name="user_id" value="{{$use->id}}">
                    <input type="hidden" name="encounter_id" value="{{$encounter->id}}">
                    <input type="hidden" name="slug" value="{{ Request::segment(2) }}">
                    @foreach($datas as $kr => $row)
                        <div>
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">{!!$row['soal']!!}</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                    <p>{!!$row['answer']!!}</p>
                            </div><!-- panel-body -->
                            <div class="panel-footer">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" id="sizing-addon{{$kr}}">Nilai</span>
                                    <input type="number" oninput="maxLengthCheck(this)" maxlength="3" min="1" name="value_{{$row['detail_id']}}" max="100" class="form-control" placeholder="Nilai" value="{{$row['nilai']}}" aria-describedby="sizing-addon{{$kr}}"
                                        @if ($row['type'] == 'essay')
                                        @else
                                            readonly
                                        @endif
                                    >
                                </div>
                            </div>
                        </div>
                        <hr style="margin:0;">
                    @endforeach
                </div>
                <a class="btn btn-primary" data-toggle="modal" data-target="#confirm_delete_modal">Simpan</a>
            </div>
        </div>
    </div>
</form>
<div class="modal fade modal-danger" id="confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
            </div>

            <div class="modal-body">
                <h4>{{ __("Anda Yakin Mau Menyimpan Data Nilai Ujian dari {$use->name}") }} '<span class="confirm_delete_name"></span>'</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" onclick="document.getElementById('post-assesment').submit()" class="btn btn-danger" id="confirm_delete">{{ __('Ya Simpan') }}</button>
            </div>

        </div>
    </div>
</div>
<!-- End Delete File Modal -->
@stop

@section('javascript')
    <script type="text/javascript">
        function maxLengthCheck(object)
        {
            if (object.value.length > object.maxLength)
              object.value = object.value.slice(0, object.maxLength)
      }
    </script>
@stop

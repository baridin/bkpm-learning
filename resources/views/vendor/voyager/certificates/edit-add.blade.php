@php
$edit = !is_null($dataTypeContent->getKey());
$add = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
<h1 class="page-title">
    <i class="{{ $dataType->icon }}"></i>
    {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
</h1>
@include('voyager::multilingual.language-selector')
@stop

@section('content')
<!-- form start -->
<form role="form" class="form-edit-add"
    action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
    method="POST" enctype="multipart/form-data">
    <!-- PUT Method if we are editing -->
    @if($edit)
    {{ method_field("PUT") }}
    @endif

    <!-- CSRF TOKEN -->
    {{ csrf_field() }}
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">

                    <div class="panel-body">

                        @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Adding / Editing -->
                        @php
                        $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                        @endphp

                        @foreach($dataTypeRows as $row)
                        <!-- GET THE DISPLAY OPTIONS -->
                        @php
                        $display_options = $row->details->display ?? NULL;
                        if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                        }
                        @endphp
                        @if (isset($row->details->legend) && isset($row->details->legend->text))
                        <legend class="text-{{ $row->details->legend->align ?? 'center' }}"
                            style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">
                            {{ $row->details->legend->text }}</legend>
                        @endif

                            @if ($row->field !== 'details')
                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}"
                                    @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->display_name }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' =>
                                    $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit'
                                    : 'add')])
                                    @elseif ($row->type == 'relationship')
                                        @if ($row->field == 'certificate_belongsto_diklat_detail_relationship')
                                            <select class="form-control select2" name="diklat_detail_id">
                                                @if ($edit)
                                                    <option value="{{$dataTypeContent->diklatDetail->id}}" selected>{{$dataTypeContent->diklatDetail->title}}</option>
                                                @endif
                                            </select>
                                        @else
                                            @include('voyager::formfields.relationship', ['options' => $row->details])
                                        @endif
                                    @else
                                    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif
        
                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                    {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                    @foreach ($errors->get($row->field) as $error)
                                    <span class="help-block">{{ $error }}</span>
                                    @endforeach
                                    @endif
                                </div>
                            @endif
                        @endforeach

                    </div><!-- panel-body -->

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>

                </div>
            </div>
        </div>
        @if ($edit)
        <div id="list-mata-diklat" class="row" style="display: {!! $dataTypeContent->source == 'system' ? 'none' : 'block' !!}">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-head">
                        <div class="panel-title">
                            Nilai Mata Diklat
                            <br>
                            <small>Nilai akhir = Jumlah Nilai / Jumlah Mata Diklat</small>
                        </div>
                    </div>
                    <div class="panel-body">
                        @forelse ($dataTypeContent->diklat->mataDiklat as $mataDiklat)
                            <div class="form-group  col-md-12 ">
                                <label class="control-label" for="name">{{$mataDiklat->title}}</label>
                                <input type="number"
                                    class="form-control" {!! $dataTypeContent->source !== 'system' ? 'autofocus' : '' !!}
                                    name="details[nilai][{{$mataDiklat->id}}]" step="any"
                                    placeholder="Nilai {{$mataDiklat->title}}"
                                    value="{!! isset($dataTypeContent->details) && !empty($dataTypeContent->details) ? $dataTypeContent->details['nilai'][$mataDiklat->id] : 0 !!}"
                                    min="0" max="100">
                            </div>
                        @empty
                            <div class="panel-title text-center">Diklat Tidak di Temukan</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-footer">
                        @section('submit-buttons')
                        <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                        @stop
                        @yield('submit-buttons')
                        <a href="javascript:" class="btn btn-success" onclick="calculateScore()">Kalkulasi Nilai</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
    enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
    <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
    <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
    {{ csrf_field() }}
</form>

<div class="modal fade modal-danger" id="confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
            </div>

            <div class="modal-body">
                <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug: '{{ $dataType->slug }}',
                filename: $file.data('file-name'),
                id: $file.data('id'),
                field: $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            $('select[name="diklat_id"]').on('select2:select', function (e) {
                e.preventDefault()
                var data = e.params.data;
                var optionSelected = $("option:selected", this);
                let formData = new FormData
                if (optionSelected.val() != '') {
                    $.ajax({
                        method: 'GET',
                        url: `{!!url('admin/encouters/relation')!!}?diklat_id=${data.id}&type=get_by_diklat&method=add`,
                        dataType: 'json',
                        success: (res)=>{
                            let html_detail = ``
                            res.results_detail.forEach(detail => {
                                html_detail += `<option value="${detail.id}">${detail.text}<option>`
                            });
                            $('select[name="diklat_detail_id"]').html(html_detail)
                        },
                        error: (err)=>{
                            console.log(err);
                        }
                    })
                }
            });

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();

            $('input[name="source"]').on('change', function () {
                if (this.value === 'manual') {
                    $('#list-mata-diklat').show();
                } else if (this.value === 'system') {
                    $('#list-mata-diklat').hide();
                }
            })
        });

        function calculateScore() {
            var ref = $('input[name^="details"]');
            var targetRef = $('input[name="nilai"]')
            var nilai = 0;
            ref.each(function(detail) {
                nilai += parseInt($(this).val());
            })
            nilai /= ref.length
            alert(`Nilai Akhir: ${nilai}`)
            return targetRef.val(nilai)
        }
    </script>
@stop

@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
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
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

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
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->display_name }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add')])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
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
                                @if ($row->field == 'details')
                                    <div id="detail-options">
                                        @php $options=['a', 'b', 'c', 'd', 'e']; $is_true=null; @endphp
                                        @if (!empty($dataTypeContent->{$row->field}))
                                            @foreach (json_decode($dataTypeContent->{$row->field}) as $k => $v)
                                                @if ($k === 'options')
                                                    @foreach ($options as $ko => $vo)
                                                        @php $val = null; if (isset($v->{$vo})) $val = $v->{$vo}; @endphp
                                                        <div class="form-group  col-md-12 ">
                                                            <div style="display: flex; flex-wrap: wrap; align-items: center; align-content: center; margin-bottom: 10px;">
                                                                <label class="control-label" for="name" style="flex: 1;">Pilihan {{strtoupper($vo)}}</label>
                                                                <input type="checkbox" id="is_active_{{$vo}}" name="is_active[{{$vo}}]" class="toggleswitch" @if(!empty($val)) {{'checked'}} @endif>
                                                            </div>
                                                            <textarea class="form-control" id="textarea_{{$vo}}" name="options[{{$vo}}]" rows="3" placeholder="Pilihan {{$vo}}">{{$val}}</textarea>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach ($options as $ko => $op)
                                                <div class="form-group col-md-12 ">
                                                    <div style="display: flex; flex-wrap: wrap; align-items: center; align-content: center; margin-bottom: 10px;">
                                                        <label class="control-label" for="name" style="flex: 1;">Pilihan {{strtoupper($op)}}</label>
                                                        <input type="checkbox" id="is_active_{{$op}}" name="is_active[{{$op}}]" class="toggleswitch" checked="">
                                                    </div>
                                                    <textarea class="form-control" name="options[{{$op}}]" id="textarea_{{$op}}" rows="3" placeholder="Pilihan {{$op}}"></textarea>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if (!empty($dataTypeContent->{$row->field}))
                                            @isset(json_decode($dataTypeContent->{$row->field})->is_true)
                                                @php $is_true = json_decode($dataTypeContent->{$row->field})->is_true; @endphp
                                            @endisset
                                        @endif
                                        <div class="form-group  col-md-12 ">
                                            <label class="control-label" for="name">Pilihan Benar</label>
                                            <ul class="radio">
                                                @foreach ($options as $op)
                                                    <li id="{{"list-option-type-$op"}}">
                                                        <input type="radio" id="{{"option-type-$op"}}" name="is_true" value="{{strtolower($op)}}" {!! $is_true===$op ? 'checked' : null !!} />
                                                        <label for="{{"option-type-$op"}}">{{strtoupper($op)}}</label>
                                                        <div class="check"></div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('css')
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@stop

@section('javascript')
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
            return function() {
                $file = $(this).siblings(tag);

                params = {
                    slug:   '{{ $dataType->slug }}',
                    filename:  $file.data('file-name'),
                    id:     $file.data('id'),
                    field:  $file.parent().data('field-name'),
                    multi: isMulti,
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text(params.filename);
                $('#confirm_delete_modal').modal('show');
            };
        }

        $('document').ready(function () {

            const typeSoal = '{{$dataTypeContent->type_soal}}';
            if (typeSoal === 'essay') {
                $('#detail-options').hide()
            } else {
                $('#detail-options').show()
            }

            var table = $('#dataTable').DataTable({!! json_encode(
                array_merge([
                    "language" => __('voyager::datatable'),
                    "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!});
            $('.toggleswitch').bootstrapToggle();
            const details = {!!json_encode($dataTypeContent->details)!!};
            const options = ['a', 'b', 'c', 'd', 'e'];
            options.forEach(el => {
                const listen = $(`#is_active_${el}`)
                if (details !== null) {
                    if (listen.prop('checked')) {
                        $(`#list-option-type-${el}`).show()
                        $(`#textarea_${el}`).show()
                    } else {
                        $(`#list-option-type-${el}`).hide()
                        $(`#textarea_${el}`).hide()
                    }
                }
                listen.change(function() {
                    console.log(this, $(this).prop('checked'))
                    if ($(this).prop('checked')) {
                        $(`#list-option-type-${el}`).show()
                        $(`#textarea_${el}`).show()
                    } else {
                        $(`#list-option-type-${el}`).hide()
                        $(`#textarea_${el}`).hide()
                    }
                })
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

            @if($dataTypeContent->type === 'essay')
                $('#detail-options').hide()
            @endif
            $('input[type=radio][name=type_soal]').change(function() {
                if (this.value == 'essay') {
                    $('#detail-options').hide()
                }
                else if (this.value == 'pg') {
                    $('#detail-options').show()
                }
            });
        });
    </script>
@stop

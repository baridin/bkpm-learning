@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('voyager::compass.includes.styles')
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/SnackBar-master/dist/snackbar.min.css') }}">
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
            <form role="form"
                class="form-edit-add"
                action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                method="POST" enctype="multipart/form-data">
                <div class="panel panel-bordered">
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
                                    @if ($row->field == 'encouter_belongsto_diklat_detail_relationship')
                                        <select class="form-control select2" name="diklat_detail_id">
                                            @if ($edit)
                                                <option value="{{$dataTypeContent->detailDiklatId->id}}" selected>{{$dataTypeContent->detailDiklatId->title}}</option>
                                            @endif
                                        </select>
                                    @elseif ($row->field == 'encouter_belongsto_mata_diklat_relationship')
                                        <select class="form-control select2" name="mata_diklat_id">
                                            @if ($edit)
                                                <option value="{{$dataTypeContent->mataDiklatId->id}}" selected>{{$dataTypeContent->mataDiklatId->title}}</option>
                                            @endif
                                        </select>
                                    @else
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @endif
                                @else
                                    @if ($add && $row->field == 'section_id' || $edit && $row->field == 'section_id')
                                        <input type="hidden" name="section_id" value="{{(!empty($section_id))?$section_id:$dataTypeContent->section_id}}">
                                    @elseif ($add && $row->field == 'mata_diklat_id' || $edit && $row->field == 'mata_diklat_id')
                                        <input type="hidden" name="mata_diklat_id" value="{{(!empty($mata_id))?$mata_id:$dataTypeContent->mata_diklat_id}}">
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif
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
                        @endforeach

                        <div id="option-form" class="form-group col-md-12">
                            @foreach ($dataTypeContent->ruleTypeSoal() as $dt)
                                <label for="{{$dt}}-count" class="control-label">Jumlah {{strtoupper($dt)}}</label>
                                <input type="number" name="options[{{$dt}}]" id="{{$dt}}-count" class="form-control" value="{!!$edit ? $dataTypeContent->getOptionsByKey($dt) : 0!!}">
                            @endforeach
                        </div>

                    </div><!-- panel-body -->
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                    </div>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            @if ($edit)
                <div id="bank-soal" class="panel panel-bordered">
                    <div class="panel-title">
                        Bank Soal
                    </div>
                    <div class="panel-body">
                        @foreach ($dataTypeContent->bankSoal($dataTypeContent->mata_diklat_id) as $kbk => $vbk)
                            <div class="collapsible">
                                <div class="collapse-head" data-toggle="collapse" data-target="#{{$kbk}}" aria-expanded="true" aria-controls="{{$kbk}}">
                                    <h4>{{ strtoupper($kbk) }}    <span class="label label-primary">{{$dataTypeContent->countBankSoal($kbk)}}</span></h4>
                                    <i class="voyager-angle-down"></i>
                                    <i class="voyager-angle-up"></i>
                                </div>
                                <div class="collapse-content collapse in" id="{{$kbk}}">
                                  {{--   <button class="btn btn-success" onclick="proses('{{$kbk}}')">Pilih Soal</button> --}}
                                    <div class="table-responsive">
                                        <table class="table table-hover dataTable no-footer">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Type</th>
                                                    <th>Soal</th>
                                                    <th>Dibuat Tgl</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vbk as $kbkd => $vbkd)
                                                    <tr>
                                                        @php
                                                                $is_ready = $dataTypeContent->chosenBankSoalId($vbkd->id); 
                                                                $url = !$is_ready ? route('voyager.exercise-bank-soal.store', $dataTypeContent->id) : route('voyager.exercise-bank-soal.delete', $dataTypeContent->id); /*delete*/
                                                                @endphp
                                                                <td><input type="checkbox" onclick="check({{$vbkd->id}},{{$dataTypeContent->id}},'{{$kbk}}')"
                                                                    @if($is_ready)
                                                                    {{'checked'}}
                                                                    @endif>
                                                                </td>
                                                        <td>{{strtoupper($vbkd->type_soal)}}</td>
                                                        <td>{{$vbkd->soal}}</td>
                                                        <td>{{$vbkd->created_at->format('d-M-Y')}}</td>
                                                        <td>
                                                            <a href="{{route('voyager.bank-soals.edit', $vbkd->id)}}" target="_blank" class="btn btn-sm btn-warning">Edit</a>
                                                            @php
                                                                $is_ready = $dataTypeContent->chosenBankSoalId($vbkd->id); 
                                                                $url = !$is_ready ? route('voyager.exercise-bank-soal.store', $dataTypeContent->id) : route('voyager.exercise-bank-soal.delete', $dataTypeContent->id); /*delete*/
                                                            @endphp
                                                           {{--  <form action="{{ $url }}" method="POST">
                                                                @if ($is_ready)
                                                                    @method('DELETE')
                                                                @endif
                                                                @csrf
                                                                <input type="hidden" name="bank_soal_id" value="{{$vbkd->id}}">
                                                                @if (!$is_ready)
                                                                    <button type="submit" class="btn btn-sm btn-success">Pilih</button>
                                                                @else
                                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                                @endif
                                                            </form> --}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<iframe id="form_target" name="form_target" style="display:none"></iframe>
<form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
        enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
    <input name="image" id="upload_file" type="file"
                onchange="$('#my_form').submit();this.value='';">
    <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
    {{ csrf_field() }}
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

@section('javascript')
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('frontend/SnackBar-master/dist/snackbar.min.js') }}"></script>
    <script type="text/javascript">
        var params = {};
        var $file;

        @if($edit)
            let UJIAN = []
            let PRETEST_POSTEST = []

            @foreach ($dataTypeContent->bankSoal($dataTypeContent->mata_diklat_id) as $kbk => $vbk)
                 @foreach ($vbk as $kbkd => $vbkd)
                        @php
                            $is_ready = $dataTypeContent->chosenBankSoalId($vbkd->id);
                        @endphp
                        @if($is_ready)
                            push({{$vbkd->id}},'{{$kbk}}')
                        @endif
                @endforeach
            @endforeach

            function push(id, type)
            {
                if(type == 'ujian')
                {
                    UJIAN.push(id)
                }
                else
                {
                    PRETEST_POSTEST.push(id)
                }
            }

              function check(id, en, type)
            {
                if(type == 'ujian')
                {
                    if(UJIAN.length > 0)
                    {
                        let status = 0

                        for(let i = 0;i < UJIAN.length; i++)
                        {
                            if(UJIAN[i] == id)
                            {
                                status = 1

                                UJIAN.splice(i, 1)

                                break
                            }
                        }

                        if(status == 0)
                        {
                            UJIAN.push(id)
                        }
                    }
                    else
                    {
                        UJIAN.push(id)
                    }
                }
                else
                {
                    if(PRETEST_POSTEST.length > 0)
                    {
                        let status = 0

                        for(let i = 0;i < PRETEST_POSTEST.length; i++)
                        {
                            if(PRETEST_POSTEST[i] == id)
                            {
                                status = 1

                                PRETEST_POSTEST.splice(i, 1)

                                break
                            }
                        }

                        if(status == 0)
                        {
                            PRETEST_POSTEST.push(id)
                        }
                    }
                    else
                    {
                        PRETEST_POSTEST.push(id)
                    }
                }
                
                let data

                data = PRETEST_POSTEST
                
                 $.ajax({
                    method: 'post',
                    url: '{{route('voyager.exercise-bank-soal.store', $dataTypeContent->id)}}',
                    data: {
                        bank_soal_id: data,
                    },
                    success(data)
                    {
                        // alert('Bank soal berhasil di tambahkan!')
                    },
                    error($xhr)
                    {
                        // alert('Bank soal gagal di tambahkan!')
                        console.log($xhr)
                    }
                })
            }
            function proses(type)
            {
                let data

               
               
                    data = PRETEST_POSTEST
               

                $.ajax({
                    method: 'post',
                    url: '{{route('voyager.exercise-bank-soal.store', $dataTypeContent->id)}}',
                    data: {
                        bank_soal_id: data,
                    },
                    success(data)
                    {
                        alert('Bank soal berhasil di tambahkan!')
                    },
                    error($xhr)
                    {
                        alert('Bank soal gagal di tambahkan!')
                        console.log($xhr)
                    }
                })
            }
        @endif

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
            $('select[name="diklat_id"]').on('change', (e)=>{
                e.preventDefault()
                var optionSelected = $("option:selected", this);
                let formData = new FormData
                // console.log(optionSelected.val());
                if (optionSelected.val() != '') {
                    $.ajax({
                        method: 'GET',
                        url: `{!!url('admin/encouters/relation')!!}?diklat_id=${optionSelected.val()}&type=get_by_diklat&method=add`,
                        dataType: 'json',
                        success: (res)=>{
                            // console.log(res);
                            let html_mata = ``
                            let html_detail = ``
                            res.results_detail.forEach(detail => {
                                html_detail += `<option value="${detail.id}">${detail.text}<option>`
                            });
                            res.results_mata.forEach(mata => {
                                html_mata += `<option value="${mata.id}">${mata.text}<option>`
                            });
                            $('select[name="diklat_detail_id"]').html(html_detail)
                            $('select[name="mata_diklat_id"]').html(html_mata)
                        },
                        error: (err)=>{
                            console.log(err);
                        }
                    })
                }
            })

            $('.toggleswitch').bootstrapToggle();

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

            var table = $('.dataTable').DataTable({!! json_encode(
                array_merge([
                    "language" => __('voyager::datatable'),
                    "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!});

            var $setting = "{!!$dataTypeContent->settings!!}";
            if ($setting === 'manual') { $('div#option-form').hide(); }
            else {
                $('div#bank-soal').hide();
                $('div#option-form').show();
            }
            $('select[name="settings"]').on('select2:select', function (e) {
                var $val = e.params.data.id;
                if ($val == 'otomatis') {
                    $('div#bank-soal').hide();
                    $('div#option-form').show();
                } else if ($val == 'manual') {
                    $('div#bank-soal').show();
                    $('div#option-form').hide();
                }
            });
        });
    </script>
@stop

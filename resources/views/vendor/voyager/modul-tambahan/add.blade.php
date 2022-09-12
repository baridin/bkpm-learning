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
        Tambah Modul Tambahan
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')

<div class="page-content edit-add container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form role="form" class="form-edit-add" action="{{ route('voyager.modul-tambahan.store') }}" method="POST" enctype="multipart/form-data">
                <div class="panel panel-bordered">
                    <!-- PUT Method if we are editing -->

                    <!-- CSRF TOKEN -->
                    @csrf
                    

                    <div class="panel-body">

                        
                        <input type="hidden" name="section_id" id="section_id" value="{{ $section_id  }}">
                        <input type="hidden" name="mata_diklat_id" id="mata_diklat_id" value="{{ $mata_diklat_id }}">

                        <div id="option-form" class="form-group col-md-12">

                            <label class="control-label" for="name">Judul</label> 
                            <input type="text" name="judul" id="judul"  class="form-control">

                        </div>

                            <div id="option-form" class="form-group col-md-12">
                                <label for="link" class="control-label">Konten</label>
                                <textarea name="link" id="link" id="richtextdescription" class="form-control richTextBox"></textarea>
                            </div>

                        </div>

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">Simpan</button>
                        </div>

                        <iframe id="form_target" name="form_target" style="display:none"></iframe>

                    </div>
                </form>
            </div>
            <div class="col-md-12">
            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('frontend/SnackBar-master/dist/snackbar.min.js') }}"></script>
    <script type="text/javascript">
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '',
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

            var $setting = "";
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

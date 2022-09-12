@extends('voyager::master')

{{-- @section('page_title', __('voyager::generic.viewing').' User Konfirmasi') --}}

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ __('icon voyager-person') }}"></i> Peserta Konfirmasi Dokumen
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="select_all">
                                        </th>
                                        <th>Peserta</th>
                                        <th>Diklat</th>
                                        <th>Angkatan</th>
                                        <th>File</th>
                                        <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $d)
                                        @foreach ($d->getDiklatDetail as $dg)
                                            @if ($dg->pivot->status == 1 && !empty($dg->pivot->file))
                                                @php
                                                    $user = $d;
                                                    $diklat = \App\Diklat::findOrFail($dg->pivot->diklat_id);
                                                    $detail = \App\DiklatDetail::findOrFail($dg->pivot->diklat_detail_id);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="row_id" id="checkbox_{{ $dg->pivot->id }}" value="{{ $dg->pivot->id }}">
                                                    </td>
                                                    <td>
                                                        <p>Nama: <a href="{{route('voyager.users.show', $user->id)}}" target="_blank">{{$user->name}}</a></p>
                                                        <p>Kategori: {{($user->category_id == 1)?'ASN':'NON ASN'}}</p>
                                                        <p>Instansi: {{$user->dept}}</p>
                                                    </td>
                                                    <td>{{$diklat->title}}</td>
                                                    <td>{{integerToRoman($detail->force)}}</td>
                                                    <td>
                                                        <a href="{{Storage::disk('local')->url($dg->pivot->file)}}" target="_blank" class="btn btn-sm btn-success">{{__('Lihat File Persyaratan')}}</a>
                                                    </td>
                                                    <td class="no-sort no-click" id="bread-actions">
                                                        <button type="button" title="Lihat" class="btn btn-sm btn-warning pull-right view" data-toggle="modal" data-target="#modalAttach{{ $dg->pivot->id }}">
                                                            <span class="hidden-xs hidden-sm">Terima</span>
                                                        </button>  
                                                        <button type="button" title="Lihat" class="btn btn-sm btn-danger pull-right view" data-toggle="modal" data-target="#modalDettach{{ $dg->pivot->id }}">
                                                            <span class="hidden-xs hidden-sm">Tolak</span>
                                                        </button>
                                                        <button type="button" title="Lihat" class="btn btn-sm btn-info pull-right view" data-toggle="modal" data-target="#modalRevisi{{ $dg->pivot->id }}">
                                                            <span class="hidden-xs hidden-sm">Revisi</span>
                                                        </button>
                                                        {{-- Single attach modal --}}
                                                        <div class="modal modal-danger fade" tabindex="-1" id="modalAttach{{ $dg->pivot->id }}" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title"><i class="voyager-trash"></i> </h4>
                                                                    </div>
                                                                        <form action="{{route('voyager.users.post-document', [$user->id, $dg->pivot->id])}}" id="delete_form" method="POST" enctype="multipart/form-data">
                                                                            {{ csrf_field() }}
                                                                            <div class="modal-body">
                                                                                <input type="hidden" name="type" value="attach">
                                                                                <div class="form-group col-md-12">
                                                                                        <label class="control-label" for="attachment">Kirim File</label>
                                                                                        <input type="file" name="attachment" id="attachment" class="form-control" >
                                                                                </div>
                                                                                <div class="form-group col-md-12">
                                                                                    <textarea name="message" id="message" class="form-control ckeditor" cols="30" rows="10">
                                                                                        <p>
                                                                                            Terima Kasih, dokumen anda telah kami terima. Selanjutnya silahkan mengikuti proses pembelajaran selanjutnya.
                                                                                        </p>
                                                                                    </textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Post">
                                                                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                        {{-- Single dettach modal --}}
                                                        <div class="modal modal-danger fade" tabindex="-1" id="modalDettach{{ $dg->pivot->id }}" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title"><i class="voyager-trash"></i> </h4>
                                                                    </div>
                                                                    <form action="{{route('voyager.users.post-document', [$user->id, $dg->pivot->id])}}" id="delete_form" method="POST">
                                                                        {{ csrf_field() }}
                                                                        <div class="modal-body">

                                                                            <input type="hidden" name="id_diklat" value="{{$dg->pivot->diklat_id}}">
                                                                            <input type="hidden" name="id_diklat_detail" value="{{$dg->pivot->diklat_detail_id}}">
                                                                            <input type="hidden" value="dettach" name="type">
                                                                            <textarea name="message" id="message" class="form-control ckeditor" cols="30" rows="10">
                                                                                <p>
                                                                                    Mohon maaf anda belum dapat mengikuti diklat elearning BKPM. Silahkan lengkapi dokumen anda kembali untuk mendaftar diklat yang lainnya.
                                                                                </p>
                                                                            </textarea>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Post">
                                                                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                                                                        </div>
                                                                    </form>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                        <div class="modal modal-info fade" tabindex="-1" id="modalRevisi{{ $dg->pivot->id }}" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title"><i class="fa fa-send"></i> </h4>
                                                                    </div>
                                                                    <form action="{{route('voyager.users.post-document', [$user->id, $dg->pivot->id])}}" id="revisi_form" method="POST">
                                                                        {{ csrf_field() }}
                                                                        <div class="modal-body">

                                                                            <input type="hidden" name="id_diklat" value="{{$dg->pivot->diklat_id}}">
                                                                            <input type="hidden" name="id_diklat_detail" value="{{$dg->pivot->diklat_detail_id}}">
                                                                            <input type="hidden" value="revisi" name="type">
                                                                            <textarea name="message" id="message" class="form-control ckeditor" cols="30" rows="10">
                                                                                <p>
                                                                                    Silahkan periksa kembali dokumen perlengkapan dan persyaratan dengan baik. Terimakasih.
                                                                                </p>
                                                                            </textarea>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="submit" class="btn btn-info pull-right revisi-confirm" value="Post">
                                                                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                                                                        </div>
                                                                    </form>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@stop

@section('javascript')
    <!-- DataTables -->
    {{-- @dump(config('dashboard.data_tables.responsive')) --}}
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script>
        $(document).ready(function () {
            CKEDITOR.replaceClass = 'ckeditor';
            var table = $('#dataTable').DataTable({!! json_encode(
                array_merge([
                    "language" => __('voyager::datatable'),
                    "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!});
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_modal').modal('show');
        });
        $('input[name="row_id"]').on('change', function () {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });
    </script>
@stop

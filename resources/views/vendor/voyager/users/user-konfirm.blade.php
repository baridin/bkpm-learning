@extends('voyager::master')

{{-- @section('page_title', __('voyager::generic.viewing').' User Konfirmasi') --}}

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ __('icon voyager-person') }}"></i> User Konfirmasi
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
                                        <th>Nama</th>
                                        <th>Nip</th>
                                        <th>Jabatan</th>
                                        <th>Bidang</th>
                                        <th>Instansi</th>
                                        <th>Email</th>
                                        <th>Telepon</th>
                                        <th>Kategori</th>
                                        <th>Diklat</th>
                                        <th>Angkatan</th>
                                        <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $d)
                                    @php
                                    $user = App\User::where('id',$d->user_id)->first();
                                    $diklat = \App\Diklat::findOrFail($d->diklat_id);
                                                $detail = \App\DiklatDetail::findOrFail($d->diklat_detail_id);
                                    @endphp
                                    @if(!empty($user))
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $d->id }}" value="{{ $d->id }}">
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{$user->username}}</td>
                                                <td>{{$user->position}}</td>
                                                <td>{{$user->bagian}}</td>
                                                <td>{{$user->dept}} {{$user->office_city}} {{$user->office_prov}}</td>
                                                <td>{{$user->email}}</td>
                                                <td>{{$user->mobile}}</td>
                                                <td>{{($user->category_id == 1)?'ASN':'NON ASN'}}</td>
                                                <td>{{$diklat->title}}</td>
                                                <td>{{integerToRoman($detail->force)}}</td>

                                                <td class="no-sort no-click" id="bread-actions">
                                                    <button type="button" title="Lihat" class="btn btn-sm btn-warning pull-right attach" data-toggle="modal" data-target="#modalAttach{{ $d->id }}">
                                                        <span class="hidden-xs hidden-sm">Terima</span>
                                                    </button>  
                                                    <button type="button" title="Lihat" class="btn btn-sm btn-danger pull-right dettach" data-toggle="modal" data-target="#modalDettach{{ $d->id }}">
                                                        <span class="hidden-xs hidden-sm">Tolak</span>
                                                    </button>   
                                                    <div class="modal modal-danger fade" tabindex="-1" id="modalAttach{{ $d->id }}" role="dialog">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title"><i class="voyager-trash"></i> </h4>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <form action="{{route('voyager.users.post-konfirm', [$user->id, $d->id])}}" id="delete_form" method="POST" enctype="multipart/form-data">
                                                                        {{ csrf_field() }}
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="id_diklat" value="{{$d->diklat_id}}">
                                                                    <input type="hidden" name="id_diklat_detail" value="{{$d->diklat_detail_id}}">
                                                                            <input type="hidden" name="type" value="attach">
                                                                            <div class="row">
                                                                                <div class="form-group col-md-12">
                                                                                    <label class="control-label" for="attachment">Kirim File</label>
                                                                                    <input type="file" name="attachment" id="attachment" class="form-control" >
                                                                                </div>
                                                                                <div class="form-group col-md-12">
                                                                                    <textarea name="message" id="message" class="form-control ckeditor" cols="30" rows="10">
                                                                                        <p>
                                                                                            Anda berhasil mendaftar diklat elearning BKPM. Silahkan lengkapi dokumen pendaftaran dan tunggu konfirmasi dari panitia.
                                                                                        </p>
                                                                                    </textarea>
                                                                                </div>
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
                                                    <div class="modal modal-danger fade" tabindex="-1" id="modalDettach{{ $d->id }}" role="dialog">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title"><i class="voyager-trash"></i> </h4>
                                                                </div>
                                                                <form action="{{route('voyager.users.post-konfirm', [$user->id, $d->id])}}" id="delete_form" method="POST">
                                                                    {{ csrf_field() }}

                                                                    <input type="hidden" name="id_diklat" value="{{$d->diklat_id}}">
                                                                    <input type="hidden" name="id_diklat_detail" value="{{$d->diklat_detail_id}}">
                                                                    <div class="modal-body">
                                                                        <input type="hidden" value="dettach" name="type">
                                                                        <textarea name="message" id="message" class="form-control ckeditor" cols="30" rows="10">
                                                                            <p>
                                                                                Mohon maaf anda belum dapat mengikuti diklat elearning BKPM dikarenakan kuota sudah terpenuhi. 
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
                                                </td>
                                                


                                        </tr>
                                        @endif
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

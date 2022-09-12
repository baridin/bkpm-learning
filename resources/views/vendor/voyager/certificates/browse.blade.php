@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->display_name_plural)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(isset($dataType->order_column) && isset($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach(Voyager::actions() as $action)
            @if (method_exists($action, 'massAction'))
                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
            @endif
        @endforeach
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <form action="{{route('voyager.certificates.generate-no-certificate')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="panel panel-bordered">
                        <div class="panel-header">
                            <div class="panel-title">Filter dan Upload No. Sertifikat</div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="tandatangan">Diklat</label>
                                <select class="form-control select2" id="diklat" name="diklat" required>
                                    @if (!$selectedDiklatId)
                                        <option value="" selected disabled>--Pilih Diklat--</option>
                                    @endif
                                    @foreach ($diklats as $diklat)
                                        <option value="{{$diklat->id}}" {!! (int)$selectedDiklatId === (int)$diklat->id ? 'selected' : '' !!}>{{$diklat->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tandatangan">Angkatan</label>
                                <select class="form-control select2" id="angkatan" name="angkatan" required>
                                    <option selected>--Pilih Angkatan--</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label" for="name">Nama Metode Pelatihan</label>
                                
                                @if(!empty(Request::get('custom_name_sertif')))
                                <input value="{{ Request::get('custom_name_sertif') }}" required type="text" id="custom_name_sertif" class="form-control" name="custom_name_sertif">
                                @else
                                <input  required type="text" id="custom_name_sertif" class="form-control" name="custom_name_sertif">
                                @endif


                                

                                
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="name">Nomor Sertifikat (.xlsx, .xls)</label>
                                <input required type="file" name="file">
                            </div>
                            <div class="form-group">
                                <a href="https://elearning.bkpm.go.id/storage/diklats/August2022/WF0QJAOp4uSbzZ6eHMAM.xlsx" class="btn btn-primary" download="">Download Template Import (.xlsx, .xls)</a>
                            </div>

                            
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-success save">Upload</button>
                            @if ($selectedDiklatId)
                                {{-- <button type="button" class="btn btn-danger save">{{count($dataTypeContent)}} Peserta</button> --}}
                                <button type="button" class="btn btn-danger save" id="hitung_peserta"> </button>
                                <a href="{{route('voyager.certificates.index')}}" class="btn btn-warning save">Clear Filter</a>
                              <button type="button" id="kirim_persetujuan" class="btn btn-success send">Kirim Persetujuan</button>
                            @endif
                              
                            <p id="ini_diklat"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    
                    <div class="panel-body">
                        @if(Session::has('pesan'))
                        
                    <div class="alert alert-danger" role="alert">
                       {{ Session::get('pesan') }}
                       @php 

                       $no = App\Nosertif::where('status',1)->get();
                       @endphp
                       <br>
                       @foreach($no as $n)
                        {{ $n->nip }}<br>
                       @endforeach
                      </div>
                       @endif
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <select id="search_key" name="key">
                                        @foreach($searchable as $key)
                                            <option value="{{ $key }}" @if($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)){{ 'selected' }}@endif>{{ ucwords(str_replace('_', ' ', $key)) }}</option>
                                        @endforeach
                                    </select>
                                    <select id="filter" name="filter">
                                        <option value="contains" @if($search->filter == "contains"){{ 'selected' }}@endif>contains</option>
                                        <option value="equals" @if($search->filter == "equals"){{ 'selected' }}@endif>=</option>
                                    </select>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}" name="s" value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @if (Request::has('sort_order') && Request::has('order_by'))
                                    <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                    <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                @endif
                            </form>
                        @endif
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        @can('delete',app($dataType->model_name))
                                            <th>
                                                <input type="checkbox" class="select_all">
                                            </th>
                                        @endcan
                                        @foreach($dataType->browseRows as $row)
                                        <th>
                                            @if ($isServerSide)
                                                <a href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
                                            @endif
                                            {{ $row->display_name }}
                                            @if ($isServerSide)
                                                @if ($row->isCurrentSortField($orderBy))
                                                    @if ($sortOrder == 'asc')
                                                        <i class="voyager-angle-up pull-right"></i>
                                                    @else
                                                        <i class="voyager-angle-down pull-right"></i>
                                                    @endif
                                                @endif
                                                </a>
                                            @endif
                                            
                                        </th>
                                        @endforeach
                                        {{-- <th>Nilai</th> --}}
                                        <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                    @php
                                        $cek_certif = App\Certificate::where('id', $data->getKey() )->first();
                                    @endphp
                                    @if($cek_certif->status !== 1)
                                    <tr>
                                        @can('delete',app($dataType->model_name))
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                                            </td>
                                        @endcan
                                        @foreach($dataType->browseRows as $row)
                                            @php
                                            if ($data->{$row->field.'_browse'}) {
                                                $data->{$row->field} = $data->{$row->field.'_browse'};
                                            }
                                            @endphp
                                            <td>
                                                @if (isset($row->details->view))
                                                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field}, 'action' => 'browse'])
                                                @elseif($row->type == 'image')
                                                    <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                                                @elseif($row->type == 'relationship')
                                                    @include('voyager::formfields.relationship', ['view' => 'browse','options' => $row->details])
                                                @elseif($row->type == 'select_multiple')
                                                    @if(property_exists($row->details, 'relationship'))

                                                        @foreach($data->{$row->field} as $item)
                                                            {{ $item->{$row->field} }}
                                                        @endforeach

                                                    @elseif(property_exists($row->details, 'options'))
                                                        @if (!empty(json_decode($data->{$row->field})))
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif
                                                    @endif

                                                    @elseif($row->type == 'multiple_checkbox' && property_exists($row->details, 'options'))
                                                        @if (@count(json_decode($data->{$row->field})) > 0)
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif

                                                @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') && property_exists($row->details, 'options'))

                                                    {!! $row->details->options->{$data->{$row->field}} ?? '' !!}

                                                @elseif($row->type == 'date' || $row->type == 'timestamp')
                                                    {{ property_exists($row->details, 'format') ? \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format) : $data->{$row->field} }}
                                                @elseif($row->type == 'checkbox')
                                                    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                                        @if($data->{$row->field})
                                                            <span class="label label-info">{{ $row->details->on }}</span>
                                                        @else
                                                            <span class="label label-primary">{{ $row->details->off }}</span>
                                                        @endif
                                                    @else
                                                    {{ $data->{$row->field} }}
                                                    @endif
                                                @elseif($row->type == 'color')
                                                    <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                                                @elseif($row->type == 'text')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'text_area')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    @if(json_decode($data->{$row->field}) !== null)
                                                        @foreach(json_decode($data->{$row->field}) as $file)
                                                            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                                                {{ $file->original_name ?: '' }}
                                                            </a>
                                                            <br/>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}" target="_blank">
                                                            Download
                                                        </a>
                                                    @endif
                                                @elseif($row->type == 'rich_text_box')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                                                @elseif($row->type == 'coordinates')
                                                    @include('voyager::partials.coordinates-static-image')
                                                @elseif($row->type == 'multiple_images')
                                                    @php $images = json_decode($data->{$row->field}); @endphp
                                                    @if($images)
                                                        @php $images = array_slice($images, 0, 3); @endphp
                                                        @foreach($images as $image)
                                                            <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
                                                        @endforeach
                                                    @endif
                                                @elseif($row->type == 'media_picker')
                                                    @php
                                                        if (is_array($data->{$row->field})) {
                                                            $files = $data->{$row->field};
                                                        } else {
                                                            $files = json_decode($data->{$row->field});
                                                        }
                                                    @endphp
                                                    @if ($files)
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                            <img src="@if( !filter_var($file, FILTER_VALIDATE_URL)){{ Voyager::image( $file ) }}@else{{ $file }}@endif" style="width:50px">
                                                            @endforeach
                                                        @else
                                                            <ul>
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                                <li>{{ $file }}</li>
                                                            @endforeach
                                                            </ul>
                                                        @endif
                                                        @if (count($files) > 3)
                                                            {{ __('voyager::media.files_more', ['count' => (count($files) - 3)]) }}
                                                        @endif
                                                    @elseif (is_array($files) && count($files) == 0)
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @elseif ($data->{$row->field} != '')
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:50px">
                                                        @else
                                                            {{ $data->{$row->field} }}
                                                        @endif
                                                    @else
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @endif
                                                @else
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <span>{{ $data->{$row->field} }}</span>
                                                @endif
                                            </td>
                                            
                                        @endforeach
                                        @php $certif = \App\Certificate::where('id','=',$data->getKey())->first();
                                        $user = \App\User::find($certif->user_id);
                                        $diklat = \App\Diklat::find($certif->diklat_id);

                                        
                                         @endphp
                                        
                                        <td class="no-sort no-click" id="bread-actions">
                                            @foreach(Voyager::actions() as $action)
                                                @if (!method_exists($action, 'massAction'))
                                                    @include('voyager::bread.partials.actions', ['action' => $action])
                                                @endif
                                            @endforeach
                                            <input type="hidden" id="angkatandiklat" value="{{ $certif->diklat_detail_id }}">
                                            <a href="{{route('voyager.certificates.generate', $data)}}" class="btn btn-sm btn-primary pull-right edit">eCertificate</a>
                                            <a href="{{route('voyager.certificates.transkipadmin', $data)}}" class="btn btn-sm btn-success pull-right edit">Transkrip</a>
                                        </td>
                                    </tr>
                                    @endif
                                  
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        @if ($isServerSide)
                           {{--  <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div> --}}
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search->value,
                                    'filter' => $search->filter,
                                    'key' => $search->key,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder,
                                    'showSoftDeleted' => $showSoftDeleted,
                                ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-body text-center" style="justify-content: center;">
              <div class="loading"></div>
              <br>
              <div clas="loading-txt">
                <p>Harap tunggu sedang memproses dokumen</p>
                <p id="berhasil"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
        <div class="modal fade" id="modalsukses" tabindex="-1" role="dialog" aria-labelledby="modalsuksesLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                   <div class="thank-you-pop">
                    <img width="100" src="http://goactionstations.co.uk/wp-content/uploads/2017/03/Green-Round-Tick.png" alt="">
                    <h1>Berhasi!</h1>
                    <p>Permohonan Berhasil Dikirim</p>
                    {{-- <h3 class="cupon-pop">Your Id: <span>12345</span></h3> --}}

                </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">

@endif
<style type="text/css">
        .select2 {
            width: 100% !important;
        }
        /** SPINNER CREATION **/
        .loading {
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #3498db; /* Blue */
  border-radius: 50%;
  margin-left: 80px;
  width: 90px;
  height: 90px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

        

        @-webkit-keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        /** MODAL STYLING **/
        .modal-content {
            border-radius: 0px;
            box-shadow: 0 0 20px 8px rgba(0, 0, 0, 0.7);
        }

        .modal-backdrop.show {
            opacity: 0.75;
        }

        .loading-txt p {
            font-size: 13px;
            color: #666;
        }

        .loading-txt p small {
            font-size: 11.5px;
            color: #999;
        }

        #output {
            padding: 25px 15px;
            background: #222;
            border: 1px solid #222;
            max-width: 350px;
            margin: 35px auto;
            font-family: 'Roboto', sans-serif !important;
        }

        #output p.subtle {
            color: #555;
            font-style: italic;
            font-family: 'Roboto', sans-serif !important;
        }

        #output h4 {
            font-weight: 300 !important;
            font-size: 1.1em;
            font-family: 'Roboto', sans-serif !important;
        }

        #output p {
            font-family: 'Roboto', sans-serif !important;
            font-size: 0.9em;
        }

        #output p b {
            text-transform: uppercase;
            text-decoration: underline;
        }
    </style>
@stop

@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>

        // $('#kirim_persetujuan').on('click',function(){
        //     alert("OK");
        // })
        // var diklat_id = $("#diklat").val();
        // var angkatanId = $("#angkatandiklat").val();
        // $('#ini_diklat').text(angkatanId);
        // var rowCount = document.getElementById('dataTable').rows.length;
        var rowCount = $("#dataTable > tbody > tr").length;
        $('#hitung_peserta').text(rowCount+ ' Peserta');

        $('#kirim_persetujuan').on('click',function(){

            var custom_name_sertif = $('#custom_name_sertif').val();
            if(custom_name_sertif == ''){
                alert("Nama Nama Metode Sertifikat tidak Boleh kosong");
            }else{



            
          
            $('#modalsukses').modal('hide');
            var diklat_id = $("#diklat").val();
            var diklat_detail_id = $("#angkatandiklat").val();
            
            var text = $("#text").val();
            var passphrase = $("#passphrase").val();
            $.ajax({
                type:'POST',
                url:"{{url('admin/certificates/senddc')}}",
                data:{diklat_id:diklat_id, diklat_detail_id:diklat_detail_id},
                beforeSend: function() {
                    $('#loadMe').modal('show');
                },
                error:function(data, error){
                    $('#loadMe').modal('hide');
                     $('#loadMe').modal('hide');
                        $('#modalsalah').modal('show');
                        setTimeout(function(){ 
                            location.reload(); },
                            3000);
                },
                success:function(data){
                    if(data.success){
                        $('#loadMe').modal('hide');
                        $('#modalsukses').modal('show');
                        $('#berhasil').text(data.success);
                        setTimeout(function(){ 
                            location.reload(); },
                            3000);

                    }else{
                       $('#loadMe').modal('hide');
                     $('#loadMe').modal('hide');
                        $('#modalsalah').modal('show');
                        setTimeout(function(){ 
                            location.reload(); },
                            3000);
                    }

                }
            });

            }


        })
        var $filterSelectAngkatan = $('select#angkatan[name="angkatan"]');
        var $diklatId = parseInt({!!$selectedDiklatId!!}, 10) || 0;
        var diklatDetails = {!!$diklatDetails!!};
        $(document).ready(function () {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => $orderColumn,
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });

            if ($diklatId) {
                var selectedDiklats = diklatDetails.find(function (detail) {
                        return detail.id === $diklatId;
                });
                if (selectedDiklats.angkatan.length > 0) {
                    $filterSelectAngkatan.select2({
                        data: selectedDiklats.angkatan
                    });
                }
            }

            $('select.form-control[name="diklat"]').on('select2:select', function (e) {
                var data = e.params.data;
                var diklatId = parseInt(data.id, 10);
                if (diklatId) {
                    if ($diklatId !== diklatId) {
                        $diklatId = diklatId;
                        $filterSelectAngkatan.html('<option selected>--Pilih Angkatan--</option>').select2();
                    }
                    var selectedDiklats = diklatDetails.find(function (detail) {
                        return detail.id === diklatId;
                    });
                    if (selectedDiklats.angkatan.length > 0) {
                        $filterSelectAngkatan.select2({
                            data: selectedDiklats.angkatan
                        });
                    }
                }
            });
            //ok
            $filterSelectAngkatan.on('select2:select', function (e) {
                
                var data = e.params.data;
                var angkatanId = parseInt(data.id, 10);
                if ($diklatId && angkatanId) {
                    window.location.href = `{!!route('voyager.certificates.index')!!}?diklatId=${$diklatId}&angkatanId=${angkatanId}`;
                }
            })
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', ['id' => '__id']) }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                    }else{
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
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

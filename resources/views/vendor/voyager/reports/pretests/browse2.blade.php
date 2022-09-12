@extends('voyager::master')

@section('page_title', __('Report').' '.ucwords(str_replace('-', ' ', $slug)))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> Report {{ ucwords(str_replace('-', ' ', $slug)) }}
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
                        <form action="{{route('voyager.reports.show', $slug)}}" method="get" id="form-searchs" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingOne">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                        href="#collapseOne" aria-expanded="true"
                                                        aria-controls="collapseOne">
                                                        Diklat
                                                    </a>
                                                    <div class="pull-right" id="c_diklat">
                                                        @if (isset($search->relation['getDiklat']) && count($search->relation['getDiklat'])>0)
                                                            <a href="javascript:" class="badge badge-warning text-white detail">{{count($search->relation['getDiklat'])}} X</a>
                                                        @endif
                                                    </div>
                                                </h4>
                                            </div>
                                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                                aria-labelledby="headingOne">
                                                <div class="panel-body">
                                                    @foreach ($datas['diklat'] as $d)
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="{{$d->id}}" id="diklat" name="relation[getDiklat][]"
                                                                    @if (isset($search->relation['getDiklat']) && in_array($d->id, $search->relation['getDiklat']))
                                                                        checked
                                                                    @endif
                                                                >
                                                                {{$d->title}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingJabatan">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse"
                                                        data-parent="#accordion" href="#collapseJabatan" aria-expanded="false"
                                                        aria-controls="collapseJabatan">
                                                        Jabatan
                                                    </a>
                                                    <div class="pull-right" id="c_position">
                                                        @if (isset($search->inline['position']) && count($search->inline['position'])>0)
                                                            <a href="javascript:" class="badge badge-warning text-white detail">{{count($search->inline['position'])}} X</a>
                                                        @endif
                                                    </div>
                                                </h4>
                                            </div>
                                            <div id="collapseJabatan" class="panel-collapse collapse" role="tabpanel"
                                                aria-labelledby="headingJabatan">
                                                <div class="panel-body">
                                                    @foreach ($datas['position'] as $d)
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="{{$d->title}}" id="position" name="inline[position][]"
                                                                    @if (isset($search->inline['position']) && in_array($d->title, $search->inline['position']))
                                                                        checked
                                                                    @endif
                                                                >
                                                                {{$d->title}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingInstansi">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse"
                                                        data-parent="#accordion" href="#collapseInstansi" aria-expanded="false"
                                                        aria-controls="collapseInstansi">
                                                        Instansi
                                                    </a>
                                                    <div class="pull-right" id="c_instansi"></div>
                                                </h4>
                                            </div>
                                            <div id="collapseInstansi" class="panel-collapse collapse" role="tabpanel"
                                                aria-labelledby="headingInstansi">
                                                <div class="panel-body">
                                                    @foreach ($datas['dept'] as $d)
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="{{$d->title}}" id="instansi" name="inline[info_instansion][]"
                                                                    @if (isset($search->inline['info_instansion']) && in_array($d->title, $search->inline['info_instansion']))
                                                                        checked
                                                                    @endif
                                                                >
                                                                {{$d->title}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingKota">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse"
                                                        data-parent="#accordion" href="#collapseKota" aria-expanded="false"
                                                        aria-controls="collapseKota">
                                                        Kota atau Kabupaten
                                                    </a>
                                                    <div class="pull-right" id="c_city">
                                                        @if (isset($search->inline['office_city']) && count($search->inline['office_city'])>0)
                                                            <a href="javascript:" class="badge badge-warning text-white detail">{{count($search->inline['office_city'])}} X</a>
                                                        @endif
                                                    </div>
                                                </h4>
                                            </div>
                                            <div id="collapseKota" class="panel-collapse collapse" role="tabpanel"
                                                aria-labelledby="headingKota">
                                                <div class="panel-body">
                                                    @foreach ($datas['kota'] as $d)
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="{{$d->nama}}" id="city" name="inline[office_city][]"
                                                                    @if (isset($search->inline['office_city']) && in_array($d->nama, $search->inline['office_city']))
                                                                        checked
                                                                    @endif
                                                                >
                                                                {{$d->nama}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingProvinsi">
                                                <h4 class="panel-title">
                                                    <a class="collapsed" role="button" data-toggle="collapse"
                                                        data-parent="#accordion" href="#collapseProvinsi" aria-expanded="false"
                                                        aria-controls="collapseProvinsi">
                                                        Provinsi
                                                    </a>
                                                    <div class="pull-right" id="c_provinsi">
                                                        @if (isset($search->inline['office_prov']) && count($search->inline['office_prov'])>0)
                                                            <a href="javascript:" class="badge badge-warning text-white detail">{{count($search->inline['office_prov'])}} X</a>
                                                        @endif
                                                    </div>
                                                </h4>
                                            </div>
                                            <div id="collapseProvinsi" class="panel-collapse collapse" role="tabpanel"
                                                aria-labelledby="headingProvinsi">
                                                <div class="panel-body">
                                                    @foreach ($datas['prov'] as $d)
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="{{$d->nama}}" id="provinsi" name="inline[office_prov][]"
                                                                    @if (isset($search->inline['office_prov']) && in_array($d->nama, $search->inline['office_prov']))
                                                                        checked
                                                                    @endif
                                                                >
                                                                {{$d->nama}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer">
                        <div class="clearfix">
                            <button onclick="document.getElementById('form-searchs').submit()" class="btn btn-primary">Cek</button>
                            <button onclick="window.location.href = `{!!route('voyager.reports.show', $slug)!!}`" class="btn btn-danger">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form id="post-excel" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-11 col-11">
                                        <div class="form-group">
                                            <select name="field[]" id="field" multiple class="select2 form-control"  required>
                                                @foreach($dataType->browseRows as $row)
                                                    <option value="{{$row->field}}">{{$row->display_name}}</option>
                                                @endforeach
                                                {{-- <option value="detail">Detail Diklat</option> --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-1">
                                        <a href="javascript:" class="btn btn-success" onclick="toExcel(event)">
                                            Excel
                                        </a>
                                    </div>
                                </div><!-- /input-group -->
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
                                            @if (in_array($row->field, $datas['fieldShow']))
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
                                            @endif
                                        @endforeach
                                        <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                    <tr>
                                        @can('delete',app($dataType->model_name))
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                                            </td>
                                        @endcan
                                        @foreach($dataType->browseRows as $row)
                                            @if (in_array($row->field, $datas['fieldShow']))
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
                                            @endif
                                        @endforeach
                                        <td class="no-sort no-click" id="bread-actions">
                                            @foreach(Voyager::actions() as $action)
                                                @if (!method_exists($action, 'massAction'))
                                                    @include('voyager::bread.partials.actions', ['action' => $action])
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    'relation' => $search->relation,
                                    'inline' => $search->inline,
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
    <!-- Modal Loader -->
    <div class="modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader"></div>
                    <div clas="loader-txt">
                        <p>Harap tunggu Sedang memproses file.. <br><br><small>We are addicted to Bootstrap...
                                #love</small></p>
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
        .loader {
            position: relative;
            text-align: center;
            margin: 15px auto 35px auto;
            z-index: 9999;
            display: block;
            width: 80px;
            height: 80px;
            border: 10px solid rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            border-top-color: #000;
            animation: spin 1s ease-in-out infinite;
            -webkit-animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
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

        .loader-txt p {
            font-size: 13px;
            color: #666;
        }

        .loader-txt p small {
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
    {{-- @dump(config('dashboard.data_tables.responsive')) --}}
    @if(!$dataType->server_side)
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.css"/>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.js"></script>
    @endif
    <script>
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
                $('.select2').select2();
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
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', ['id' => '__id']) }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
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
        function toExcel(e)
        {
            $('#loadMe').modal('show')
            e.preventDefault()
            let $filter = $('form#form-searchs').serializeArray()
            let $excel = $('#field').val()
            let formData = new FormData()
            $.each($filter, function(){
                formData.append(this.name, this.value)
            })
            $excel.forEach(element => {
                formData.append('filter[]', element)
                console.log(element);
                
            });
            console.log(formData)
            $.ajax({
                url: '{!!route('voyager.reports.excel', $slug)!!}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (res)=>{
                    console.log(res);
                    $('#loadMe').modal('hide')
                    window.open(`${res}`, '_blank')
                },
                error: (err)=>{
                    console.log(err);
                }
            })
        }
    </script>
@stop

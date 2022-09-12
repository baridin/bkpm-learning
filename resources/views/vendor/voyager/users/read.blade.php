@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->display_name_singular) }} &nbsp;

        @can('edit', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;
                {{ __('voyager::generic.edit') }}
            </a>
        @endcan
        @can('delete', $dataTypeContent)
            @if($isSoftDeleted)
                <a href="{{ route('voyager.'.$dataType->slug.'.restore', $dataTypeContent->getKey()) }}" title="{{ __('voyager::generic.restore') }}" class="btn btn-default restore" data-id="{{ $dataTypeContent->getKey() }}" id="restore-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.restore') }}</span>
                </a>
            @else
                <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger delete" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
                </a>
            @endif
        @endcan

        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            {{ __('voyager::generic.return_to_list') }}
        </a>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Data Peserta</a></li>
                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Log Peserta</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
                            <div class="panel panel-bordered" style="padding-bottom:5px;">
                                <!-- form start -->
                                @foreach($dataType->readRows as $row)
                                    @php
                                    if ($dataTypeContent->{$row->field.'_read'}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_read'};
                                    }
                                    @endphp
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="panel-title">{{ $row->display_name }}</h3>
                                    </div>
            
                                    <div class="panel-body" style="padding-top:0;">
                                        @if (isset($row->details->view))
                                            @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => 'read'])
                                        @elseif($row->type == "image")
                                            <img class="img-responsive"
                                                    src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                        @elseif($row->type == 'multiple_images')
                                            @if(json_decode($dataTypeContent->{$row->field}))
                                                @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                                    <img class="img-responsive"
                                                            src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
                                                @endforeach
                                            @else
                                                <img class="img-responsive"
                                                        src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                            @endif
                                        @elseif($row->type == 'relationship')
                                                @include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])
                                        @elseif($row->type == 'select_dropdown' && property_exists($row->details, 'options') &&
                                                !empty($row->details->options->{$dataTypeContent->{$row->field}})
                                        )
                                            <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
                                        @elseif($row->type == 'select_multiple')
                                            @if(property_exists($row->details, 'relationship'))
            
                                                @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                                    {{ $item->{$row->field}  }}
                                                @endforeach
            
                                            @elseif(property_exists($row->details, 'options'))
                                                @if (!empty(json_decode($dataTypeContent->{$row->field})))
                                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                                        @if (@$row->details->options->{$item})
                                                            {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{ __('voyager::generic.none') }}
                                                @endif
                                            @endif
                                        @elseif($row->type == 'date' || $row->type == 'timestamp')
                                            {{ property_exists($row->details, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) : $dataTypeContent->{$row->field} }}
                                        @elseif($row->type == 'checkbox')
                                            @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                                @if($dataTypeContent->{$row->field})
                                                <span class="label label-info">{{ $row->details->on }}</span>
                                                @else
                                                <span class="label label-primary">{{ $row->details->off }}</span>
                                                @endif
                                            @else
                                            {{ $dataTypeContent->{$row->field} }}
                                            @endif
                                        @elseif($row->type == 'color')
                                            <span class="badge badge-lg" style="background-color: {{ $dataTypeContent->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
                                        @elseif($row->type == 'coordinates')
                                            @include('voyager::partials.coordinates')
                                        @elseif($row->type == 'rich_text_box')
                                            @include('voyager::multilingual.input-hidden-bread-read')
                                            <p>{!! $dataTypeContent->{$row->field} !!}</p>
                                        @elseif($row->type == 'file')
                                            @if(json_decode($dataTypeContent->{$row->field}))
                                                @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}">
                                                        {{ $file->original_name ?: '' }}
                                                    </a>
                                                    <br/>
                                                @endforeach
                                            @else
                                                <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($row->field) ?: '' }}">
                                                    {{ __('voyager::generic.download') }}
                                                </a>
                                            @endif
                                        @else
                                            @include('voyager::multilingual.input-hidden-bread-read')
                                            <p>{{ $dataTypeContent->{$row->field} }}</p>
                                        @endif
                                    </div><!-- panel-body -->
                                    @if(!$loop->last)
                                        <hr style="margin:0;">
                                    @endif
                                @endforeach
            
                            </div>
                        </div>
                        @php
                            $user = \App\User::findOrFail($dataTypeContent->id);
                        @endphp
                        <div role="tabpanel" class="tab-pane" id="profile">
                            <div class="row">
                                <div class="col-md-3">
                                    <div>
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-stacked" role="tablist">
                                            @foreach ($user->monitorLog->unique('type') as $ku => $um)
                                                <li role="presentation" class="{{($ku==0)?'active':''}}"><a href="#homes{{$um->type}}" aria-controls="homes{{$um->type}}" role="tab" data-toggle="tab">{{ucwords($um->type)}}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        @foreach ($user->monitorLog->unique('type') as  $ku => $um)
                                        <div role="tabpanel" class="tab-pane {{($ku==0)?'active':''}}" id="homes{{$um->type}}">
                                            <div class="panel panel-bordered">
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover dataTable">
                                                            <thead>
                                                                <tr>
                                                                    <th>IP</th>
                                                                    <th>Content</th>
                                                                    <th>Waktu</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($user->monitorLog()->where('monitor_logs.type', $um->type)->get() as $mi)
                                                                    <tr>
                                                                        <td>{{$mi->ip_address}}</td>
                                                                        <td>{{ucwords(str_replace('_', ' ', $mi->type_detail))}}</td>
                                                                        <td>{{\Carbon\Carbon::parse($mi->created_at)->format('d M Y H:i')}}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->display_name_singular) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.css"/>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysiwyg/0.3.3/bootstrap3-wysihtml5.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysiwyg/0.3.3/amd/bootstrap3-wysihtml5.all.min.js"></script>
    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>
        <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) {
                // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');

            $('#delete_modal').modal('show');
        });
        $(function(){
            var table = $('.dataTable').DataTable({!! json_encode(
                array_merge([
                    "language" => __('voyager::datatable'),
                    "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!});
        })
    </script>
@stop

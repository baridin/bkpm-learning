@foreach ($datas->search->relation['getDiklat'] as $d)
    @php $diklat = \App\Diklat::findOrFail((int)$d); @endphp
    @if (isset($datas->search->relation["getDiklatDetail{$diklat->getKey()}"]))
        @foreach ($diklat->diklatDetail()->whereIn('diklat_details.id', array_map('intval', $datas->search->relation["getDiklatDetail{$diklat->getKey()}"]))->get() as $dd)
            <table class="paleBlueRows">
                <thead>
                    <tr>
                        <th>Diklat: <b>{{$diklat->title}}</b></th>
                    </tr>
                    <tr>
                        <th>Angkatan: <b>{{$dd->title.' - '.strftime('%Y', strtotime($dd->start_at))}}</b></th>
                    </tr>
                </thead>
            </table>
            @php $no = 0; @endphp
            
                <table class="paleBlueRows">
                    <thead>
                        
                        <tr>
                            <th>No</th>
                            @foreach ($datas->dataType->browseRows as $row)
                                @if (in_array($row->field, $fields))
                                    <th>{{$row->display_name}}</th>
                                @endif
                            @endforeach
                            @foreach ($diklat->mataDiklat as $dm)
                            <th><b>{{$dm->title}}</b></th>
                            @endforeach
                           {{--  <th>Nilai PG</th>
                            <th>Nilai Essay</th>
                            <th>Nilai Akhir</th> --}}
                        </tr>
                    </thead>
                    @php $i = 0; @endphp
                    <tbody>
                        @foreach($dd->users(1)->get() as $data)
                            <tr>
                                <td>{{(++$i)}}</td>
                                @foreach ($datas->dataType->browseRows as $row)
                                    @if (in_array($row->field, $fields))
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

                                 @foreach ($diklat->mataDiklat as $td)
                            <td>{{$td->scoreType($data->getKey(), $diklat->id)}}</td>
                                 @endforeach
                                {{-- <td>{{$dm->scoreType($data->getKey(), $diklat->id, 'pg')}}</td>
                                <td>{{$dm->scoreType($data->getKey(), $diklat->id, 'essay')}}</td>
                                <td>
                                    {{$dm->score($data->getKey(), $diklat->id)}}
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            
        @endforeach
    @else
        @foreach ($diklat->diklatDetail as $dd)
            <table class="paleBlueRows">
                <thead>
                    <tr>
                        <th>Diklat: <b>{{$diklat->title}}</b></th>
                    </tr>
                    <tr>
                        <th>Angkatan: <b>{{$dd->title.' - '.strftime('%Y', strtotime($dd->start_at))}}</b></th>
                    </tr>
                </thead>
            </table>
            
                <table class="paleBlueRows">
                    <thead>
                        <tr>
                            
                        </tr>
                        <tr>
                            <th>No</th>
                            @foreach ($datas->dataType->browseRows as $row)
                                @if (in_array($row->field, $fields))
                                    <th>{{$row->display_name}}</th>
                                @endif
                            @endforeach
                            @foreach ($diklat->mataDiklat as $dm)

                            <th><b>{{$dm->title}}</b></th>
                            @endforeach
                            <th>Hasil Total</th>
                            
                        </tr>
                    </thead>
                    @php
                        $i = 0;
                    @endphp
                    <tbody>
                        @foreach($dd->users(1)->get() as $data)
                            <tr>
                                <td>{{(++$i)}}</td>
                                @foreach ($datas->dataType->browseRows as $row)
                                    @if (in_array($row->field, $fields))
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
                                @php $sum = 0;  @endphp
                                 @foreach ($diklat->mataDiklat as $key=> $dk)
                                <td>
                                    {{ $dk->score($data->getKey(), $diklat->id)}}
                                    @php 
                                     $sum+=  $dk->score($data->getKey(), $diklat->id); 
                                    
                                    @endphp
                                </td>

                                @endforeach
                                <td>{{ $sum }}</td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
          
        @endforeach
    @endif
@endforeach
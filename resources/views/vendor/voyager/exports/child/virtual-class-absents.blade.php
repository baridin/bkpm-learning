@if(isset($datas->search->relation['getDiklat']))
@foreach ($datas->search->relation['getDiklat'] as $o)
    @php $diklat = \App\Diklat::findOrFail((int)$o); 
    $getDiklat = $diklat['title']; 
    @endphp


@endforeach
@else

    @php $getDiklat = ""; @endphp

@endif

@if(isset($datas->search->relation['getDiklatDetail']))
@foreach ($datas->search->relation['getDiklatDetail'] as $d)
    @php $diklatDetail = \App\DiklatDetail::findOrFail((int)$d); 
    $diklatDetail = $diklatDetail['title']; 
    @endphp


@endforeach
@else

    @php $diklatDetail = ""; @endphp

@endif
 
 @if(isset($datas->search->relation['getDiklatDetailYear']))
@foreach($datas->search->relation['getDiklatDetailYear'] as $y)
     @php $year = "Tahun ". $y; @endphp
@endforeach
@else
    @php $year = ""; @endphp
@endif

@if(isset($datas->search->relation['getMataDiklat']))
@foreach($datas->search->relation['getMataDiklat'] as $m)
     @php 
     $mataDiklat = \App\MataDiklat::findOrFail((int)$m); 
     $getMataDiklat = "Mata Diklat ".$mataDiklat['title'];
          @endphp
@endforeach
@else
    @php
    $getMataDiklat = "";
     @endphp
@endif

<center><h5>{{ $getDiklat }} {{ $diklatDetail }} {{ $getMataDiklat }} {{ $year }}</h5> </center>
<br>


<table border="1">
    <tr>
        <th>No</th>
        <th>Peserta</th>
        <th>Jabatan</th>
        <th>Instansi</th>
        <th>Waktu Ttd</th>
        <th>Signature</th>

        
       {{--  @foreach ($datas->dataType->browseRows as $row)
            
                <th>{{$row->display_name}}</th>
            
        @endforeach --}}
        
    </tr>
    @php
        $no = 0;
    @endphp
    @foreach($datas->dataTypeContent as $data)
        <tr>
                <td>{{ (++$no) }}</td>
                @php
                    $virtual = \App\VirtualClassAbsent::where('id',$data->getKey())->first();
                    $user = \App\User::where('id',$virtual->user_id)->first();
                @endphp
                <td>{{ $user->name }}</td>
                <td>{{ $user->position }}</td>
                <td>{{ $user->dept }} - {{ $user->office_city }}</td>
                <td>{{ $virtual->created_at }}</td>
                <td><img width="110" src="{!! url('/signature_img//') . '/' . $virtual->signature !!}"></td>

        </tr>
    @endforeach
</table>
@if(isset($datas->search->relation['getDiklat']))
@foreach ($datas->search->relation['getDiklat'] as $d)
    @php $diklat = \App\Diklat::findOrFail((int)$d); 
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

 <table >
    <center> <th  colspan="0">{{ $getDiklat }} {{ $diklatDetail }} {{ $getMataDiklat }} {{ $year }} </th></center>
 </table>
 
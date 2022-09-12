@extends('voyager::master')

@section('page_title','Tanda Tangan Elektronik')

@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> Tandatangan Elektronik
    </h1>
</div>
@stop

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@section('content')

<div class="page-content browse container-fluid">
    @include('voyager::alerts')
    <div  class="row">
        @php $gd = App\DiklatDetail::orderBy('updated_at','desc')->get(); $nomor = 1;  @endphp
        @foreach($gd as $g)
        @php $dik = App\Diklat::where('id',$g->diklat_id)->first();  
        $sertif = App\Certificate::where('status','=','0')->where('diklat_id','=',$g->diklat_id)->where('diklat_detail_id','=',$g->id)->first();
        @endphp
        @if($sertif)
        <div class="modal fade" id="exampleModalCenter{{ $g->diklat_id }}{{ $g->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Konfirmasi Tanda Tangan Elektronik</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form  method="POST">
                            {{ csrf_field() }}
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">PassPhrase</label>
                                <div class="col-sm-10">
                                    <input type="password"  id="passphrase{{ $g->diklat_id }}{{ $g->id }}" class="form-control" name="passphrase" value="#1234Qwer*" id="staticEmail">
                                </div>
                            </div>
                            <input type="hidden" id="diklat_id{{ $g->diklat_id }}{{ $g->id }}" name="diklat_id" value="{{ $g->diklat_id }}">
                            <input type="hidden" id="diklat_detail_id{{ $g->diklat_id }}{{ $g->id }}"  name="diklat_detail_id" value="{{ $g->id }}">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">Keterangan</label>
                                <div class="col-sm-10">
                                    <input type="text" name="text"  id="text{{ $g->diklat_id }}{{ $g->id }}"  class="form-control" id="staticEmail">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" id="button{{ $g->diklat_id }}{{ $g->id }}" class="btn btn-primary">Setujui</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>        

        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div  class="panel-body">
                    <h4> {{ $dik->title }} - {{ $g->title }}</h4>
                    <br>
                    
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-hover">
                            <thead>
                                <tr>
                                    @foreach($dataType->browseRows as $row)
                                    <th>

                                        {{ $row->display_name }}

                                    </th>
                                    @endforeach
                                    {{-- <th>Nilai</th> --}}

                                </tr>
                            </thead>
                            <tbody>

                                <?php  $count1 = 1; $count = 5; ?>
                                @foreach($dataTypeContent->where('status','=','0')->where('diklat_id','=',$g->diklat_id)->where('diklat_detail_id','=',$g->id) as $angka =>  $data)
                                
                                

                                @php $var1 = $count1++; $var2 = $count++; @endphp

                                
                                <tr @if($var1 !== 1 && $var1 !== 2 && $var1 !== 3 && $var1 !== 4 && $var1 !== 5  ) class="more{{ $g->diklat_id }}{{ $g->id }}" id="more{{ $g->diklat_id }}{{ $g->id }}"  @endif>
                                    


                                    @foreach($dataType->browseRows as  $row)
                                    @php
                                    if ($data->{$row->field.'_browse'}) {
                                        $data->{$row->field} = $data->{$row->field.'_browse'};
                                    }
                                    @endphp

                                    <td >
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

                                </tr>
                               
                                
                                
                                
                                    @endforeach

                                    <tr>
                                        <td colspan="10"><button type="button" id="button-more{{ $g->diklat_id }}{{ $g->id }}" style="width: 100%" class="btn btn-info btn-block">Lihat Semuanya</button></td>
                                    </tr>
                                    <tr>
                                        <td colspan="10"><button id="button-back{{ $g->diklat_id }}{{ $g->id }}" style="width: 100%" class="btn btn-warning btn-block">Kembalikan Semuanya</button></td>
                                    </tr>

                                </tbody>
                            </table>
                            
                              
                            {{-- <a href="{{url('admin/digital-signatures/approved', ['diklat_id'=>$g->diklat_id,'diklat_detail_id'=>$g->id])}}" class="btn btn-primary">Setujui</a> --}}
                            {{-- <button type="submit" class="btn btn-primary">Setujui</button> --}}
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter{{ $g->diklat_id }}{{ $g->id }}">
                                Setujui
                          </button>

                        </div>

                    </div>
                </div>
            </div>



            @endif

            @endforeach

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
                    <div clas="loader-txt center">
                        <p>Harap tunggu Sedang memproses Tanda Tangan Elektronik </p>
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
                    <img src="http://goactionstations.co.uk/wp-content/uploads/2017/03/Green-Round-Tick.png" alt="">
                    <h1>Berhasi!</h1>
                    <p>Sertifikat Berhasil disetujui</p>
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
 @foreach($gd as $po)
.more{{ $po->diklat_id}}{{ $po->id}} {display: none;}
@endforeach
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
        /*--thank you pop starts here--*/
.thank-you-pop{
    width:100%;
    padding:20px;
    text-align:center;
}
.thank-you-pop img{
    width:76px;
    height:auto;
    margin:0 auto;
    display:block;
    margin-bottom:25px;
}

.thank-you-pop h1{
    font-size: 42px;
    margin-bottom: 25px;
    color:#5C5C5C;
}
.thank-you-pop p{
    font-size: 20px;
    margin-bottom: 27px;
    color:#5C5C5C;
}
.thank-you-pop h3.cupon-pop{
    font-size: 25px;
    margin-bottom: 40px;
    color:#222;
    display:inline-block;
    text-align:center;
    padding:10px 20px;
    border:2px dashed #222;
    clear:both;
    font-weight:normal;
}
.thank-you-pop h3.cupon-pop span{
    color:#03A9F4;
}
.thank-you-pop a{
    display: inline-block;
    margin: 0 auto;
    padding: 9px 20px;
    color: #fff;
    text-transform: uppercase;
    font-size: 14px;
    background-color: #8BC34A;
    border-radius: 17px;
}
.thank-you-pop a i{
    margin-right:5px;
    color:#fff;
}
#ignismyModal .modal-header{
    border:0px;
}
/*--thank you pop ends here--*/

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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        @foreach($gd as $p)
        $('#button-more{{ $p->diklat_id}}{{ $p->id}}').on('click',function(){

            $('.more{{ $p->diklat_id}}{{ $p->id}}').show();


        })
        $('#button-back{{ $p->diklat_id}}{{ $p->id}}').on('click',function(){

            $('.more{{ $p->diklat_id}}{{ $p->id}}').hide();


        })
        $('#button{{ $p->diklat_id}}{{ $p->id}}').on('click',function(){
            $('#exampleModalCenter{{ $p->diklat_id}}{{ $p->id}}').modal('hide');
            
            var diklat_id = $("#diklat_id{{ $p->diklat_id}}{{ $p->id}}").val();
            var diklat_detail_id = $("#diklat_detail_id{{ $p->diklat_id}}{{ $p->id}}").val();
            var text = $("#text{{ $p->diklat_id}}{{ $p->id}}").val();
            var passphrase = $("#passphrase{{ $p->diklat_id}}{{ $p->id}}").val();
            $.ajax({
                type:'POST',
                url:"{{url('admin/digital-signatures/approved')}}",
                data:{diklat_id:diklat_id, diklat_detail_id:diklat_detail_id, text:text,passphrase:passphrase},
                beforeSend: function() {
                $('#loadMe').modal('show');
                },
                success:function(data){
                    if(data.success){
                       $('#loadMe').modal('hide');
                    $('#modalsukses').modal('show');
                    setTimeout(function(){ 
                        location.reload(); },
                     3000);
                       // location.reload();
                   }else{
                    $('#loadMe').modal('hide');
                    alert("salah");
                   }
                   
                }
            });

            
        })
        @endforeach
        


        function active_inactive(params, el)
        {
            if(el.checked)
            {
                $('#'+params).attr('disabled', false)
            }
            else
            {
                $('#'+params).attr('disabled', true)
            }
        }




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
                url: '',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (res)=>{

$('#loadMe').modal('hide')
window.open(`${res}`, '_blank')
},
error: (err)=>{
    console.log(err);
}
})
        }

        // function setujui(diklat_id,diklat_detail_id){
            
        // }


    </script>
    @stop

{{-- <style type="text/css">
@php $var = $count++;
$var2 = $count1++;
$min = $var  - 5;
@endphp

.more{{  $min }}{{$g->diklat_id}}{{ $g->id}}{
display:none;
}

</style>
@section('javascript')

<script type="text/javascript">
$('#button-more{{ $g->diklat_id}}{{ $g->id}}').on('click',function(){

$('.more{{ $var }}{{ $g->diklat_id}}{{ $g->id}}').show();


})
$('#button-back').on('click',function(){
$('.more6').hide();



})
</script>

@endsection --}}
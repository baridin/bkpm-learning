@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' Penilaian Peserta')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-params"></i> Penilaian {{(isset($title))?ucwords($title):'Ujian'}}
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
                        
                        <button class="btn btn-warning" onclick="openmodal(2)"><i class="voyager-person"></i> Peserta Remedial</button>
                       {{--  <button class="btn btn-primary" onclick="openmodal3()"><i class="voyager-search"></i> Filter</button> --}}
                        @if(isset($type))
                            @if($type == 'belum')
                                <button onclick="openmodal2()" class="btn btn-success"><i class="voyager-upload"></i> Kirim Remedial </button>
                            @endif
                        @endif
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>{{(isset($title))?ucwords($title):'Ujian'}}</th>
                                        <Th>Peserta</Th>
                                        <th class="actions text-right">
                                        @if(isset($type))
                                            @if($type == 'udah')
                                            {{ __('voyager::generic.actions') }}
                                            @else
                                                Status
                                            @endif
                                        @else
                                            Nilai
                                        @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $i = 0; 
                                        // dd($users);
                                    @endphp
                                    @foreach ($users as $ik => $iv)
                                     
                                        <tr>
                                            <td>{{++$i}}</td>
                                            <td>{{$iv['ujian']}}</td>
                                            <td>{{$iv['users']}}</td>
                                            <td>
                                                @if(isset($type))
                                                    @if($type == 'udah')
                                                        <a
                                                        @if (isset($title))
                                                        href="{{url('admin/get-start-bobot-assesment')}}?enc_id={{$iv['id']}}&usr_id={{$iv['user_id']}}"
                                                        @else    
                                                        href="{{url('admin/get-start-assesment')}}?enc_id={{$iv['id']}}&usr_id={{$iv['user_id']}}"
                                                        @endif
                                                        class="btn btn-success btn-add-new pull-right">
                                                            <i class="voyager-plus"></i> <span>{{ __('Nilai') }}</span>
                                                        </a>
                                                    @else
                                                        {{$iv['nilai']}}
                                                    @endif
                                                @else
                                                    <a
                                                    @if (isset($title))
                                                    href="{{url('admin/get-start-bobot-assesment')}}?enc_id={{$iv['id']}}&usr_id={{$iv['user_id']}}"
                                                    @else    
                                                    href="{{url('admin/get-start-assesment')}}?enc_id={{$iv['id']}}&usr_id={{$iv['user_id']}}"
                                                    @endif
                                                    class="btn btn-success btn-add-new pull-right">
                                                        <i class="voyager-plus"></i> <span>{{ __('Nilai') }}</span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        
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
    

    <div class="modal fade" id="modal-cek-remedial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="get" action="/admin/send-remedial">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Form Kirim Ujian Remedial   

                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @php $get_angkatan = App\DiklatDetail::all();
                        $get_mata_diklat = App\MataDiklat::all();
                        $encouters = App\Encouter::all();
                     @endphp
                    <div class="modal-body">
                      
                        <input type="hidden" id="diklat" name="diklat" value="{{ $diklat_form }}">
                        <input type="hidden" id="angkatan" name="angkatan" value="{{ $angkatan_form }}">
                        <input type="hidden" id="mata-diklat" name="mata-diklat" value="{{ $mata_diklat_form }}">
                       
                       
                        <input type="hidden" id="ujian" name="ujian" value="{{ $ujian_form }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tandatangan"> Dimulai Pada</label>
                                    <input type="datetime" class="form-control datepicker" name="start_at" placeholder="Dimulai Pada" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tandatangan"> Durasi Menit</label>
                                    <input type="number" class="form-control" name="durasi" placeholder="Durasi ( Menit) " value="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- DataTables -->
    <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    <script>
        let angkatan = [
            @if(isset($type))
                @foreach($diklatDetail as $a)
                    { id: `{{ $a->id }}`, diklat_id: `{{ $a->diklat_id }}`, title: `{{ $a->title }}` },
                @endforeach
            @endif
        ]

        let mataDiklat = [
            @if(isset($type))
                @foreach($mataDiklat as $m)
                    { id: `{{ $m->id }}`, diklat_id: `{{ $m->diklat_id }}`, mata_id: `{{ $m->mata_diklat_id }}`, title: `{{ $m->title }}` },
                @endforeach
            @endif
        ]

        let ujian = [
            @if(isset($type))
                @foreach($ujian as $uj)
                    { id: `{{ $uj->id }}`, diklat_id: `{{ $uj->diklat_id }}`, diklat_detail_id: `{{ $uj->diklat_detail_id }}`, mata_diklat_id: `{{ $uj->mata_diklat_id }}`, title: `{{ $uj->title }}` },
                @endforeach
            @endif
        ]

        $('#diklat2').on('change', ev =>
        {
            let diklat2 = $('#diklat2').val()

            $('#angkatan2').html('')

            angkatan.map(r =>
            {
                if(r.diklat_id == diklat2)
                {
                    $('#angkatan2').append(`<option value="${r.id}">${r.title}</option>`)
                }
            })

            $('#mata-diklat2').html('')

            mataDiklat.map(r =>
            {
                if(r.diklat_id == diklat2)
                {
                    $('#mata-diklat2').append(`<option value="${r.mata_id}">${r.title}</option>`)
                }
            })
        })

        function openmodal(nomor)
        {

            if(nomor == 1){
                 $('#modal-cek').modal('show');
                 $('#form-filter').attr('action', '/admin/get-doesnt-done-assesment');
                 $('#exampleModalLabel-filter').text("Form User Belum Mengerjakan Ujian");
            }else{
             $('#modal-cek').modal('show');
             $('#form-filter').attr('action', '/admin/get-remedial');
             $('#exampleModalLabel-filter').text("Form User Remedial");
            }
           
        }

        function openmodal2()
        {
            $('#modal-cek-remedial').modal('show')
        }

        

        

        function openmodal3()
        {
            $('#modal-filter').modal('show')
        }

        $('#diklat').on('change', ev =>
        {
            let diklat = $('#diklat').val()

            $('#angkatan').html('<option value="" selected disabled>--Pilih Mata Diklat--</option>')

            for(let i = 0; i < angkatan.length; i++)
            {
                if(Number(angkatan[i].diklat_id) == Number(diklat))
                {
                    $('#angkatan').append(`<option value="${angkatan[i].id}">${angkatan[i].title}</option>`)
                }
            }

            $('#mata-diklat').html('<option value="" selected disabled>--Pilih Mata Diklat--</option>')

            for(let i2 = 0;i2 < mataDiklat.length; i2++)
            {
                if(Number(mataDiklat[i2].diklat_id) == Number(diklat))
                {
                    $('#mata-diklat').append(`<option value="${mataDiklat[i2].mata_id}">${mataDiklat[i2].title}</option>`)
                }
            }
        })

        $('#mata-diklat').on('change', ev =>
        {
            let diklat_id = $('#diklat').val()
            let angkatan_id = $('#angkatan').val()
            let mata_id = $('#mata-diklat').val()

            $('#ujian').html('<option value="" selected disabled>--Pilih Ujian--</option>')

            for(let i = 0;i < ujian.length; i++)
            {
                if(Number(ujian[i].diklat_id) == Number(diklat_id) && Number(ujian[i].diklat_detail_id) == Number(angkatan_id)  && Number(ujian[i].mata_diklat_id) == Number(mata_id))
                {
                    $('#ujian').append(`<option value="${ujian[i].id}">${ujian[i].title}</option>`)
                }
            }
        })

        $(document).ready(function () {
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
    </script>
@stop

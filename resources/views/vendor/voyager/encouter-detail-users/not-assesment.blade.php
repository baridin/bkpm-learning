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
                        <button class="btn btn-warning" onclick="openmodal()"><i class="voyager-person"></i> Peserta Yang Belum Mengerjakan Ujian</button>
                        <button class="btn btn-primary" onclick="openmodal3()"><i class="voyager-search"></i> Filter</button>
                        @if(isset($type))
                            @if($type == 'belum')
                                <button onclick="openmodal2()" class="btn btn-success"><i class="voyager-upload"></i> Kirim Ujian Ulang </button>
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
                                            Status
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
    <div class="modal fade" id="modal-cek" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="get" action="/admin/get-doesnt-done-assesment">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Form User Belum Mengerjakan Ujian
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tandatangan"> Diklat</label>
                                    <select class="form-control select2" id="diklat" name="diklat" required>
                                        <option value="" selected disabled>--Pilih Diklat--</option>
                                        @if(isset($type))
                                            @foreach($diklat as $d)
                                                <option value="{{ $d->id }}">{{ $d->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tandatangan"> Angkatan</label>
                                    <select class="form-control select2" id="angkatan" name="angkatan" required>
                                        <option value="" selected disabled>--Pilih Angkatan--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tandatangan"> Mata Diklat</label>
                                    <select class="form-control select2" id="mata-diklat" name="mata-diklat" required>
                                        <option value="" selected disabled>--Pilih Mata Diklat--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tandatangan"> Ujian</label>
                                    <select class="form-control select2" id="ujian" name="ujian" required>
                                        <option value="" selected disabled>--Pilih Ujian--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-jadwal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="get" id="ggwp" action="/admin/send-encounter">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Form User Belum Mengerjakan Ujian
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if(isset($type))
                            @if($type == 'belum')
                            <input type="hidden" name="diklat" value="{{ $datas['diklat'] }}">
                            <input type="hidden" name="angkatan" value="{{ $datas['angkatan'] }}">
                            <input type="hidden" name="mata-diklat" value="{{ $datas['mata-diklat'] }}">
                            <input type="hidden" name="ujian" value="{{ $datas['ujian'] }}">
                            @endif
                        @endif
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

    <div class="modal fade" id="modal-filter"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="model-title">
                        Filter Ujian
                    </h4>
                </div>
                <div class="modal-footer">
                    @php
                        $diklat = \App\Diklat::all();
                    @endphp
                    <form action="/admin/get-user-assesment-filter" class="text-left">
                        <div class="form-group">
                            <label for="nama">Diklat</label>
                            <select class="form-control" id="diklat2" name="diklat">
                                <option value="">--Pilih Diklat--</option>
                                @foreach($diklat as $d)
                                    <option value="{{ $d->id }}">{{ $d->title }}</option>
                                @endforeach
                            </select>
                            <label for="nama">Angkatan</label>
                            <select class="form-control" id="angkatan2" name="angkatan">
                                
                            </select>
                            <label for="nama">Mata Diklat</label>
                            <select class="form-control" id="mata-diklat2" name="mata-diklat">
                                
                            </select>
                            <br>
                        </div>
                        <input type="submit" class="btn btn-success pull-right" value="Submit">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
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

        function openmodal()
        {
            $('#modal-cek').modal('show')
        }

        function openmodal2()
        {
            $('#modal-jadwal').modal('show')
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

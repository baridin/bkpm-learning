@extends('voyager::master')

@section('page_title', 'Sistem Laporan')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ __('icon') }}"></i> {{ __('Sistem Laporan') }}
        </h1>
        <a href="javascript:" onclick="clearFilter(event)" class="btn btn-primary btn-add-new">
            <i class="voyager-list"></i> <span>{{ __('Reset Filter') }}</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                    href="#collapseOne" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                    Diklat
                                                </a>
                                                <div class="pull-right" id="c_diklat"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                            aria-labelledby="headingOne">
                                            <div class="panel-body">
                                                @foreach ($diklat as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d->title}}" id="diklat" name="diklat">
                                                            {{$d->title}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingTwo">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse"
                                                    data-parent="#accordion" href="#collapseTwo" aria-expanded="false"
                                                    aria-controls="collapseTwo">
                                                    Angkatan
                                                </a>
                                                <div class="pull-right" id="c_detail"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingTwo">
                                            <div class="panel-body">
                                                @foreach ($detail as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d->title}}" id="detail" name="detail">
                                                            {{$d->title}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingThree">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse"
                                                    data-parent="#accordion" href="#collapseThree" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                    Tahun
                                                </a>
                                                <div class="pull-right" id="c_year"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingThree">
                                            <div class="panel-body">
                                                @foreach (['2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'] as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d}}" id="year" name="year">
                                                            {{$d}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingThree">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse"
                                                    data-parent="#accordion" href="#collapseAsn" aria-expanded="false"
                                                    aria-controls="collapseAsn">
                                                    ASN atau NON ASN
                                                </a>
                                                <div class="pull-right" id="c_asn"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseAsn" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingAsn">
                                            <div class="panel-body">
                                                @foreach (['ASN', 'NON ASN'] as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d}}" id="asn" name="asn">
                                                            {{$d}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingJabatan">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse"
                                                    data-parent="#accordion" href="#collapseJabatan" aria-expanded="false"
                                                    aria-controls="collapseJabatan">
                                                    Jabatan
                                                </a>
                                                <div class="pull-right" id="c_position"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseJabatan" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingJabatan">
                                            <div class="panel-body">
                                                @foreach ($position as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d->title}}" id="position" name="position">
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
                                                @foreach ($dept as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d->title}}" id="instansi" name="instansi">
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
                                                <div class="pull-right" id="c_city"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseKota" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingKota">
                                            <div class="panel-body">
                                                @foreach ($kota as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d->nama}}" id="city" name="city">
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
                                                <div class="pull-right" id="c_provinsi"></div>
                                            </h4>
                                        </div>
                                        <div id="collapseProvinsi" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingProvinsi">
                                            <div class="panel-body">
                                                @foreach ($prov as $d)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="{{$d->nama}}" id="provinsi" name="provinsi">
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
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Diklat</th>
                                        <th>Angkatan</th>
                                        <th>Angkatan Tahun</th>
                                        <th>Peserta</th>
                                        <th>NIP</th>
                                        <th>Jabatan</th>
                                        <th>Instansi</th>
                                        <th>Alamat Instansi</th>
                                    </tr>
                                </thead>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.css"/>
@stop

@section('javascript')
    <script type="text/javascript" src="{{asset('js/dataTables.responsive.nightly.js')}}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.19/api/fnFilterClear.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/moment.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/locale/id.js" type="text/javascript"></script>
    <script>
        $(function(){
            var n = document.createElement('script');
            n.setAttribute('language', 'JavaScript');
            n.setAttribute('src', 'https://debug.datatables.net/debug.js');
            document.body.appendChild(n);
        })
        $(document).ready(function () {
            var table = $('#dataTable').DataTable({
                "language":{"sEmptyTable":"Tidak ada data pada tabel","sInfo":"Lihat mulai _START_ hingga _END_ dari _TOTAL_ entri","sInfoEmpty":"Lihat mulai 0 hingga 0 dari 0 entri","sInfoFiltered":"(menyaring dari _MAX_ total entri)","sInfoPostFix":"","sInfoThousands":",","sLengthMenu":"Lihat _MENU_ entri","sLoadingRecords":"Menunggu...","sProcessing":"Memproses...","sSearch":"Cari:","sZeroRecords":"Tidak ada data yang sesuai","oPaginate":{"sFirst":"Pertama","sLast":"Terakhir","sNext":"Selanjutnya","sPrevious":"Sebelumnya"},"oAria":{"sSortAscending":": aktivasi untuk mengurutkan dari kecil ke besar","sSortDescending":": aktivasi untuk mengurutkan dari besar ke kecil"}},
                "dom":"Bfrtip",
                "buttons":["copyHtml5","excelHtml5","csvHtml5","pdfHtml5"],
                "responsive":true,
                "stateSave":true,
                "cache":true,
                "iDisplayLength":30,
                "processing":true,
                "serverSide":true,
                "ajax": {
                    "url": "{!!route('voyager.reports.show', 0)!!}",
                    "dataSrc": ""
                },
                "dataType": "json",
                "sAjaxDataProp": "",
                "columns": [
                    {"data": "no"},
                    {"data": "diklat"},
                    {"data": "angkatan"},
                    {"data": "angkatan_tahun"},
                    {"data": "peserta"},
                    {"data": "nip"},
                    {"data": "jabatan"},
                    {"data": "instansi"},
                    {"data": "alamat_instansi"},
                ],
                "columnDefs": [
                    { "className": "never", "targets": 1 },
                    { "className": "never", "targets": 2 },
                    { "className": "never", "targets": 3 },
                    { "targets": 4, "render": (data, type, row, meta)=>{
                        let types =  '';
                        if (data.category_id == 1) {
                            types = 'ASN';
                        } else {
                            types = 'NON ASN';
                        }
                        return `<a target="_blank" href="{!!url('admin/users')!!}/${data.id}">${data.name}</a><p>Tipe: ${types}</p>`
                    }},
                    { "targets": 8, "render": (data, type, row, meta)=>{
                        return `<p>Type: ${data.city}</p><p>Detail: ${data.prov}</p>`
                    }}
                ]
            });
            $('input:checkbox[name="diklat"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="diklat"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_diklat').html(`<a href="javascript:" onclick="hideCloseFill('diklat')" class="badge badge-warning text-white diklat">${input_minat.length} X</a>`)
                    let html = $('#c_diklat').html(`<a href="javascript:" class="badge badge-warning text-white diklat">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_diklat').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 1, true, false, false, false);
            })
            $('input:checkbox[name="detail"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="detail"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_detail').html(`<a href="javascript:" onclick="hideCloseFill('detail')" class="badge badge-warning text-white detail">${input_minat.length} X</a>`)
                    let html = $('#c_detail').html(`<a href="javascript:" class="badge badge-warning text-white detail">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_detail').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 2, true, false, false, false);
            })
            $('input:checkbox[name="year"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="year"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_year').html(`<a href="javascript:" onclick="hideCloseFill('year')" class="badge badge-warning text-white year">${input_minat.length} X</a>`)
                    let html = $('#c_year').html(`<a href="javascript:" class="badge badge-warning text-white year">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_year').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 3, true, false, false, false);
            })
            $('input:checkbox[name="asn"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="asn"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_asn').html(`<a href="javascript:" onclick="hideCloseFill('asn')" class="badge badge-warning text-white asn">${input_minat.length} X</a>`)
                    let html = $('#c_asn').html(`<a href="javascript:" class="badge badge-warning text-white asn">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_asn').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 4, true, false, false, false);
            })
            $('input:checkbox[name="position"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="position"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_position').html(`<a href="javascript:" onclick="hideCloseFill('position')" class="badge badge-warning text-white position">${input_minat.length} X</a>`)
                    let html = $('#c_position').html(`<a href="javascript:" class="badge badge-warning text-white position">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_position').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 6, true, false, false, false);
            })
            $('input:checkbox[name="instansi"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="instansi"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_instansi').html(`<a href="javascript:" onclick="hideCloseFill('instansi')" class="badge badge-warning text-white instansi">${input_minat.length} X</a>`)
                    let html = $('#c_instansi').html(`<a href="javascript:" class="badge badge-warning text-white instansi">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_instansi').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 7, true, false, false, false);
            })
            $('input:checkbox[name="city"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="city"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_city').html(`<a href="javascript:" onclick="hideCloseFill('city')" class="badge badge-warning text-white city">${input_minat.length} X</a>`)
                    let html = $('#c_city').html(`<a href="javascript:" class="badge badge-warning text-white city">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_city').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 8, true, false, false, false);
            })
            $('input:checkbox[name="provinsi"]').on('change', function(e){
                e.preventDefault()
                let input_minat = $('input:checkbox[name="provinsi"]:checked');
                let types = input_minat.map(function() {
                    return '^' + this.value + '\$';
                }).get().join('|');
                console.log(types);
                if (input_minat.length > 0) {
                    // let html = $('#c_provinsi').html(`<a href="javascript:" onclick="hideCloseFill('provinsi')" class="badge badge-warning text-white provinsi">${input_minat.length} X</a>`)
                    let html = $('#c_provinsi').html(`<a href="javascript:" class="badge badge-warning text-white provinsi" onclick="clearFilter('provinsi')">${input_minat.length} X</a>`)
                } else {
                    let html = $('#c_provinsi').html(``)
                }
                
                $('#dataTable').dataTable().fnFilter(types, 8, true, false, false, false);
            })
        })
        function clearFilter(e) {
            e.preventDefault()
            var r = confirm("Anda Yakin Akan Mereset Penyaringan Data?");
            const table = $('#dataTable').dataTable()
            if (r == true) {
                // Remove all filtering
                table.fnFilterClear();
                location.reload()
            } else {
                alert('Penyaringan Data Aman!')
            }
        }
    </script>
@stop

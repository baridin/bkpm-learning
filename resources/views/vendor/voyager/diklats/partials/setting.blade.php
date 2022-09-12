<div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <a href="{{route('voyager.diklat-bobots.create', ['mt_id' => $key])}}" title="Ubah"
                        class="btn btn-sm btn-success">
                        <i class="voyager-plus"></i> <span
                            class="hidden-xs hidden-sm">Tambah Bobot</span>
                    </a>
                    <div class="table-responsive">
                        <table id="" class="table table-hover table-bordered dataTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>judul</th>
                                    <th>Type</th>
                                    <th>Bobot %</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($dataTypeContent->bobots as $dm)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{ucwords($dm->title)}}</td>
                                        <td>
                                            {{ucwords($dm->type)}}
                                        </td>
                                        <td>{{round($dm->bobot)}}</td>
                                        <td class="no-sort no-click" id="bread-actions">
                                            <a href="javascript:;" title="Hapus"
                                                class="btn btn-sm btn-danger pull-right" onclick="document.getElementById('bobotDelete{!!$dm->id!!}').submit()">
                                                <i class="voyager-trash"></i> <span
                                                    class="hidden-xs hidden-sm">Hapus</span>
                                            </a>
                                            <a href="{{route('voyager.diklat-bobots.edit', $dm->id)}}" title="Ubah"
                                                class="btn btn-sm btn-primary pull-right edit">
                                                <i class="voyager-edit"></i> <span
                                                    class="hidden-xs hidden-sm">Ubah</span>
                                            </a>
                                            <form action="{{route('voyager.diklat-bobots.destroy', [$dm->id, 'mt_id'=>$key])}}" id="bobotDelete{!!$dm->id!!}" method="post">
                                                @method('DELETE')
                                                @csrf
                                            </form>
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

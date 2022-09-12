<div class="dd">
    <ol class="dd-list">
        <a href="{{route('voyager.sections.create', ['mt_id' => $key])}}" class="btn btn-sm btn-success"><i class="icon voyager-plus"></i> Materi</a>
        @foreach ($dataTypeContent->sections as $ks => $vs)
            <li class="dd-item" data-id="{{$vs->id}}-{{get_class($vs)}}">
                <div class="pull-right item_actions">
                    <div class="btn btn-sm btn-danger pull-right delete" data-id="{{$vs->id}}" onclick="document.getElementById('deleteSection{{$vs->id}}').submit()">
                        <i class="voyager-trash"></i> Hapus
                    </div>
                    <div class="btn btn-sm btn-primary pull-right edit" data-id="{{$vs->id}}"
                        data-title="{{ucwords($vs->title)}}" data-url="" data-target="_self"
                        data-icon_class="voyager-boat" data-color="" onclick="window.open(`{!!route('voyager.sections.edit', [$vs->id, 'mt_id' => $key])!!}`)"
                        data-parameters="null">
                        <i class="voyager-edit"></i> Ubah
                    </div>
                </div>
                <form id="deleteSection{{$vs->id}}" action="{!!route('voyager.sections.destroy', [$vs->id, 'mt_id' => $key])!!}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('DELETE')
                </form>
                <div class="dd-handle">
                    <span>{{ucwords($vs->title)}}</span>
                </div>
                @if (count($dataTypeContent->sections)>0)
                <ol class="dd-list">
                    <a href="{{route('voyager.materials.create', ['mt_id' => $key, 'st_id' => $vs->id])}}" target="_blank" class="btn btn-sm btn-success"><i class="icon voyager-plus"></i> Video</a>
                    <a href="{{route('voyager.materials.create', ['mt_id' => $key, 'st_id' => $vs->id])}}" target="_blank" class="btn btn-sm btn-danger"><i class="icon voyager-plus"></i> PDF</a>
                    <a href="{{route('voyager.exercises.create', ['mt_id' => $key, 'st_id' => $vs->id])}}" class="btn btn-sm btn-primary"><i class="icon voyager-plus"></i> Latihan</a>
                    <a href="{{route('voyager.modul-tambahan.create', ['mt_id' => $key, 'st_id' => $vs->id])}}" class="btn btn-sm btn-warning"><i class="icon voyager-plus"></i> Modul Tambahan</a>
                    @foreach ($vs->materials as $km => $vm)
                        <li class="dd-item" data-id="{{$vm->id}}-{{get_class($vm)}}">
                            <div class="pull-right item_actions">
                                <div class="btn btn-sm btn-danger pull-right delete" data-id="{{$vm->id}}"
                                    onclick="document.getElementById('formDestroyMaterial{!!$vm->id!!}').submit()">
                                    <i class="voyager-trash"></i> Hapus
                                </div>
                                <div class="btn btn-sm btn-primary pull-right edit" data-id="{{$vm->id}}"
                                    data-title="Menu Builder" data-url="" data-target="_self"
                                    data-icon_class="voyager-list" data-color=""
                                    data-route="voyager.materials.edit" data-parameters="null"
                                    onclick="window.location.href = `{!!route('voyager.materials.edit', [$vm->id, 'mt_id' => $key, 'st_id' => $vs->id])!!}`">
                                    <i class="voyager-edit"></i> Ubah
                                </div>
                                <div class="btn btn-sm btn-warning pull-right edit" data-id="{{$vm->id}}"
                                    data-title="Menu Builder" data-url="" data-target="_self"
                                    data-icon_class="voyager-list" data-color=""
                                    data-route="voyager.materials.edit" data-parameters="null"
                                    onclick="window.location.href = `{!!route('voyager.materials.show', [$vm->id, 'mt_id' => $key, 'st_id' => $vs->id])!!}`">
                                    <i class="voyager-read"></i> Lihat
                                </div>
                                 {{-- <a href="{{route('voyager.materials.show', $vs->id)}}" class="btn btn-sm btn-warning pull-right">
                                    Lihat
                                </a> --}}
                            </div>
                            <div class="dd-handle">
                                <span class="label label-success">{{$vm->type}}</span> <span>{{ucwords($vm->title)}}</span>
                            </div>
                            <form id="formDestroyMaterial{!!$vm->id!!}" action="{!!route('voyager.materials.destroy', $vm->id)!!}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="mt_id" value="{{$key}}">
                            </form>
                        </li>
                    @endforeach
                    @foreach ($vs->exercieses as $ke => $ve)
                        <li class="dd-item" data-id="{{$ve->id}}-{{get_class($ve)}}">
                            <div class="pull-right item_actions">
                                <div class="btn btn-sm btn-danger pull-right delete" data-id="{{$ve->id}}"
                                        onclick="document.getElementById('formDestroyExercises{!!$ve->id!!}').submit()">
                                    <i class="voyager-trash"></i> Hapus
                                </div>
                                <div class="btn btn-sm btn-primary pull-right edit" data-id="{{$ve->id}}"
                                    data-title="Menu Builder" data-url="" data-target="_self"
                                    data-icon_class="voyager-list" data-color=""
                                    data-route="voyager.menus.index" data-parameters="null"
                                    onclick="window.open(`{!!route('voyager.exercises.edit', $ve->id)!!}`, '_blank')">
                                    <i class="voyager-edit"></i> Ubah
                                </div>
                                <a href="{{route('voyager.exercises.show', $ve->id)}}" class="btn btn-sm btn-warning pull-right">
                                    Lihat
                                </a>
                            </div>
                            <div class="dd-handle">
                                    <span class="label label-success">{{$ve->type}}</span> <span>{{ucwords($ve->title)}}</span>
                            </div>
                            <form id="formDestroyExercises{!!$ve->id!!}" action="{!!route('voyager.exercises.destroy', [$ve->id, 'mt_id' => $dataTypeContent->id])!!}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="mata_dilat_id" value="{{$key}}">
                            </form>
                        </li>
                    @endforeach
                    @foreach ($modulTambahan as $ki => $va)
                        @if($va->section_id == $vs->id)
                        <li class="dd-item" data-id="{{$va->id}}-{{get_class($va)}}">
                            <div class="pull-right item_actions">
                                <div class="btn btn-sm btn-danger pull-right delete" data-id="{{$va->id}}"
                                        onclick="document.getElementById('formDestroyModulTambahan{!!$va->id!!}').submit()">
                                    <i class="voyager-trash"></i> Hapus
                                </div>
                                <div class="btn btn-sm btn-primary pull-right edit" data-id="{{$va->id}}"
                                    data-title="Menu Builder" data-url="" data-target="_self"
                                    data-icon_class="voyager-list" data-color=""
                                    data-route="voyager.menus.index" data-parameters="null"
                                    onclick="window.open(`{!!route('voyager.modul-tambahan.edit', $va->id)!!}`, '_blank')">
                                    <i class="voyager-edit"></i> Ubah
                                </div>
                                <a href="{{route('voyager.modul-tambahan.show', $va->id)}}" class="btn btn-sm btn-warning pull-right">
                                    Lihat
                                </a>
                            </div>
                            <div class="dd-handle">
                                    <span class="label label-warning">Link</span> <span>{{ucwords($va->judul)}}</span>
                            </div>
                            <form id="formDestroyModulTambahan{!!$va->id!!}" action="{!!route('voyager.modul-tambahan.destroy', [$va->id, 'mt_id' => $dataTypeContent->id])!!}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="mata_dilat_id" value="{{$key}}">
                            </form>
                        </li>
                        @endif
                    @endforeach
                </ol>
                @endif
            </li>
        @endforeach
    </ol>
</div>

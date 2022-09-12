<div class="sidebar__list">
    @php
        $_s = 0;
    @endphp
    @forelse ($mata->sections as $s)
    <h5 class="title">Modul {{++$_s}} : {{$s->title}}</h5>
    <ul class="bg-white">
        {{-- @dump($s->materials)
        @dump($s->exercieses) --}}
        @foreach ($s->materials as $m)
            <li class="play">
                <a href="{{route('showMaterial', [$diklat->id, $mata->id, 'material', $s->id, $m->id])}}">
                    <ion-icon name="play-circle"></ion-icon> <span class="text">{{ucwords($m->title)}}</span>
                </a>
            </li>
        @endforeach
        @foreach ($s->exercieses as $ex)
            <li class="play">
                <a href="{{route('showMaterial', [$diklat->id, $mata->id, 'latihan', $s->id, $ex->id])}}">
                    <ion-icon name="play-circle"></ion-icon> <span class="text">{{ucwords($ex->title)}}</span>
                </a>
            </li>
        @endforeach

        
    </ul>
    {{-- <div class="divider"></div> --}}
    @empty
    @endforelse
    <h5 class="title">Modul Tambahan</h5>
    <ul class="bg-white">
        @foreach ($modulTambahan as $mt)
            <li class="play">
                <a href="{{route('showMaterial', [$diklat->id, $mata->id, 'modultambahan', $s->id, $mt->id])}}">
                   <i class="fa fa-external-link"></i> <span class="text">{{ucwords($mt->judul)}}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <h5 class="title">Modul Akhir</h5>
    <ul class="bg-white">
        @php $ujian = true; $u_title = []; @endphp
        @foreach ($mata->virtualClass(auth()->id(), $diklat->id)->get() as $v)
            <li class="play">
                <a href="{{route('showMaterial', [$diklat->id, $mata->id, 'virtual-class', $s->id, $v->id])}}">
                    <ion-icon name="play-circle"></ion-icon> <span class="text">{{ucwords($v->title)}}</span>
                </a>
            </li>
            @if (is_null($v->users()->find(auth()->id())))
                @php $ujian = false; array_push($u_title, $v->title); @endphp
            @endif
        @endforeach
        @foreach ($mata->encounters(auth()->id(), $diklat->id)->get() as $en)
            <li class="play">
                <a
                    @if ($ujian)
                        href="{{route('showMaterial', [$diklat->id, $mata->id, 'ujian', $s->id, $en->id])}}"
                    @else
                        href="javascript:" data-toggle="modal" data-target="#ujianModal{{$en->id}}"
                    @endif
                >
                    <ion-icon name="play-circle"></ion-icon> <span class="text">{{ucwords($en->title)}}</span>
                </a>
                <!-- Modal -->
                <div class="modal fade" id="ujianModal{{$en->id}}" tabindex="-1" role="dialog"
                    aria-labelledby="ujianModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                            <h5>Anda masih belum bisa mengikuti ujian {{ucwords($en->title)}}, dikarenakan belum mengikuti virtual class:</h5>
                            <ul class="list-group list-group-flush">
                                @foreach ($u_title as $kt => $vt)
                                    <li class="list-group-item">{{$kt+1}}). {{$vt}}</li>
                                @endforeach
                            </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
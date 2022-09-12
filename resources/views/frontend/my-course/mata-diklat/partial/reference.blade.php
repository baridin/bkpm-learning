<div class="comunication__content">
    <nav>
        <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-referns-tab" data-toggle="tab" href="#nav-referns"
                role="tab" aria-controls="nav-referns" aria-selected="true">Referense</a>
            {{-- <a class="nav-item nav-link" id="nav-note-tab" data-toggle="tab" href="#nav-note"
                role="tab" aria-controls="nav-note" aria-selected="false">Note</a> --}}
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-referns" role="tabpanel"
            aria-labelledby="nav-referns-tab">
            <div class="table-responsive">
                <table class="table table-hovered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @forelse ($refer as $r)
                            <tr>
                                <td>{{++$i}}</td>
                                <td>{{$r->title}}</td>
                                <td>
                                    <a href="{{asset('storage/'.jsonFile($r->file))}}" download="{{asset('storage/'.jsonFile($r->file))}}" class="btn btn-sm btn-primary">Download</a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-note" role="tabpanel" aria-labelledby="nav-note-tab">
            <form class="notes">
                <div class="form-group">
                    <label for="subjectmail">Subject :</label>
                    <input type="text" class="form-control" id="subjectmail"
                        aria-describedby="emailHelp" placeholder="Enter Text">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Message :</label>
                    <textarea type="text" rows="8" cols="100" class="form-control" id="messagearea"
                        placeholder="Enter Your Message"></textarea>
                </div>
                <div class="button text-right">
                    <button type="submit" class="btn btn-primary btn-blue">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
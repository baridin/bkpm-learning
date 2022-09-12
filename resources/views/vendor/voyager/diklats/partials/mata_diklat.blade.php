
<div class="panel-body">
    <div class="table-responsive">
        <table id="" class="table table-hover table-bordered dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mata Diklat</th>
                    <th>Bobot %</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @foreach ($dataTypeContent->mataDiklat as $dm)
                    <tr>
                        <td>{{++$i}}</td>
                        <td>{{ucwords($dm->title)}}</td>
                        <td>
                            {{$dm->pivot->bobot}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

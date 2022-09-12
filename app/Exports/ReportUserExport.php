<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportUserExport implements FromView
{

    protected $data;
    protected $field;

    public function __construct(array $data, array $field)
    {
        $this->data = $data;
        $this->field = $field;
    }

    public function view(): View
    {
        $datas = (object)$this->data;
        $fields = $this->field;
        return view('voyager::exports.index', compact('datas', 'fields'));
    }
}

<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class AreaVisitReport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
    protected $data, $area, $from, $to;

    public function __construct($data, $area, $from, $to)
    {
        $this->data = $data;
        $this->area = $area;
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        return view('dashboards.areavisit-reports.area-visit-report', [
            'list' => $this->data,
            'area' => $this->area,
            'from' => $this->from,
            'to' => $this->to
        ]);
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VisitReport implements FromView
{
    /**
     * @return \Illuminate\Contracts\View\View;
     */
    protected $data, $from, $to;

    public function __construct($data, $from, $to)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
    }
    public function view(): View
    {
        // dd($this->from);
        return view('dashboards.visit-reports.excel-report', [
            'list' => $this->data,
            'from' => $this->from,
            'to' => $this->to
        ]);
    }
}

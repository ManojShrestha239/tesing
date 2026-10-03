<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class VisitMISReport implements FromView
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
        // dd($this->data);
        return view('dashboards.visit-reports.mis-report', [
            'list' => $this->data,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }
}

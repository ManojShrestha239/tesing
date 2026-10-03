<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class VisitDetailedReport implements FromView
{
    /**
     * @return \Illuminate\Contracts\View\View;
     */
    protected $data, $from, $to, $user;
    public function __construct($data, $from, $to, $user)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->user = $user;
    }
    public function view(): View
    {
        // dd($this->data);
        return view('dashboards.visit-reports.visit-excel-report', [
            'list' => $this->data,
            'from' => $this->from,
            'to' => $this->to,
            'user' => $this->user
        ]);
    }
}

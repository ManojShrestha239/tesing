<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class DealerVisitDetailedReport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
    }
    /**
     * @return Illuminate\Contracts\View\View
     */
    protected $data, $dealer, $from, $to;

    public function __construct($data, $dealer, $from, $to)
    {
        $this->data = $data;
        $this->dealer = $dealer;
        $this->from = $from;
        $this->to = $to;
    }

    public function view(): View
    {
        return view('dashboards.dealervisit-reports.dealer-visit-detailed-report', [
            'list' => $this->data,
            'dealer' => $this->dealer,
            'from' => $this->from,
            'to' => $this->to
        ]);
    }
}

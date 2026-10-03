<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderTrackingExport implements FromView
{
    protected $data, $dealer, $from, $to, $month;
    public function __construct($data, $dealer, $from, $to, $month)
    {

        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->dealer = $dealer;
        $this->month = $month;
    }

    public function view(): View
    {
        return view('dashboards.orders.ordertrackingExport', [

            'lists' => $this->data,
            'dealer' => $this->dealer,
            'from' => $this->from,
            'to' => $this->to,
            'month' => $this->month,

        ]);
    }
}

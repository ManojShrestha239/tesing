<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class OrderCollectionExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data, $dealer, $from, $to, $month ,$year;
    
    public function __construct($data, $dealer, $from, $to, $month, $year)
    {
        $this->data = $data;
        $this->dealer = $dealer;
        $this->from = $from;
        $this->to = $to;
        $this->month= $month;
        $this->year = $year;
    }

    public function view(): View{

        

        return view('dashboards.orders.ordercollectionReport',[

            'list' => $this->data,
            'dealer'=>$this->dealer,
            'from' => $this->from,
            'to' => $this->to,
            'month'=>$this->month,
            'year'=>$this->year,
            // dd($this->year)
        ]);
    }

    
}

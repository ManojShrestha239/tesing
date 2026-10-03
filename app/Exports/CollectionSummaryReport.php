<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Traits\Nepali_Calendar;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CollectionSummaryReport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Nepali_Calendar;

    protected $payments, $status, $from, $to, $user;

    public function __construct($payments, $status, $user, $from, $to)
    {
        $this->status = $status;
        $this->user = $user;
        $this->from = $from;
        $this->to = $to;
        $this->payments = $payments;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('dashboards.payments.collectionSummaryReport', [
            'payments' => $this->payments,
            'from' => $this->from,
            'to' => $this->to,
            'user' => $this->user
        ]);
    }
}

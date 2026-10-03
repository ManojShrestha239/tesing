<?php

namespace App\Exports;

use App\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Traits\Nepali_Calendar;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CollectionReport implements FromView
// class CollectionReport implements FromCollection,WithHeadings,WithEvents
{
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
        return view('dashboards.payments.collectionReportExcel', [
            'payments' => $this->payments,
            'from' => $this->from,
            'to' => $this->to,
            'user' => $this->user
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     // dd($this->user,$this->from,$this->to);
    //     if($this->status=="cheque"){
    //         $status = ['cheque (bearer)','cheque (payee)','cheque','cash (account payee)'];
    //     }
    //     if($this->status=='cash'){
    //         $status = ['cash (regular)','cash (against)'];
    //     }
    //     if($this->status=='token'){
    //         $status = ['token'];
    //     }
    //     $payment = Payment::leftjoin('banks','banks.id','payments.bank_id')
    //     ->join('dealers','dealers.code','payments.dealer_code')
    //     ->select('payments.id','payments.collection_date','dealers.code','dealers.name','dealers.address','payments.receipt_num','payments.payment_type','payments.cheque_num','payments.discount','payments.TDS','payments.payment_date','banks.name as bank_name','payments.amount','payments.status',DB::raw("'' as dep_bank"),DB::raw("'' as dep_date"),'payments.remarks');

    //     if(!empty($this->status)&&!empty($this->user)&&!empty($this->from)&&!empty($this->to)){
    //         $payments = $payment->where(['payments.user_id'=>$this->user])
    //         ->whereBetween('collection_date',[$this->from,$this->to])
    //         ->whereIn('payment_type',$status)
    //         ->get();
    //     }else if(empty($this->status)&&!empty($this->user)&&!empty($this->from)&&!empty($this->to)){
    //         $payments = $payment->where(['payments.user_id'=>$this->user])
    //         ->whereBetween('collection_date',[$this->from,$this->to])
    //         ->get();
    //     }else if(empty($this->status)&&empty($this->user)&&!empty($this->from)&&!empty($this->to)){
    //         $payments = $payment->whereBetween('collection_date',[$this->from,$this->to])
    //         ->get();
    //     }else if(!empty($this->status)&&empty($this->user)&&!empty($this->from)&&!empty($this->to)){
    //         $payments = $payment->whereBetween('collection_date',[$this->from,$this->to])
    //         ->whereIn('payment_type',$status)
    //         ->get();
    //     }else{
    //         $payments = $payment->where('collection_date',date('Y-m-d'))->get();
    //     }
    //     foreach($payments as $payment){
    //         $date = explode('-',$payment->collection_date);
    //         $currentNepaliDay = $this->AD_to_BS($date[0],$date[1],$date[2]);
    //         $nepaliDay = $currentNepaliDay['date'];
    //         if (strlen($nepaliDay) == 1) {
    //             $nepaliDay = '0' . $nepaliDay;
    //         }
    //         $nepaliMth = $currentNepaliDay['month'];
    //         if (strlen($nepaliMth) == 1) {
    //             $nepaliMth = '0' . $nepaliMth;
    //         }
    //         $nepaliYr = $currentNepaliDay['year'];
    //         $date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
    //         $payment->collection_date = $date;
    //     }

    //     return $payments;
    // }
    // public function headings():array{
    //     return [
    //         ["Receipt Report"],
    //         [
    //             'SN',
    //             'Date',
    //             'Dealer Code',
    //             'Dealer Name',
    //             'Dealer Address',
    //             'Receipt No.',
    //             'Mode of Payment',
    //             'Cheque No.',
    //             'CD',
    //             'TDS',
    //             'Cheque Date',
    //             'Bank Name',
    //             'Amount',
    //             'Status',
    //             'Dep. Bank',
    //             'Dep. Date',
    //             'Remarks'
    //         ]

    //     ];
    // }
    //         /**
    //  * Write code on Method
    //  *
    //  * @return response()
    //  */
    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class    => function(AfterSheet $event) {

    //             $event->sheet->getDelegate()->getStyle('1')->getFont()->setSize(20);

    //         },
    //     ];
    // }
}

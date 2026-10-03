<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\Orderitem;
use App\Traits\Nepali_Calendar;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

// class OrderReport implements FromCollection,WithHeadings,WithEvents
class OrderReport implements FromView
{
    use Nepali_Calendar;

    protected $order, $title, $from, $to, $dealer;

    public function __construct($order, $title, $dealer, $from, $to)
    {
        $this->order = $order;
        $this->title = $title;
        $this->dealer = $dealer;
        $this->from = $from;
        $this->to = $to;
    }
    /**
     * @return \Illuminate\Support\Collection
     */


    public function view(): View
    {
        return view('dashboards.orders.orderReportExcel', [
            'list' => $this->order,
            'dealer' => $this->dealer,
            'title' => $this->title,
            'from' => $this->from,
            'to' => $this->to
        ]);
    }



    // public function collection()
    // {
    //     $order = Order::join('dealers','dealers.code','orders.dealer_code')
    //     ->join('areas','areas.areacode','dealers.area_code')
    //     ->join('mobileusers','orders.user_id','mobileusers.id')
    //     ->select(
    //'orders.date',
    //'orders.id',
    //'dealers.code',
    //'dealers.name',
    //'dealers.address',
    //'areas.area',
    //DB::raw("0.0 as sales"),
    //DB::raw("0.0 as outstanding"),
    //DB::raw("0.0 as od"),
    //DB::raw("0.0 as ageing"),
    //"orders.total_price",
    //"mobileusers.name as orderBy"
    //,DB::raw("'n-a' as payment_date"),
    //"dealers.order_limit",
    //"orders.remarks"
    //);
    //     if(empty($this->user) && empty($this->from) && empty($this->to)){
    //         $order = $order->get();
    //     }else if(!empty($this->user) && empty($this->from) && empty($this->to)){
    //         $order = $order->where('orders.dealer_code',$this->user)
    //         ->get();
    //     }elseif(empty($this->user) && !empty($this->from) && !empty($this->to)){
    //         $order = $order->whereBetween('orders.date',[$this->from,$this->to])
    //         ->get();
    //     }else{
    //         $order = $order->where('orders.dealer_code',$this->user)
    //         ->whereBetween('orders.date',[$this->from,$this->to])
    //         ->get();
    //     }
    //     foreach($order as $ord){
    //         $date = explode('-',$ord->date);
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
    //         $ord->date = $date;
    //     }
    //     return $order;

    // }
    // public function headings():array{
    //     return [
    //         ["Order Tracking  Report"],
    //         [
    //             'Date',
    //             'SN',
    //             'Dealer Code',
    //             'Dealer Name',
    //             'Dealer Address',
    //             'Area',
    //             'Sales',
    //             'Total Outstanding Dues',
    //             'Over 90 Days OD','Ageing',
    //             'Order Value',
    //             'Order By',
    //             'Payment Commitment Date',
    //             'Credit Limit',
    //             'Remarks'
    //         ]

    //     ];
    // }
    /**
     * Write code on Method
     *
     * @return response()
     */
    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class    => function(AfterSheet $event) {

    //             $event->sheet->getDelegate()->getStyle('1')->getFont()->setSize(20);

    //         },
    //     ];
    // }
}

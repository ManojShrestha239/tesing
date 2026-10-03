<?php

namespace App\Http\Controllers;

use App\Exports\OrderCollectionExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderReport;
use App\Exports\OrderTrackingExport;
use App\Models\Area;
use App\Models\Dealer;
use App\Models\Order;
use App\Models\Payment;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderReportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:dealers,code',
            'from' => ['nullable', 'nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'from' => ['nullable', 'nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
        ]);
        $from = '';
        $to = '';
        if ($request->from) {
            $from = $this->custom_BS_to_AD($request->from);
        }
        if ($request->to) {
            $to = $this->custom_BS_to_AD($request->to);
        }
        $title = "Test title";
        $user = $request->user;
        $from = $from;
        $to = $to;

        $order = Order::join('dealers', 'dealers.code', 'orders.dealer_code')
            ->join('areas', 'areas.areacode', 'dealers.area_code')
            ->join('mobileusers', 'orders.user_id', 'mobileusers.id')
            ->select('orders.date', 'orders.id', 'dealers.code', 'dealers.name', 'dealers.address', 'areas.area', DB::raw("0.0 as sales"), DB::raw("0.0 as outstanding"), DB::raw("0.0 as od"), DB::raw("0.0 as ageing"), "orders.total_price", "mobileusers.name as orderBy", DB::raw("'n-a' as payment_date"), "dealers.order_limit", "orders.remarks");
        if (empty($user) && empty($from) && empty($to)) {
            $order = $order->get();
        } else if (!empty($user) && empty($from) && empty($to)) {
            $order = $order->where('orders.dealer_code', $user)
                ->get();
        } elseif (empty($user) && !empty($from) && !empty($to)) {
            $order = $order->whereBetween('orders.date', [$from, $to])
                ->get();
        } else {
            $order = $order->where('orders.dealer_code', $user)
                ->whereBetween('orders.date', [$from, $to])
                ->get();
        }

        // ob_end_clean();
        // ob_start();
        return Excel::download(new OrderReport($order, $title, $user, $from, $to), 'data.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request, [
            'status' => 'nullable|in:Pending,ongoing,approved,completed,cancelled,order',
            'user' => 'nullable|exists:dealers,code',
            'from' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'to' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
        ]);
        $from = '';
        $to = '';
        if ($request->status) {
            if ($request->status == "Pending" || $request->status == "pending") {
                $status = $request->status;
            } else if ($request->status == "ongoing" || $request->status == "Ongoing") {
                $status = 'Sent to Dispatch';
            } else if ($request->status == 'cancelled' || $request->status == "Cancelled") {
                $status = 'Declined';
            } else if ($request->status == 'completed' || $request->status == "Completed") {
                $status = 'Dispatched';
            } else if ($request->status == 'approved' || $request->status == "Approved") {
                $status = "Approved";
            } else {
                $status = "";
            }
        } else {
            $status = "";
        }
        // return $status;
        if ($request->from) {
            $from = $this->custom_BS_to_AD($request->from);
        }
        if ($request->to) {
            $to = $this->custom_BS_to_AD($request->to);
        }
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
        } else {
            $admin_area = Area::pluck('areacode');
        }
        $title = "Test title";
        $user = $request->user;
        // $from = $request->from;
        // $to = $request->to;
        if ($status) {
            $order = Order::join('dealers', 'dealers.code', 'orders.dealer_code')
                ->join('areas', 'areas.areacode', 'dealers.area_code')
                ->join('mobileusers', 'orders.user_id', 'mobileusers.id')
                ->select(
                    'orders.date',
                    'orders.id',
                    'dealers.code',
                    'dealers.name',
                    'dealers.address',
                    'areas.area',
                    DB::raw("0.0 as sales"),
                    DB::raw("0.0 as outstanding"),
                    DB::raw("0.0 as od"),
                    DB::raw("0.0 as ageing"),
                    "orders.total_price",
                    "mobileusers.name as orderBy",
                    DB::raw("'n-a' as payment_date"),
                    "dealers.order_limit",
                    "orders.remarks",
                    "orders.status"
                )
                ->whereIn('orders.areacode', $admin_area)
                ->where('orders.status', $status);
        } else {
            $order = Order::join('dealers', 'dealers.code', 'orders.dealer_code')
                ->join('areas', 'areas.areacode', 'dealers.area_code')
                ->join('mobileusers', 'orders.user_id', 'mobileusers.id')
                ->select(
                    'orders.date',
                    'orders.id',
                    'dealers.code',
                    'dealers.name',
                    'dealers.address',
                    'areas.area',
                    DB::raw("0.0 as sales"),
                    DB::raw("0.0 as outstanding"),
                    DB::raw("0.0 as od"),
                    DB::raw("0.0 as ageing"),
                    "orders.total_price",
                    "mobileusers.name as orderBy",
                    DB::raw("'n-a' as payment_date"),
                    "dealers.order_limit",
                    "orders.remarks",
                    "orders.status"
                )
                ->whereIn('orders.areacode', $admin_area);
        }
        if (empty($user) && empty($from)) {
            $order = $order->get();
        } else if (!empty($user) && empty($from)) {
            $order = $order->where('orders.dealer_code', $user)
                ->get();
        } elseif (empty($user) && !empty($from)) {
            $order = $order->whereBetween('orders.date', [$from, $to])
                ->get();
        } else {
            $order = $order->where('orders.dealer_code', $user)
                ->whereBetween('orders.date', [$from, $to])
                ->get();
        }
        // return $order[2];
        $user = Dealer::where('code', $user)->first();
        // ob_end_clean();
        // ob_start();
        return Excel::download(new OrderReport($order, $title, $user, $request->from, $request->to), 'Order Report.xlsx');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    // public function queue()
    // {
    //     dispatch(new \App\Jobs\OrderItembulkUpdate());

    //     dd('done');
    // }

    public function orderreport(Request $request)
    {
        /*
        in alloder $status = '' , then fetch all status
        in filtered order report if status = order then fetch all status
        in Pending/Inventory order report if status = Pending then fetch only Pending/Sent to Account status
        in Account/Approved order report if status = approved then fetch only Approved/Account status
        in Ongoing order report if status = ongoing then fetch only Sent to Dispatch/Dispatched status
        in Completed order report if status = completed then fetch only Delivered status
        in Cancelled order report if status = cancelled then fetch only Declined status
        in Draft order report if status = draft then fetch only Draft status
        */
        Validator::make($request->all(), [
            'status' => 'nullable|in:order,Pending,approved,ongoing,completed,cancelled,draft',
            'user' => 'nullable|exists:dealers,code',
            'from' => ['required', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['required', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ])->validate();

        $filterType = strtolower(trim($request->status));

        $statusMap = [
            'pending'       => ['Pending', 'Sent to Account'],
            'approved'      => ['Approved', 'Account'],
            'ongoing'       => ['Sent to Dispatch', 'Dispatched'],
            'cancelled'     => ['Declined'],
            'completed'     => ['Delivered'],
            'urgent order'  => ['Urgent Order'],
            'draft'         => ['Draft'],
        ];

        $status = $statusMap[$filterType] ?? '';

        $from = $to = '';

        if ($request->from || $request->to) {
            $from = $this->custom_BS_to_AD($request->from);
            $to   = $this->custom_BS_to_AD($request->to);
        }

        $admin_area = Auth::user()->hasRole('ASM')
            ? collect(Auth::user()->area)->pluck('areacode')
            : Area::pluck('areacode');


        $dealer = $request->user;
        if ($status) {
            $order = Order::with(['dealer', 'area', 'user'])
                ->select('orders.*', DB::raw('sum(total_price)as total_collection'))
                ->whereBetween('orders.date', [$from, $to])
                ->where('orders.status', $status)
                ->whereIn('areacode', $admin_area)
                ->groupBy('dealer_code', 'date')
                ->orderBy('date', "asc");
        } else {
            $order = Order::with(['dealer', 'area', 'user'])
                ->select('orders.*', DB::raw('sum(total_price)as total_collection'))
                ->whereBetween('orders.date', [$from, $to])
                ->whereIn('areacode', $admin_area)
                ->groupBy('dealer_code', 'date')
                ->orderBy('date', "asc");
        }

        $query = Order::with(['dealer', 'area', 'user'])
            ->select('orders.*', DB::raw('sum(total_price) as total_collection'))
            ->whereBetween('orders.date', [$from, $to])
            ->whereIn('areacode', $admin_area)
            ->groupBy('dealer_code', 'date')
            ->orderBy('date', 'asc');

        if ($status) {
            $query->whereIn('orders.status', is_array($status) ? $status : [$status]);
        }

        if ($dealer) {
            $query->where('dealer_code', $dealer);
        }

        $order = $query->get();

        $dealers = $order->pluck('dealer_code')->unique();
        $dates = $order->pluck('date')->unique();

        $pdcPayments = Payment::whereIn('dealer_code', $dealers)
            ->whereIn('collection_date', $dates)
            ->where('payment_type', 'PDC')
            ->select('dealer_code', 'collection_date', DB::raw('sum(amount) as amount'))
            ->groupBy('dealer_code', 'collection_date')
            ->get()
            ->keyBy(fn($p) => "{$p->dealer_code}_{$p->collection_date}");

        $approvedRemarks = Order::whereIn('dealer_code', $dealers)
            ->whereIn('date', $dates)
            ->where('status', 'Approved')
            ->whereNotNull('remarks')
            ->select('dealer_code', 'date', 'remarks')
            ->get()
            ->keyBy(fn($o) => "{$o->dealer_code}_{$o->date}");

        foreach ($order as $odr) {
            $key = "{$odr->dealer_code}_{$odr->date}";
            $odr->pdc = $pdcPayments[$key]->amount ?? 0;
            if ($odr->remarks !== null && $odr->status === 'Approved' && isset($approvedRemarks[$key])) {
                $odr->approvedRemarks = $approvedRemarks[$key]->remarks;
            }
        }

        $from = $request->from;
        $to = $request->to;
        $date = explode('-', $request->to);

        return Excel::download(new OrderTrackingExport($order, $dealer, $from, $to, $this->get_nepali_month($date[1])), $this->get_nepali_month($date[1]) . '_' . 'OrderTrackingExcel_' . time() . '.xlsx');
    }


    public function ordercollection(Request $request)
    {
        // return $request->all();
        Validator::make($request->all(), [
            'status' => 'nullable|in:Pending,pending,approved,Approved,ongoing,Sent to Dispatch,completed,Dispatched,cancelled,Declined,order',
            'user' => 'nullable|exists:dealers,code',
            'from' => ['required', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['required', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ])->validate();
        if ($request->status) {
            if ($request->status == "Pending") {
                $status = $request->status;
            } else if ($request->status == "approved") {
                $status = 'Approved';
            } else if ($request->status == "cancelled") {
                $status = 'Declined';
            } else if ($request->status == "ongoing") {
                $status = "Sent to Dispatch";
            } else if ($request->status == "completed") {
                $status = "Dispatched";
            } else {
                $status = "";
            }
        } else {
            $status = "";
        }

        if (!($request->from || $request->to)) {
            $from = '';
            $to = '';
        } else {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
        }

        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
        } else {
            $admin_area = Area::pluck('areacode');
        }

        $dealer = $request->user;
        if ($status) {
            $order = Order::with(['dealer.collection', 'user', 'area'])
                ->select('orders.*', DB::raw('sum(total_price)as total_amount'), DB::raw('sum(committed_amount)as total_committed_amount'))
                ->where('orders.status', $status)
                ->whereBetween('orders.date', [$from, $to])
                ->whereIn('areacode', $admin_area)
                ->groupBy('dealer_code')
                ->orderBy('areacode');
        } else {
            $order = Order::with(['dealer.collection', 'user', 'area'])
                ->select('orders.*', DB::raw('sum(total_price)as total_amount'), DB::raw('sum(committed_amount)as total_committed_amount'))
                ->whereBetween('orders.date', [$from, $to])
                ->whereIn('areacode', $admin_area)
                ->groupBy('dealer_code')
                ->orderBy('areacode');
        }
        if (!$dealer) {
            $order = $order->get();
        } else {
            $order = $order->where('dealer_code', $dealer)
                ->get();
        }
        $from = $request->from;
        $to = $request->to;

        // get nepali year and month
        $date = explode('-', $request->from);
        $currentYear = $date[0];
        $currentMonth = $date[1];

        // return $order;

        if (isset($dealer)) {
            $dealerName = Dealer::where('code', $dealer)->first()->name;
            return Excel::download(new OrderCollectionExport($order, $dealer, $from, $to, $this->get_nepali_month($currentMonth), $currentYear), $dealerName . '_' . $this->get_nepali_month($currentMonth) . '_' . 'OrderCollection Report_' . time() . '.xlsx');
        }
        return Excel::download(new OrderCollectionExport($order, $dealer, $from, $to, $this->get_nepali_month($currentMonth), $currentYear),  $this->get_nepali_month($currentMonth) . '_' . 'OrderCollection Report_' . time() . '.xlsx');
    }
}

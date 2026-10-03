<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Order;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LogDetail;
use App\Models\Orderitem;
use App\Models\Mobileuser;
use App\Models\ProductSize;
use App\Models\Size;
use App\Traits\FCM;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Str;
use App\Models\Area;
use App\Models\DealerwiseTarget;
use App\Models\Product;
use App\Models\OrderImage;
use App\Models\Payment;
use App\Models\ProductwiseTarget;
use App\Models\TargetInfo;
use App\Models\UserTarget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use FCM;
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function show($id)
    {
        //Todo
        $o = Order::findorFail($id);
        $order = DB::table('orders')
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->join('product_size', 'product_size.id', '=', 'orderitems.items_id')
            ->join('products', 'products.id', '=', 'product_size.product_id')
            ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
            ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
            ->join('mobileusers', 'mobileusers.id', '=', 'orders.user_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('orders.id as order_id', 'orderitems.quantity as quantity', 'orderitems.status', 'orders.dealer_code', 'orderitems.id as orderitems_id', 'product_size.price as price', 'products.name as product_name', 'sizes.size as size', 'dealers.name as dealer_name', 'orders.date as order_date', 'orders.status as order_status', 'mobileusers.name as user_name', 'categories.name as category_name', 'orders.remarks', 'orderitems.custom_unit_remarks as custom_remarks')
            ->where('orders.id', $id)
            ->get();

        foreach ($order as $n) {
            $explode = explode('::', $n->order_date);
            $implode = implode('', $explode);
            $date = explode('-', $implode);
            $currentYear = $date[0];
            $currentMonth = $date[1];
            $currentDay = $date[2];
            $currentNepaliDay = $this->AD_to_BS($currentYear, $currentMonth, $currentDay);
            $nepaliDay = $currentNepaliDay['date'];
            $nepaliMth = $currentNepaliDay['month'];
            $d = strlen($nepaliMth);
            if ($d == 1) {
                $nepaliMth = '0' . $nepaliMth;
            }
            $nepaliYr = $currentNepaliDay['year'];
            $order_date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
            $dealer = $n->dealer_name;
            $mo = $n->user_name;

            $list[] = array(
                'order_id' => $n->order_id,
                'dealer_name' => $n->dealer_name,
                'order_status' => $n->order_status,
                'quantity' => $n->quantity,
                'price' => $n->price,
                'orderitems_id' => $n->orderitems_id,
                'product_name' => $n->product_name,
                'size' => $n->size,
                'category_name' => $n->category_name,
                'order_date' => $order_date,
                'remarks' => $n->remarks,
                'status' => $n->status,
                'user_name' => $n->user_name,
                'dealer_code' => $n->dealer_code,
                'custom_remarks' => $n->custom_remarks
            );
        }
        if (empty($list)) {
            //    if (count($list)<1) {
            return view('dashboards.orders.list', ['order' => '', 'dealer' => '', 'mo' => '', 'o' => '']);
        } else {
            return view('dashboards.orders.list', ['order' => $list, 'dealer' => $dealer, 'mo' => $mo, 'o' => $o]);
        } //todo
    }

    public function index()
    {
        $list = [];
        $total = 0;
        $pending = 0;
        $ongoing = 0;
        $cancelled = 0;
        $completed = 0;
        $orderamt = 0;
        $list = [];
        $engto = date('Y-m-d');
        $engfrom = date('Y-m-d');
        $from = $this->custom_AD_to_BS($engfrom);
        $to = $this->custom_AD_to_BS(date('Y-m-d'));

        if (Auth::user()->user_type == "Area Service Management") {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $areas = Area::whereIn('areacode', $admin_area)->get();
            $dealers = Dealer::where('active_status', 'Active')
                ->whereNull('main_dealer_code')
                ->whereIn('area_code', $admin_area)
                ->get();
        } else {
            $dealers = Dealer::whereNull('main_dealer_code')->get();
            $areas = Area::get();
            $admin_area = $areas->pluck('areacode');
        }
        $dealercodes = $dealers->pluck('code');

        $stats = Order::whereIn('dealer_code', $dealercodes)
            ->whereIn('areacode', $admin_area)
            ->whereBetween('date', [$engfrom, $engto])
            ->selectRaw("
                        SUM(CASE WHEN DATE(date) = CURDATE() THEN total_price ELSE 0 END) as total,
                        SUM(CASE WHEN status IN ('Sent To Account','Pending','Inventory', 'Account') THEN total_price ELSE 0 END) as pending,
                        SUM(CASE WHEN status IN ('Sent to Dispatch','Approved','Dispatched','Account') THEN total_price ELSE 0 END) as ongoing,
                        SUM(CASE WHEN status = 'Declined' THEN total_price ELSE 0 END) as cancelled,
                        SUM(CASE WHEN status = 'Delivered' THEN total_price ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'Draft' THEN total_price ELSE 0 END) as draft
                    ")
            ->first();

        $total     = $stats->total;
        $pending   = $stats->pending;
        $ongoing   = $stats->ongoing;
        $cancelled = $stats->cancelled;
        $completed = $stats->completed;
        $draft     = $stats->draft;

        $orders = Order::with(['dealer:code,name,discount', 'user:id,name', 'items:id,custom_unit_remarks'])
            ->whereIn('dealer_code', $dealercodes)
            ->whereIn('areacode', $admin_area)
            ->whereBetween('date', [$engfrom, $engto])
            ->groupBy('orders.id')
            ->get();

        foreach ($orders as $order) {
            $order_date = $this->custom_AD_to_BS($order->date);

            $list[] = array(
                'order_id' => $order->id,
                'dealer_name' => $order->dealer->name,
                'order_status' => $order->status,
                'order_date' => $order_date,
                'remarks' => $order->remarks,
                'user_name' => $order->user->name,
                'updated_at' => $order->updated_at,
                'orderamt' => $order->total_price,
                'dealer_code' => $order->dealer_code,
                'due' => $order->due,
                'last_collection_date' => $order->last_collection_date,
                'last_collection' => $order->last_collection,
                'delivery_time' => $order->delivery_time,
                'delivery_cost' => $order->delivery_cost,
                'custom_remarks' => $order->items->first()->custom_unit_remarks ?? null,
                'dealer_discount' => $order->dealer->discount,
                'discount' => $order->discount,
                'orderimages' => OrderImage::where('status', 'Active')->get(),
                'vat' => $order->vat,
                'lat_lng' => $order->lat_lng,
                'location' => $order->location,
            );
            $orderamt = 0;
        }

        $data['dealers'] = $dealers;
        $data['order'] = $list;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['de'] = '';
        $data['start'] = 'index';
        $data['total'] = $total;
        $data['pending'] = $pending;
        $data['ongoing'] = $ongoing;
        $data['cancelled'] = $cancelled;
        $data['completed'] = $completed;
        $data['draft'] = $draft;
        $data['dealercode'] = null;

        $data['areas'] = $areas;
        $data['areacode'] = '';
        return view('dashboards.orders.Allorder', $data);
    }

    public function store(Request $request)
    {
        $total = 0;
        $today = $this->custom_AD_to_BS(date('Y-m-d'));

        $this->validate($request, [
            'dealer' => 'nullable|exists:dealers,code',
            'areacode' => 'nullable|exists:areas,areacode',
            'filter_type' => 'required|in:all,Pending,approved,ongoing,completed,cancelled,draft',
            'end-date' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:start-date'],
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:end-date']
        ]);

        $filterType = strtolower(trim($request->filter_type));

        $statusMap = [
            'pending'       => ['Pending', 'Sent to Account'],
            'approved'      => ['Approved', 'Account'],
            'ongoing'       => ['Sent to Dispatch', 'Dispatched'],
            'cancelled'     => ['Declined'],
            'completed'     => ['Delivered'],
            'urgent order'  => ['Urgent Order'],
            'draft'         => ['Draft'],
        ];

        $status = $statusMap[$filterType] ?? 'all';

        $startDate = $request->input('start-date');
        $endDate   = $request->input('end-date');
        // $today     = $today;

        // if ($startDate->gt($endDate)) {
        if ($startDate > $endDate) {
            return back()
                ->withInput()
                ->withErrors(['end-date' => 'End date must be after start date']);
        }

        // if ($startDate->gt($today)) {
        if ($startDate > $today) {
            return back()
                ->with('status', 'The start date must be before or equal to today');
        }

        $dealer = $request['dealer'];
        $area = $request->areacode;
        $fr = $request['start-date'];
        $t = $request['end-date'];

        if (!empty($request['start-date'] || $request['end-date'])) {
            $from = $this->custom_BS_to_AD($request['start-date']);
            $to = $this->custom_BS_to_AD($request['end-date']);
        }

        $user = Auth::user();
        $isRestricted = !$user->hasRole('Super-Admin')
            && optional($user->roles->first())->hasArea;

        $admin_area = $isRestricted
            ? $user->area->pluck('areacode')
            : Area::pluck('areacode');

        $mobileuser = $isRestricted
            ? Mobileuser::whereHas(
                'areas',
                fn($q) =>
                $q->whereIn('areacode', $admin_area)
            )->pluck('id')
            : Mobileuser::pluck('id');

        $dealers = Dealer::with('area')
            ->whereNull('main_dealer_code')
            ->when(
                $isRestricted,
                fn($q) =>
                $q->whereIn('area_code', $admin_area)
                    ->where('active_status', 'Active')
                    ->whereHas(
                        'area',
                        fn($a) =>
                        $a->where('status', 'Published')
                    )
            )->get();

        $areas = Area::whereIn('areacode', $admin_area)->get();


        $dealercodes = $dealers->pluck('code');

        $baseQuery = Order::query()
            ->whereBetween('date', [$from, $to])
            ->whereIn('dealer_code', $dealercodes);

        if ($dealer) {
            $baseQuery->where('dealer_code', $dealer);
        }

        if ($area) {
            $baseQuery->where('areacode', $area);
        }

        if (!$dealer && !$area) {
            $baseQuery->whereIn('user_id', $mobileuser);
        }

        if (!$dealer && $area) {
            $baseQuery->whereIn('user_id', $mobileuser);
        }

        $baseQuery->when($status !== 'all', function ($query) use ($status) {
            $query->whereIn('status', (array) $status);
        });


        $stats = (clone $baseQuery)
            ->selectRaw("
                    SUM(total_price) as total,
                    SUM(CASE WHEN status IN ('Sent To Account','Pending','Inventory', 'Account') THEN total_price ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Sent to Dispatch' THEN total_price ELSE 0 END) as ongoing,
                    SUM(CASE WHEN status = 'Approved' THEN total_price ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'Dispatched' THEN total_price ELSE 0 END) as dispatched,
                    SUM(CASE WHEN status = 'Declined' THEN total_price ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'Delivered' THEN total_price ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'Draft' THEN total_price ELSE 0 END) as draft
                ")
            ->first();

        $total     = $stats->total ?? 0;
        $pending   = $stats->pending ?? 0;
        $ongoing   = $stats->ongoing ?? 0;
        $approved  = $stats->approved ?? 0;
        $dispatched = $stats->dispatched ?? 0;
        $completed = $stats->completed ?? 0;
        $cancelled = $stats->cancelled ?? 0;
        $draft     = $stats->draft ?? 0;

        $order = $baseQuery->get();

        if (!empty($dealer)) {
            $dea = Dealer::where('code', $dealer)->first();
            $d = $dea->name;
        } else {
            $d = '';
        }

        foreach ($order as $ordr) {
            $ordr->orderimages = OrderImage::where('status', 'Active')->get();
        }
        $data['dealers'] = $dealers;
        $data['from'] = $fr;
        $data['to'] = $t;
        $data['order'] = $order;
        $data['start'] = '';
        $data['total'] = $total;
        $data['dealercode'] = $request->dealer;
        $data['areacode'] = $request->areacode;
        $data['de'] = $d;
        $data['pending'] = $pending;
        $data['completed'] = $completed;
        $data['ongoing'] = $ongoing;
        $data['cancelled'] = $cancelled;
        $data['dispatched'] = $dispatched;
        $data['draft'] =  $draft;
        $data['areas'] = $areas;
        $data['status'] = $request->filter_type;

        return view('dashboards.orders.show', $data);
    }

    public function update($id, Request $request)
    {
        $valid = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Sent to Dispatch,Inventory,Sent To Account,Declined,Approved,Dispatched,Delivered,Account',
            'remark' => 'required|max:191',
            'delivery_time' => 'nullable|required_if:status,==,Account|min:0',
            'delivery_cost' => 'nullable|required_if:status,==,Account|min:0|numeric|between:0,99.99',
            'due' => 'nullable',
            'last_collection_date' => 'nullable|date|before:today',
            'last_collection' => 'nullable'

        ]);

        if ($valid->fails()) {
            return back()->withErrors($valid);
        }

        DB::beginTransaction();
        try {
            $date = date('Y-m-d');
            $orders = Order::with('dealer:code,name,address')->findOrFail($id);
            $sta = Str::lower($orders->status);
            $orders->status = request('status');
            $orders->remarks = request('remark');
            if ($request->status == 'Account') {
                $orders->delivery_time = $request->delivery_time;
                $orders->delivery_cost = $request->delivery_cost;
                $orders->due = $request->due;
                $orders->last_collection_date = $request->last_collection_date;
                $orders->last_collection = $request->last_collection;
                $orders->risk_zone = $request->riskzone;
            }

            $orders->save();
            $status = '';
            if ($orders->status == "Approved" || $orders->status == "Dispatched" || $orders->status == "Sent to Dispatch") {
                $status = 'true';
            }
            if ($orders->status == 'Sent To Account' && $request->status == 'Approved') {
                $status = 'false';
                $mouser = $orders->user_id;
                $areacode = $orders->areacode;
                $dealer_code = $orders->dealer_code;
                $target_month = explode('-', $orders->date);
                $target_month = $target_month[0] . '-' . $target_month[1];

                if ($orders->date >= $target_month . '-01' && $orders->date <= $target_month . '-07') {
                    $week = 'week_1_target';
                } else if ($orders->date >= $target_month . '-08' && $orders->date <= $target_month . '-14') {
                    $week = 'week_2_target';
                } else if ($orders->date >= $target_month . '-15' && $orders->date <= $target_month . '-21') {
                    $week = 'week_3_target';
                } else if ($orders->date >= $target_month . '-22' && $orders->date <= $target_month . '-31') {
                    $week = 'week_4_target';
                } else {
                    $week = 'week_4_target';
                }

                $targetinfo = TargetInfo::with(['usertarget' => function ($query) use ($mouser, $areacode) {
                    $query->where(['mobileuser_id' => $mouser, 'areacode' => $areacode]);
                }, 'dealerwisetarget' => function ($query) use ($mouser, $dealer_code) {
                    $query->where(['mobileuser_id' => $mouser, 'dealer_code' => $dealer_code]);
                }])->where('target_month', $target_month)->first();
                if ($targetinfo) {
                    if (count($targetinfo->usertarget) > 0) {
                        $usertarget = UserTarget::where(['target_name' => $week, 'target_info_id' => $targetinfo->id, 'mobileuser_id' => $orders->user_id, 'areacode' => $orders->areacode])->first();
                        if ($usertarget) {
                            $usertarget->target_achieve = $orders->total_price;

                            $usertarget->save();
                        }
                    }
                    if (count($targetinfo->dealerwisetarget) > 0) {
                        $dealertarget = DealerwiseTarget::where(['target_name' => $week, 'target_info_id' => $targetinfo->id, 'mobileuser_id' => $orders->user_id, 'dealer_code' => $orders->dealer_code])->first();
                        if ($dealertarget) {
                            $dealertarget->target_achieve = $orders->total_price;

                            $dealertarget->save();
                        }
                    }
                }
                $orderitems = Orderitem::join('product_size', 'product_size.id', 'orderitems.items_id')
                    ->join('products', 'products.id', 'product_size.product_id')
                    ->select('orderitems.*', 'products.category_id')
                    ->where('order_id', $orders->id)
                    ->get();

                foreach ($orderitems as $orderitem) {
                    $producttarget = ProductwiseTarget::where(['target_name' => $week, 'target_info_id' => $targetinfo->id, 'mobileuser_id' => $orders->user_id, 'category_id' => $orderitem->category_id])->first();
                    if ($producttarget) {
                        $producttarget->target_achieve = $producttarget->target_achieve + $orderitem->total_price;

                        $producttarget->save();
                    }
                }
            }
            //to change if need correction
            if ($request->status == 'Declined') {
                $order = Order::findorFail($id);
                $order->is_need_correction = $request->needcorrection;
                if ($request->needcorrection == "1") {
                    $order->status = "Draft";
                }
                $order->save();
            }
            if ($status == 'true' && $request->status == 'Declined') {
                $mouser = $orders->user_id;
                $areacode = $orders->areacode;
                $dealer_code = $orders->dealer_code;
                $target_month = explode('-', $orders->date);
                $target_month = $target_month[0] . '-' . $target_month[1];




                if ($orders->date >= $target_month . '-01' && $orders->date <= $target_month . '-07') {
                    $week = 'week_1_target';
                } else if ($orders->date >= $target_month . '-08' && $orders->date <= $target_month . '-14') {
                    $week = 'week_2_target';
                } else if ($orders->date >= $target_month . '-15' && $orders->date <= $target_month . '-21') {
                    $week = 'week_3_target';
                } else if ($orders->date >= $target_month . '-22' && $orders->date <= $target_month . '-31') {
                    $week = 'week_4_target';
                } else {
                    $week = 'week_4_target';
                }

                $targetinfo = TargetInfo::with(['usertarget' => function ($query) use ($mouser, $areacode) {
                    $query->where(['mobileuser_id' => $mouser, 'areacode' => $areacode]);
                }, 'dealerwisetarget' => function ($query) use ($mouser, $dealer_code) {
                    $query->where(['mobileuser_id' => $mouser, 'dealer_code' => $dealer_code]);
                }])->where('target_month', $target_month)->first();
                if ($targetinfo) {
                    if (count($targetinfo->usertarget) > 0) {
                        $usertarget = UserTarget::where(['target_name' => $week, 'target_info_id' => $targetinfo->id, 'mobileuser_id' => $orders->user_id, 'areacode' => $orders->areacode])->first();
                        if ($usertarget) {
                            $usertarget->target_achieve = $usertarget->target_achieve - $orders->total_price;

                            $usertarget->save();
                        }
                    }
                    if (count($targetinfo->dealerwisetarget) > 0) {
                        $dealertarget = DealerwiseTarget::where(['target_name' => $week, 'target_info_id' => $targetinfo->id, 'mobileuser_id' => $orders->user_id, 'dealer_code' => $orders->dealer_code])->first();
                        if ($dealertarget) {
                            $dealertarget->target_achieve = $dealertarget->target_achieve - $orders->total_price;

                            $dealertarget->save();
                        }
                    }
                }
                $orderitems = Orderitem::join('product_size', 'product_size.id', 'orderitems.items_id')
                    ->join('products', 'products.id', 'product_size.product_id')
                    ->select('orderitems.*', 'products.category_id')
                    ->where('order_id', $orders->id)
                    ->get();

                foreach ($orderitems as $orderitem) {
                    $producttarget = ProductwiseTarget::where(['target_name' => $week, 'target_info_id' => $targetinfo->id, 'mobileuser_id' => $orders->user_id, 'category_id' => $orderitem->category_id])->first();
                    if ($producttarget) {
                        $producttarget->target_achieve = $producttarget->target_achieve - $orderitem->total_price;

                        $producttarget->save();
                    }
                }
            }

            $user = Mobileuser::find($orders->user_id);

            if ($user && $user->fcm_id) {
                $title = "Dealer Order Status Updated";
                $body  = "{$orders->dealer->name} order status has been updated to {$request->status}";

                $data = [
                    'route_channel' => 'order?order_id=' . $orders->id . '&dealer=' . $orders->dealer,
                ];

                $this->runFirebaseNotification($user->fcm_id, null, $title, $body, $data);
            }

            $dealers = Dealer::get();

            $order = DB::table('orders')
                ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
                ->join('mobileusers', 'mobileusers.id', '=', 'orders.user_id')
                ->select('orders.id as order_id', 'dealers.name as dealer_name', 'orders.dealer_code', 'orders.date as order_date', 'orders.status as order_status', 'mobileusers.name as user_name', 'orders.remarks')
                ->where('orders.status', '=', 'pending')
                ->groupBy('orders.id')
                ->get();

            // Order Log details
            $log = new LogDetail();
            $log->flag = 'order';
            $log->ordercoll_id = $orders->id;
            $log->cheque_no = $orders->cheque_num;
            $log->date = date('Y-m-d');
            $log->admin = Auth::user()->id;
            $log->user_id = $orders->user_id;
            $log->status = request('status');
            $log->remarks = request('remark');
            $log->previous_status = request('previous_status');

            $log->save();


            foreach ($order as $n) {
                $order_date = $this->custom_AD_to_BS($n->order_date);
                $list[] = array(
                    'order_id' => $n->order_id,
                    'dealer_name' => $n->dealer_name,
                    'order_status' => $n->order_status,
                    'order_date' => $order_date,
                    'remarks' => $n->remarks,
                    'user_name' => $n->user_name,
                    'dealer_code' => $n->dealer_code
                );
            }
            DB::commit();
            return back()->with('Status', 'Order has been updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while updating the order status.' . $e->getMessage());
        }
    }

    public function status($oldstatus, Request $request)
    {
        $total = 0;
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        if (is_null($request['filter_type'])) {
            $request['filter_type'] = $oldstatus;
        }
        if (is_null($request['start-date'])) {
            $request['start-date'] = $today;
        }
        if (is_null($request['end-date'])) {
            $request['end-date'] = $today;
        }

        $this->validate($request, [
            'dealer' => 'nullable|exists:dealers,code',
            'areacode' => 'nullable|exists:areas,areacode',
            'filter_type' => 'required|in:all,Pending,approved,ongoing,completed,cancelled,draft',
            'end-date' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:start-date'], //'nullable|date_format:Y-m-d|after_or_equal:start-date',
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:end-date'] //'nullable|required_with_all:end-date|date|date_format:Y-m-d',
        ]);

        $filterType = strtolower(trim($request->filter_type));

        $statusMap = [
            'pending'       => ['Pending', 'Sent to Account'],
            'approved'      => ['Approved', 'Account'],
            'ongoing'       => ['Sent to Dispatch', 'Dispatched'],
            'cancelled'     => ['Declined'],
            'completed'     => ['Delivered'],
            'urgent order'  => ['Urgent Order'],
            'draft'         => ['Draft'],
        ];

        $status = $statusMap[$filterType] ?? 'all';

        $startDate = $request->input('start-date');
        $endDate   = $request->input('end-date');
        // $today     = $today;

        // if ($startDate->gt($endDate)) {
        if ($startDate > $endDate) {
            return back()
                ->withInput()
                ->withErrors(['end-date' => 'End date must be after start date']);
        }

        // if ($startDate->gt($today)) {
        if ($startDate > $today) {
            return back()
                ->with('status', 'The start date must be before or equal to today');
        }

        $dealer = $request['dealer'];
        $area = $request->areacode;
        $fr = $request['start-date'];
        $t = $request['end-date'];
        $orderamt = 0;

        if (!empty($fr || $t)) {
            $from = $this->custom_BS_to_AD($fr);
            $to = $this->custom_BS_to_AD($t);
        }

        $user = Auth::user();
        $isRestricted = !$user->hasRole('Super-Admin')
            && optional($user->roles->first())->hasArea;

        $admin_area = $isRestricted
            ? $user->area->pluck('areacode')
            : Area::pluck('areacode');

        $mobileuser = $isRestricted
            ? Mobileuser::whereHas(
                'areas',
                fn($q) =>
                $q->whereIn('areacode', $admin_area)
            )->pluck('id')
            : Mobileuser::pluck('id');

        $dealers = Dealer::with('area')
            ->whereNull('main_dealer_code')
            ->when(
                $isRestricted,
                fn($q) =>
                $q->whereIn('area_code', $admin_area)
                    ->where('active_status', 'Active')
                    ->whereHas(
                        'area',
                        fn($a) =>
                        $a->where('status', 'Published')
                    )
            )->get();

        $areas = Area::whereIn('areacode', $admin_area)->get();


        $dealercodes = $dealers->pluck('code');

        $baseQuery = Order::query()
            ->whereBetween('date', [$from, $to])
            ->whereIn('dealer_code', $dealercodes);

        if ($dealer) {
            $baseQuery->where('dealer_code', $dealer);
        }

        if ($area) {
            $baseQuery->where('areacode', $area);
        }

        if (!$dealer && !$area) {
            $baseQuery->whereIn('user_id', $mobileuser);
        }

        if (!$dealer && $area) {
            $baseQuery->whereIn('user_id', $mobileuser);
        }

        $baseQuery->when($status !== 'all', function ($query) use ($status) {
            $query->whereIn('status', (array) $status);
        });


        $stats = (clone $baseQuery)
            ->selectRaw("
                    SUM(total_price) as total,
                    SUM(CASE WHEN status IN ('Sent To Account','Pending','Inventory', 'Account') THEN total_price ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Sent to Dispatch' THEN total_price ELSE 0 END) as ongoing,
                    SUM(CASE WHEN status = 'Approved' THEN total_price ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'Dispatched' THEN total_price ELSE 0 END) as dispatched,
                    SUM(CASE WHEN status = 'Declined' THEN total_price ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'Delivered' THEN total_price ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'Draft' THEN total_price ELSE 0 END) as draft
                ")
            ->first();

        $total     = $stats->total ?? 0;
        $pending   = $stats->pending ?? 0;
        $ongoing   = $stats->ongoing ?? 0;
        $approved  = $stats->approved ?? 0;
        $dispatched = $stats->dispatched ?? 0;
        $completed = $stats->completed ?? 0;
        $cancelled = $stats->cancelled ?? 0;
        $draft     = $stats->draft ?? 0;

        $order = $baseQuery->get();

        if (!empty($dealer)) {
            $dea = Dealer::where('code', $dealer)->first();
            $d = $dea->name;
        } else {
            $d = '';
        }
        foreach ($order as $ordr) {
            $ordr->orderimages = OrderImage::where('status', 'Active')->get();
        }
        $data['dealers'] = $dealers;
        $data['from'] = $fr;
        $data['to'] = $t;
        $data['order'] = $order;
        $data['start'] = '';
        $data['total'] = $total;
        $data['dealercode'] = $request->dealer;
        $data['areacode'] = $request->areacode;
        $data['de'] = $d;
        $data['pending'] = $pending;
        $data['completed'] = $approved;
        $data['ongoing'] = $ongoing;
        $data['cancelled'] = $cancelled;
        $data['dispatched'] = $dispatched;
        $data['draft'] =  $draft;
        $data['areas'] = $areas;
        $data['status'] = $request->filter_type;

        return view('dashboards.orders.show', $data);
    }

    public function pop($id)
    {
        $log = LogDetail::with('adminUser')->where('ordercoll_id', $id)
            ->whereNot('admin', 0)
            ->where('flag', 'order')->get();

        foreach ($log as $n) {
            $date = $this->custom_AD_to_BS($n->date);


            $list[] = array(
                'ordercoll_id' => $n->ordercoll_id,
                'status' => $n->status,
                'previous_status' => $n->previous_status,
                'admin' => $n->adminUser->name,
                'remarks' => $n->remarks,
                'date' => $date,
            );
        }
        if (!empty($list)) {
            return view('dashboards.orders.log', ['log' => $list]);
        } else {
            return view('dashboards.orders.log');
        }
    }

    public function allOrder($id)
    {
        $o = Order::findorFail($id);
        dd($o);
        $user = $o->user_id;
        $dealer = $o->dealer_code;

        $order = DB::table('orders')
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->join('product_size', 'product_size.id', '=', 'orderitems.items_id')
            ->join('products', 'products.id', '=', 'product_size.product_id')
            ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
            ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
            ->join('mobileusers', 'mobileusers.id', '=', 'orders.user_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('orders.id as order_id', 'orderitems.quantity as quantity', 'orders.dealer_code', 'orderitems.status', 'product_size.price as price', 'orderitems.id as sizeid', 'products.name as product_name', 'sizes.size as size', 'dealers.name as dealer_name', 'orders.date as order_date', 'orders.status as order_status', 'mobileusers.name as user_name', 'categories.name as category_name', 'orders.remarks')
            ->where('orders.dealer_code', $dealer)
            ->where('orders.user_id', $user)
            ->where('orders.status', $o->status)
            //    ->where('orderitems.status','0')
            ->get();
        //    return $order;

        foreach ($order as $n) {
            $order_date = $this->custom_AD_to_BS($n->order_date);
            $dName = $n->dealer_name;
            $mo = $n->user_name;

            $list[] = array(
                'order_id' => $n->order_id,
                'dealer_name' => $n->dealer_name,
                'order_status' => $n->order_status,
                'quantity' => $n->quantity,
                'price' => $n->price,
                'product_name' => $n->product_name,
                'size' => $n->size,
                'category_name' => $n->category_name,
                'order_date' => $order_date,
                'remarks' => $n->remarks,
                'user_name' => $n->user_name,
                'sizeid' => $n->sizeid,
                'status' => $n->status,
                'dealer_code' => $n->dealer_code,
            );
        }
        //    return $list;
        if (empty($list)) {
            return view('dashboards.orders.allOrderList', ['order' => '', 'dealer' => '', 'mo' => '', 'o' => '']);
        } else {
            return view('dashboards.orders.allOrderList', ['order' => $list, 'dealer' => $dName, 'mo' => $mo, 'o' => $o]);
        }
    }

    function editOrderView($id)
    {
        $products = DB::table('products')
            ->join('product_size', 'products.id', 'product_size.product_id')
            ->distinct('product_size.product_id')
            ->select('products.id', 'products.name')
            ->get();
        // return $products;
        $orderItems = Orderitem::findorFail($id);
        $item_id = $orderItems->items_id;
        $size = ProductSize::where('id', $item_id)->first();
        $product_size = ProductSize::where('product_id', $size->product_id)->get();
        $variants = Size::all();
        return view('dashboards.orders.orderEdit', ['products' => $products, 'orderItems' => $orderItems, 'variants' => $variants, 'size' => $size]);
    }

    public function orderdetails($id)
    {
        $data['odr'] = Order::findorfail($id);
        return view('dashboards.orders.orderdetails', $data);
    }

    public function viewcomplaint($id)
    {
        $complaint = complaint::find($id);
        // return dd($complaint->id);
        return view('dashboards.complaints.view2')->with('complaint', $complaint);
    }

    public function addOrderstore(Request $request)
    {
        $totalorder = 0;
        $product = Product::findOrFail($request->subproduct);
        $productSizes = ProductSize::where('product_id', $product->id)->orderBy('id', 'desc')->get();
        $quantities = $request->quantity;

        $order = $request->order_id ? Order::findOrFail($request->order_id) : new Order;

        $dealer = Dealer::findOrFail($request->dealer);
        $order->areacode = $dealer->area_code;
        $order->dealer_code = $request->dealer;
        $order->user_id = $request->mobileuser;
        $order->date = date('Y-m-d');
        $order->status = 'Pending';
        $order->total_price += $totalorder;
        $order->save();

        $orderItem = [];
        foreach ($productSizes as $key => $value) {
            $quantity = $quantities[$key] ?? 0;


            if ($quantity > 0) {
                $orderItem[] = [
                    "order_id" => $order->id,
                    "items_id" => $value->id,
                    "quantity" => $quantity,
                    "product_rate" => $value->price,
                    "total_price" => $quantity * $value->price,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ];
                $totalorder += $quantity * $value->price;
            }
        }

        if (!empty($orderItem)) {
            Orderitem::insert($orderItem);
        }

        $order->payment_commitment_date = $request->payment_commitment_date ?: null;
        $order->committed_amount = $request->committed_amount ?: 0;
        $order->total_price += $totalorder;
        $order->save();

        return back()->with("Success", "Order placed successfully");
    }

    public function getOrderLocationDetails(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:order,payment',
        ]);

        $model = $request->type == 'order' ? Order::with([
            'user:id,name',
            'dealer:code,name,latitude,longitude',
        ])
            ->select(['id', 'user_id', 'dealer_code', 'lat_lng', 'created_at'])
            ->findOrFail($id) : Payment::with([
                'user:id,name',
                'dealer:code,name,latitude,longitude',
            ])
            ->select(['id', 'user_id', 'dealer_code', 'lat_lng', 'created_at'])
            ->findOrFail($id);

        $array = explode(',', $model->lat_lng);
        $data['lat'] = $array[0];
        $data['lng'] = $array[1];
        $data['model'] = $model;
        $data['type'] = $request->type;

        if ($array[0] == '' || $array[1] == '') {
            return back()->with('error', 'Location data not available for this ' . $request->type);
        }
        return view('dashboards.orders.map', $data);
    }
}

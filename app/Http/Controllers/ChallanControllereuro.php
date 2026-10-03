<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use App\Models\Challan;
use App\Models\Dealer;
use App\Models\Mobileuser;
use App\Models\Orderitem;
use Illuminate\Support\Facades\Auth;
use App\Models\LogDetail;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Validator;

class ChallanController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $valid = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id'
        ]);
        if ($valid->fails()) {
            return back()->withErrors($valid);
        }
        $id = $request->order_id;
        $odr = Order::findorFail($id);
        $order = Orderitem::where('order_id', $id)->where('status', '0')->get();
        if (count($order) == 0) {
            return redirect('/order')->with('Status', 'Product unavailable for this Order.');
        } else {
            foreach ($order as $or) {
                $challan = Challan::where(['order_id'=> $or->order_id, 'items_id' => $or->items_id])->first();
                if (!$challan) {
                    // $product = ProductSize::where('id',$or->items_id)->first();
                    $challan = new Challan();
                    $challan->order_id = $or->order_id;
                    $challan->items_id = $or->items_id;
                    $challan->quantity = $or->quantity;
                    $challan->product_rate = $or->product_rate;
                    $challan->total_price = $or->total_price;
                    $challan->group_id = $id;

                    $challan->remarks = $or->custom_unit_remarks;

                    $challan->save();
                }
            }
            // $log = new LogDetail();
            // $log->flag = 'order';
            // $log->ordercoll_id = $odr->id;
            // if ($odr->tint_user == null) {
            //     $log->user_id = $odr->user_id;
            // } else {
            //     $log->tint_user = $odr->tint_user;
            // }
            // // $log->cheque_no = $odr->cheque_num;
            // $log->date = date('Y-m-d');
            // $log->admin = Auth::user()->name;
            // // $log->user_id = $odr->user_id;
            // $log->status = 'Sent to Dispatch';
            // $log->remarks = '';
            // $log->previous_status = $odr->status;
            // $log->save();

            // $odr->status = 'Sent to Dispatch';
            // $odr->save();
            // $otp = Mobileuser::where(['id' => $odr->user_id])->first();
            // if ($otp) {
            //     $fcmid = $otp->fcm_id;
            //     $flag = "order";
            //     $title = "Sales Tracker";
            //     $detail = $odr->id;
            //     $app = 0;
            //     $content = array('Title' => 'Order Status Updated', 'Message' => 'Challan Created', 'order_id' => $detail, 'Status' => 'ongoing');
            //     if ($fcmid) {
            //         $notice = new NotificationController();
            //         $notice->individualNotificationSend($fcmid, $title, $flag, $content, $app);
            //     }
            // }
            // $otp1 = DealerUser::where(['id' => $odr->tint_user])->first();
            // if ($otp1) {
            //     $fcmid = $otp1->fcm_id;
            //     $flag = "order";
            //     $title = "Sales Tracker";
            //     $detail = $odr->id;
            //     $app = 1;
            //     $content = array('Title' => 'Order Status Updated', 'Message' => 'Challan Created', 'order_id' => $detail, 'Status' => 'ongoing');
            //     if ($fcmid) {
            //         $notice = new NotificationController();
            //         $notice->individualNotificationSend($fcmid, $title, $flag, $content, $app);
            //     }
            // }
            return redirect("/challan/$odr->id");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        public function show($id)
    {
        $mo = "";
        $o = Order::findorFail($id);

        $suser = $o->user_id;
        if ($suser) {
            $mobileuser = Mobileuser::where('id', $suser)->first();
            $mo = $mobileuser->name;
        }
        $dealer = Dealer::where('code', $o->dealer_code)->first();
        // $dName = $dealer->name;

        $tuser = $o->tint_user;

        // $order = DB::table('orders')
        //     ->join('challans', 'challans.order_id', '=', 'orders.id')
        //     ->join('product_size', 'product_size.id', '=', 'challans.items_id')
        //     ->join('products', 'products.id', '=', 'product_size.product_id')
        //     ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
        //     ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
        //     ->join('mobileusers', 'mobileusers.id', '=', 'orders.user_id')
        //     ->join('categories', 'categories.id', '=', 'products.category_id')
        //     ->select('orders.id as order_id', 'challans.quantity as quantity', 'product_size.price as price', 'challans.id as sizeid', 'products.name as product_name', 'sizes.size as size', 'dealers.name as dealer_name', 'orders.date as order_date', 'orders.status as order_status', 'mobileusers.name as user_name', 'categories.name as category_name', 'orders.remarks')
        //     ->where('orders.dealer_code', $dealer)
        //     ->where('orders.user_id', $suser)
        //     ->where('challans.group_id', $id)
        //     ->get();
        // $order = Challan::where(['group_id' => $id])->get();
        $order = Orderitem::where(['order_id' => $id])
            ->join('product_size', 'product_size.id', '=', 'orderitems.items_id')
            ->select('orderitems.*', DB::raw('count(*) as total_row'), 'product_size.*')
            ->orderBy('product_size.product_id')
            ->groupBy('product_size.product_id')
            ->get();
        foreach ($order as $ordr) {
            $kg40 = 0;
            $kg20 = 0;
            $kg10 = 0;
            $kg5 = 0;
            $kg1 = 0;

            $rskg40 = 0;
            $rskg20 = 0;
            $rskg10 = 0;
            $rskg5 = 0;
            $rskg1 = 0;

            $ml500 = 0;
            $ml200 = 0;
            $ml100 = 0;
            $ml50 = 0;

            $rsml500 = 0;
            $rsml200 = 0;
            $rsml100 = 0;
            $rsml50 = 0;
            // $ordr->singleProducts = Orderitem::where(['order_id' => $id])
            //     ->join('product_size', 'product_size.id', '=', 'orderitems.items_id')
            //     ->whereHas('items', function ($query) use ($ordr) {
            //         $query->where('product_id', $ordr->items->product_id);
            //     })
            //     ->groupBy('product_size.product_id')
            //     ->get();
            $singleProducts = Orderitem::where(['order_id' => $id])
                ->join('product_size', 'product_size.id', '=', 'orderitems.items_id')
                ->whereHas('items', function ($query) use ($ordr) {
                    $query->where('product_id', $ordr->items->product_id);
                })->select('orderitems.*', DB::raw('count(*) as total_row'), 'product_size.*')
                ->orderBy('product_size.product_id')
                ->groupBy('product_size.product_id', 'product_size.size_id', 'orderitems.items_id')
                ->get();
            foreach ($singleProducts as $singleProduct) {
                if ($singleProduct->items->size->size == '40 KG') {
                    $ordr->kg40 = $kg40 + ($singleProduct->quantity);
                    $ordr->rskg40 = $rskg40 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }
                if (($singleProduct->items->size->size == '20 KG') || ($singleProduct->items->size->size == '20 LTR')) {
                    $ordr->kg20 = $kg20 + ($singleProduct->quantity);
                    $ordr->rskg20 = $rskg20 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if (($singleProduct->items->size->size == '10 KG') || ($singleProduct->items->size->size == '10 LTR')) {
                    $ordr->kg10 = $kg10 + ($singleProduct->quantity);
                    $ordr->rskg10 = $rskg10 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if (($singleProduct->items->size->size == '5 KG') || ($singleProduct->items->size->size == '4 LTR')) {
                    $ordr->kg5 = $kg5 + ($singleProduct->quantity);
                    $ordr->rskg5 = $rskg5 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if (($singleProduct->items->size->size == '1 KG') || ($singleProduct->items->size->size == '1 LTR') || ($singleProduct->items->size->size == '1 LTR')) {
                    $ordr->kg1 = $kg1 + ($singleProduct->quantity);
                    $ordr->rskg1 = $rskg1 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if ($singleProduct->items->size->size == '500 ML') {
                    $ordr->ml500 = $ml500 + ($singleProduct->quantity);
                    $ordr->rsml500 = $rsml500 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if ($singleProduct->items->size->size == '200 ML') {
                    $ordr->ml200 = $ml200 + ($singleProduct->quantity);
                    $ordr->rsml200 = $rsml200 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if ($singleProduct->items->size->size == '100 ML') {
                    $ordr->ml100 = $ml100 + ($singleProduct->quantity);
                    $ordr->rsml100 = $rsml100 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }

                if ($singleProduct->items->size->size == '50 ML') {
                    $ordr->ml50 = $ml50 + ($singleProduct->quantity);
                    $ordr->rsml50 = $rsml50 + ($singleProduct->items->price * $singleProduct->quantity * $singleProduct->total_row);
                }
            }

            if (!isset($ordr->kg40)) {
                $ordr->kg40 = 0;
                $ordr->rskg40 = 0;
            }
            if (!isset($ordr->kg20)) {
                $ordr->kg20 = 0;
                $ordr->rskg20 = 0;
            }

            if (!isset($ordr->kg10)) {
                $ordr->kg10 = 0;
                $ordr->rskg10 = 0;
            }

            if (!isset($ordr->kg5)) {
                $ordr->kg5 = 0;
                $ordr->rskg5 = 0;
            }

            if (!isset($ordr->kg1)) {
                $ordr->kg1 = 0;
                $ordr->rskg1 = 0;
            }

            if (!isset($ordr->ml500)) {
                $ordr->ml500 = 0;
                $ordr->rsml500 = 0;
            }

            if (!isset($ordr->ml200)) {
                $ordr->ml200 = 0;
                $ordr->rsml200 = 0;
            }

            if (!isset($ordr->ml100)) {
                $ordr->ml100 = 0;
                $ordr->rsml100 = 0;
            }

            if (!isset($ordr->ml50)) {
                $ordr->ml50 = 0;
                $ordr->rsml50 = 0;
            }
            // dd($singleProducts);
        }
        // dd($order[0]);
        // if ($tuser) {
        //     $order = DB::table('orders')
        //         ->join('challans', 'challans.order_id', '=', 'orders.id')
        //         ->join('product_size', 'product_size.id', '=', 'challans.items_id')
        //         ->join('products', 'products.id', '=', 'product_size.product_id')
        //         ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
        //         ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
        //         ->join('dealer_user_info', 'dealer_user_info.id', '=', 'orders.tint_user')
        //         ->join('categories', 'categories.id', '=', 'products.category_id')
        //         ->select('orders.id as order_id', 'challans.quantity as quantity', 'product_size.price as price', 'challans.id as sizeid', 'products.name as product_name', 'sizes.size as size', 'dealers.name as dealer_name', 'orders.date as order_date', 'orders.status as order_status', 'dealer_user_info.full_name as user_name', 'categories.name as category_name', 'orders.remarks')
        //         ->where('orders.dealer_code', $dealer)
        //         ->where('orders.tint_user', $tuser)
        //         ->where('challans.group_id', $id)
        //         ->get();
        // }
        //    $order = array_merge($sorder->toArray(),$torder->toArray());
        // return $order;

        // foreach ($order as $n) {
        //     $explode = explode('::', $n->order_date);
        //     $implode = implode('', $explode);
        //     $date = explode('-', $implode);
        //     $currentYear = $date[0];
        //     $currentMonth = $date[1];
        //     $currentDay = $date[2];
        //     $currentNepaliDay = $this->AD_to_BS($currentYear, $currentMonth, $currentDay);
        //     $nepaliDay = $currentNepaliDay['date'];
        //     $nepaliMth = $currentNepaliDay['month'];
        //     $d = strlen($nepaliMth);
        //     if ($d == 1) {
        //         $nepaliMth = '0' . $nepaliMth;
        //     }
        //     $nepaliYr = $currentNepaliDay['year'];
        //     $order_date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
        //     $dName = $n->dealer_name;
        //     $mo = $n->user_name;
        //     $orderid = $n->order_id;
        //     $list[] = array(
        //         'order_id' => $n->order_id,
        //         'dealer_name' => $n->dealer_name,
        //         'order_status' => $n->order_status,
        //         'quantity' => $n->quantity,
        //         'price' => $n->price,
        //         'product_name' => $n->product_name,
        //         'size' => $n->size,
        //         'category_name' => $n->category_name,
        //         'order_date' => $order_date,
        //         'remarks' => $n->remarks,
        //         'user_name' => $n->user_name,
        //         'sizeid' => $n->sizeid,

        //     );
        // }
        $data['order'] = $order;
        $data['dealer'] = $dealer;
        $data['mo'] = $mo;
        return view('dashboards.orders.new_challan', $data);
        // if (empty($list)) {
        //     return view('dashboards.orders.challan', ['order' => '', 'dealer' => '', 'mo' => '']);
        // } else {
        //     return view('dashboards.orders.challan', ['order' => $list, 'dealer' => $dName, 'mo' => $mo]);
        // }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

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
}

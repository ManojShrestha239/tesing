<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Mobileuser;
use App\Models\Payment;
use App\Models\Dealer;
use App\Models\LogDetail;
use Carbon\Carbon;
use App\Models\Denominationpoint;
use Illuminate\Http\Request;
use App\Traits\FCM;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TokenController extends Controller
{
    use CustomNepaliCalendarConverter, ValidatesRequests, FCM;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $engto = date('Y-m-d');
        $engfrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $from = $this->custom_AD_to_BS($engfrom);
        $to = $this->custom_AD_to_BS(date('Y-m-d'));

        if (Auth::user()->user_type == "Area Service Management") {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            $users = Mobileuser::whereIn('id', $mobileuser)->get();
        } else {
            $mobileuser = Mobileuser::pluck('id');
            $users = Mobileuser::get();
        }

        $totalcoll = Payment::where('payments.payment_type', '=', "token")
            ->whereIn('user_id', $mobileuser)
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->sum('amount');
        $payment = DB::table('payments')
            ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->select('mobileusers.name as user', 'payments.is_edit', 'payments.location', 'payments.lat_lng', 'dealers.name as dealer', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.TDS', 'payments.discount', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status')
            ->where('payments.payment_type', '=', 'token')
            ->whereIn('user_id', $mobileuser)
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->orderBy('payments.created_at', 'DESC')
            ->get();

        foreach ($payment as $n) {
            $n->previous_amt = LogDetail::where(['ordercoll_id' => $n->id, 'flag' => "collection"])->latest()->first();
            $n->collection_date = $this->custom_AD_to_BS($n->collection_date);
        }
        $data['payment'] = $payment;
        $data['users'] = $users;
        $data['mr'] = "";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['total'] = $totalcoll;
        $data['status'] = "token";
        return view('dashboards.tokens.view', $data);
        // return view('dashboards.tokens.view', ['payment' => $payment, 'users' => $users, 'mr' => '', 'from' => '', 'to' => '', 'total' => $totalcoll]);
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
        // dd('hi');
        $totalcoll = 0;
        $discount = 0;
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        // return $today;
        $this->validate($request, [
            'end-date' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:start-date'], //'required|date_format:Y-m-d|after_or_equal:start-date',
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:end-date'], //'nullable|required_with_all:end-date|date|date_format:Y-m-d',
            'user' => 'nullable|integer'
        ]);
        if ($request['start-date'] > $request['end-date']) {
            return back()->withInput($request->input())->withErrors(['end-date' => 'end date must be after or equal with start date']);
        }
        // if ($request['start-date'] > $today) {

        //     return redirect('dashboard/collection')->with('status', 'The start-date must be a date before or equal to today');
        // }

        if (Auth::user()->user_type == "Area Service Management") {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            $users = Mobileuser::whereIn('id', $mobileuser)->get();
        } else {
            $mobileuser = Mobileuser::pluck('id');
            $users = Mobileuser::get();
        }

        $from = $this->custom_BS_to_AD($request['start-date']);
        $to = $this->custom_BS_to_AD($request['end-date']);

        $date = date('Y-m-d');
        $user = $request['user'];
        $t = $request['end-date'];
        $fr = $request['start-date'];




        if ($user) {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.location', 'payments.lat_lng', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status')
                ->where('payments.user_id', '=', $user)
                ->where('payments.payment_type', '=', 'token')
                ->whereBetween('payments.collection_date', [$from, $to])
                ->orderBy('payments.created_at', 'DESC')
                ->get();
            $totalcoll = Payment::where('payments.user_id', '=', $user)->where('payments.payment_type', '=', 'token')->whereBetween('payments.collection_date', [$from, $to])->sum('amount');
        } else {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.location', 'payments.lat_lng', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status')
                ->where('payments.payment_type', '=', 'token')
                ->whereIn('payments.user_id', $mobileuser)
                ->whereBetween('payments.collection_date', [$from, $to])
                ->orderBy('payments.created_at', 'DESC')
                ->get();
            $totalcoll = Payment::whereIn('payments.user_id', $mobileuser)->where('payments.payment_type', '=', 'token')->whereBetween('payments.collection_date', [$from, $to])->sum('amount');
        }

        foreach ($payment as $n) {
            $n->previous_amt = LogDetail::where(['ordercoll_id' => $n->id, 'flag' => "collection"])->latest()->first();
            $n->collection_date = $this->custom_AD_to_BS($n->collection_date);
        }
        if (!empty($user)) {
            $mrk = Mobileuser::where('id', $user)->first();
            $mr = $mrk->name;
        } else {
            $mr = '';
        }
        $data['payment'] = $payment;
        $data['users'] = $users;
        $data['mr'] = $mr;
        $data['from'] = $fr;
        $data['to'] = $t;
        $data['total'] = $totalcoll;
        $data['status'] = "token";
        $data['discount'] = $discount;
        $data['userid'] = $request->user;
        $data['status'] = 'token';

        return view('dashboards.tokens.view', $data);

        // return view('dashboards.tokens.view', ['payment' => $payment, 'users' => $users, 'mr' => $mr, 'from' => $fr, 'to' => $t, 'start' => '', 'total' => $totalcoll, 'discount' => $discount, 'userid' => $request->user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $log = LogDetail::where('ordercoll_id', $id)->where('flag', 'collection')->get();

        foreach ($log as $n) {
            $date = $this->custom_AD_to_BS($n->date);

            $n->date = $date;
        }
        return view('dashboards.tokens.log', ['log' => $log]);
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
        // $r = request('pre_status');
        // return response()->JSON($r);
        // die();
        //
        $users = Mobileuser::where('role_id', 1);
        $payment = Payment::findorFail($id);

        $payment->status = $request->status;
        $payment->remarks = $request->remark;

        $payment->save();

        $date = date('Y-m-d');
        $log = new LogDetail();
        $log->flag = 'collection';
        $log->ordercoll_id = $payment->id;
        $log->cheque_no = $payment->cheque_num;
        $log->date = $date;
        $log->admin = Auth::user()->name;
        $log->user_id = $payment->user_id;
        $log->status = $request->status;
        $log->remarks = $request->remark;
        $log->previous_status = request('pre_status');

        $log->save();

        $dealer = Dealer::where('code', $payment->dealer_code)->first();
        $del = explode('::', $dealer->name);
        $dealer_name = implode('', $del);

        if (!empty($otp = Mobileuser::where(['id' => $payment->user_id])->first())) {
            $fcmid = $otp->fcm_id;
            $title = "Sales Tracker";
            $flag = 'collection';
            $detail = $payment->id;
            $status = $payment->status;
            $mess = request('remark');
            if (empty($mess)) {
                $mess = 'Collection Status Updated';
            }
            $content = array('Title' => 'Collection Status Updated', 'Message' => $mess, 'payment_id' => $payment->id, 'dealer_name' => $dealer_name, 'dealer_code' => $dealer->code, 'dealer_address' => $dealer->address, 'Status' => request('status'));
            // if (!empty($fcmid)) {
            //     $notice = new NotificationController();
            //     $notice->individualNotificationSend($fcmid, $title, $flag, $content);
            // }

            if (!empty($fcmid)) {
                $title = "Collection Status Updated";
                $body  = "{$payment->dealer->name} collection status has been updated to {$payment->status}";

                $data = [
                    'route_channel' => 'collection?collection_id=' . $payment->id . '&dealer=' . $payment->dealer,
                ];

                $this->runFirebaseNotification($fcmid, null, $title, $body, $data);
            }
        }

        // $payment = Payment::where('payment_type', '=', 'cheque')->orWhere('payment_type','=','cheque (bearer)')->get();


        return redirect('token')->with('Status', 'Topken Status Changed.');
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
    public function details(Request $request, $id)
    {

        $payment = Denominationpoint::where('collection_id', $id)->get();

        return view('dashboards.tokens.list', ["payments" => $payment]);
    }
}

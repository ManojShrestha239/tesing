<?php

namespace App\Http\Controllers;

use App\Models\Mobileuser;
use App\Models\Payment;
use App\Models\Dealer;
use App\Models\LogDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;
use App\Traits\FCM;
use App\Traits\Nepali_Calendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PaymentController extends Controller
{
    use FCM, ValidatesRequests;
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    //
    public function list()
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

        $totalcoll = Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])->whereIn('user_id', $mobileuser)->whereBetween('collection_date', [$engfrom, $engto])->sum('amount');
        $discount = Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])->whereIn('user_id', $mobileuser)->whereBetween('collection_date', [$engfrom, $engto])->sum('discount');

        $payment = DB::table('payments')
            ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'dealers.name as dealer', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.TDS', 'payments.discount', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status', 'payments.location')
            ->whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
            ->whereIn('payments.user_id', $mobileuser)
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->orderBy('payments.created_at', 'DESC')
            ->get();

        foreach ($payment as $payments) {
            $payments->previous_amt = LogDetail::where(['ordercoll_id' => $payments->id, 'flag' => "collection"])->latest()->first();
        }

        foreach ($payment as $n) {
            $date = $this->custom_AD_to_BS($n->collection_date);
            $n->collection_date = $date;
        }
        $data['payment'] = $payment;
        $data['users'] = $users;
        $data['mr'] = '';
        $data['from'] = $from;
        $data['to'] = $to;
        $data['start'] = "index";
        $data['total'] = $totalcoll;
        $data['discount'] = $discount;
        $data['status'] = "cash";

        return view('dashboards.payments.index', $data);
    }

    public function filter(Request $request)
    {
        $totalcoll = 0;
        $discount = 0;
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        // return $today;
        $this->validate($request, [
            'end-date' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:start-date'], //'required|date_format:Y-m-d|after_or_equal:start-date',
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:end-date'], //'nullable|required_with_all:end-date|date|date_format:Y-m-d',
            'user' => 'nullable|exists:mobileusers,id'
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
        // if (!empty($request['start-date'])) {
        $from = $this->custom_BS_to_AD($request['start-date']);
        $to = $this->custom_BS_to_AD($request['end-date']);
        // }

        // $date = date('Y-m-d');
        $user = $request['user'];
        $fr = $request['start-date'];
        $t = $request['end-date'];
        // $users = Mobileuser::where('role_id', 1)->get();


        if (!empty($user)) {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status')
                ->where('payments.user_id', '=', $user)
                ->whereBetween('payments.collection_date', [$from, $to])
                ->whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->orderBy('payments.created_at', 'DESC')
                ->get();
        } else {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status')
                ->whereBetween('payments.collection_date', [$from, $to])
                ->whereIn('payments.user_id', $mobileuser)
                ->whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->orderBy('payments.created_at', 'DESC')
                ->get();
        }

        foreach ($payment as $payments) {
            $payments->previous_amt = LogDetail::where(['ordercoll_id' => $payments->id, 'flag' => "collection"])->latest()->first();
        }

        foreach ($payment as $n) {

            $totalcoll = $totalcoll + $n->amount;
            $discount = $discount + $n->discount;

            $date = $this->custom_AD_to_BS($n->collection_date);
            $n->collection_date = $date;
            // $list[] = array(
            //     'id' => $n->id,
            //     'user' => $n->user,
            //     'dealer' => $n->dealer,
            //     'collection_date' => $date,
            //     'payment_type' => $n->payment_type,
            //     'amount' => $n->amount,
            //     'token' => $n->tokenvalue,
            //     'discount' => $n->discount,
            //     'TDS' => $n->TDS,
            //     'receipt_num' => $n->receipt_num,
            //     'status' => $n->status,
            // );
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
        $data['start'] = "";
        $data['total'] = $totalcoll;
        $data['discount'] = $discount;
        $data['userid'] = $user;
        $data['status'] = "cash";
        return view('dashboards.payments.index', $data);
        // if (empty($list)) {
        //     return view('dashboards.payments.index', ['payment' => '', 'users' => $users, 'mr' => $mr, 'from' => $fr, 'to' => $t, 'start' => '', 'total' => $totalcoll, 'discount' => $discount, 'userid' => $request->user]);
        // } else {
        //     return view('dashboards.payments.index', ['payment' => $list, 'users' => $users, 'mr' => $mr, 'from' => $fr, 'to' => $t, 'start' => '', 'total' => $totalcoll, 'discount' => $discount, 'userid' => $request->user]);
        // }
    }

    public function cheque_list()
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
            $users = Mobileuser::get();
            $mobileuser = Mobileuser::pluck('id');
        }
        $payment = DB::table('payments')
            ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->join('banks', 'banks.id', 'payments.bank_id')
            ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'dealers.name as dealer', 'payments.token as tokenvalue', 'payments.id', 'payments.TDS', 'payments.discount', 'payments.payment_type', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank', 'payments.status', 'payments.remarks')
            ->whereIn('payments.payment_type', ['cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC'])
            ->whereIn('payments.user_id', $mobileuser)
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->orderBy('payments.created_at', 'DESC')
            ->get();
        $totalcoll = Payment::whereIn('payments.payment_type', ['cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC'])
            ->whereIn('payments.user_id', $mobileuser)
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->sum('amount');
        $discount = Payment::whereIn('payments.payment_type', ['cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC'])
            ->whereIn('payments.user_id', $mobileuser)
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->sum('discount');

        foreach ($payment as $n) {
            $n->previous_amt = LogDetail::where(['ordercoll_id' => $n->id, 'flag' => "collection"])->latest()->first();
            if (!empty($n->payment_date)) {
                $payment_date = $this->custom_AD_to_BS($n->payment_date);
            } else {
                $payment_date = '';
            }
            $date = $this->custom_AD_to_BS($n->collection_date);
            $n->payment_date = $payment_date;
            $n->collection_date = $date;
            $n->token = $n->tokenvalue;
        }

        $data['payment'] = $payment;
        $data['users'] = $users;
        $data['mr'] = "";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['start'] = "index";
        $data['total'] = $totalcoll;
        $data['discount'] = $discount;
        $data['status'] = "cheque";

        return view('dashboards.payments.cheque', $data);

        if (!empty($list)) {
            return view('dashboards.payments.cheque', ['payment' => $list, 'users' => $users, 'mr' => '', 'from' => '', 'to' => '', 'start' => 'index', 'total' => $totalcoll, 'discount' => $discount]);
        } else {
            return view('dashboards.payments.cheque', ['payment' => '', 'users' => $users, 'mr' => '', 'from' => '', 'to' => '', 'start' => 'index', 'total' => 0, 'discount' => $discount]);
        }
    }

    public function cheque_filter(Request $request)
    {
        $this->validate($request, [
            'end-date' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:start-date'], //'nullable|date_format:Y-m-d|after_or_equal:start-date',
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:end-date'], //'nullable|required_with_all:end-date|date|date_format:Y-m-d',
            'user' => 'nullable|exists:mobileusers,id'
        ]);
        if ($request['start-date'] > $request['end-date']) {
            return back()->withInput($request->input())->withErrors(['end-date' => 'end date must be after or equal with start date']);
        }
        $totalcoll = 0;
        $discount = 0;
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        // if ($request['start-date'] > $today) {

        //     return redirect('dashboard/collection-cheque')->with('status', 'The start-date must be a date before or equal to today');
        // }
        $from = $this->custom_BS_to_AD($request['start-date']);
        $to = $this->custom_BS_to_AD($request['end-date']);

        $user = $request['user'];
        $fr = $request['start-date'];
        $t = $request['end-date'];

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


        if ($user) {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->join('banks', 'banks.id', 'payments.bank_id')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.discount', 'payments.TDS', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank', 'payments.status', 'payments.remarks')
                ->where('payments.user_id', $user)
                ->whereBetween('collection_date', [$from, $to])
                ->whereIn('payments.payment_type', ['cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC'])
                ->orderBy('payments.created_at', 'DESC')
                ->get();
            // return $payment;
            // $payment = Payment::where('user_id', $user)->where('payment_type', '=', 'cheque (bearer)')->orWhere('payment_type', '=', 'cash (account payee)')->whereBetween('collection_date', [$from, $to])->get();
        } else {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->join('banks', 'banks.id', 'payments.bank_id')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.discount', 'payments.TDS', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank', 'payments.status', 'payments.remarks')
                ->whereIn('payments.user_id', $mobileuser)
                ->whereBetween('collection_date', [$from, $to])
                ->whereIn('payments.payment_type', ['cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC'])
                ->orderBy('payments.created_at', 'DESC')
                ->get();
        }
        foreach ($payment as $n) {
            $n->previous_amt = LogDetail::where(['ordercoll_id' => $n->id, 'flag' => "collection"])->latest()->first();
            $totalcoll = $totalcoll + $n->amount;
            $discount = $discount + $n->discount;

            if (!empty($n->payment_date)) {
                $payment_date = $this->custom_AD_to_BS($n->payment_date);
            } else {
                $payment_date = '';
            }
            $date = $this->custom_AD_to_BS($n->collection_date);
            $n->payment_date = $payment_date;
            $n->collection_date = $date;
            $n->token = $n->tokenvalue;
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
        $data['start'] = "";
        $data['total'] = $totalcoll;
        $data['discount'] = $discount;
        $data['status'] = "cheque";
        $data['userid'] = $request->user;

        return view('dashboards.payments.cheque', $data);
    }

    public function update($id)
    {
        $payment = Payment::findorFail($id);

        $payment->status = request('status');
        $payment->remarks = request('remark');

        $payment->save();

        $date = date('Y-m-d');
        $log = new LogDetail();
        $log->flag = 'collection';
        $log->ordercoll_id = $payment->id;
        $log->cheque_no = $payment->cheque_num;
        $log->date = $date;
        $log->admin = Auth::id();
        $log->user_id = $payment->user_id;
        $log->status = request('status');
        $log->remarks = request('remark');
        $log->previous_status = request('pre_status');

        $log->save();

        $user = Mobileuser::find($payment->user_id);

        if ($user && $user->fcm_id) {
            $title = "Collection Status Updated";
            $body  = "{$payment->dealer->name} collection status has been updated to {$payment->status}";

            $data = [
                'route_channel' => 'collection?collection_id=' . $payment->id . '&dealer=' . $payment->dealer,
            ];

            $this->runFirebaseNotification($user->fcm_id, null, $title, $body, $data);
        }

        if ($payment->payment_type == 'cash (regular)' || $payment->payment_type == 'cash (against)') {

            return redirect('collection')->with('Status', 'Cash Status Changed.');
        } elseif ($payment->payment_type == 'direct') {
            return redirect('direct-collection')->with('Status', 'Direct Status Changed.');
        } else {
            return redirect('cheque-collection')->with('Status', 'Cheque Status Changed.');
        }
    }

    public function edit($id)
    {
        $payment = Payment::findorFail($id);

        if (!$payment->is_edit == 0 || !$payment->is_edit == null) {
            return back()->with('Status', "Collection already was edit.");
        }

        $payment->remarks = request('remarks');
        $payment->amount = request('amount');
        $payment->is_edit = 1;

        $payment->save();

        $date = date('Y-m-d');
        $log = new LogDetail();
        $log->flag = 'collection';
        $log->ordercoll_id = $payment->id;
        $log->cheque_no = $payment->cheque_num;
        $log->date = $date;
        $log->admin = Auth::id();
        $log->user_id = $payment->user_id;
        $log->remarks = request('remarks');
        $log->amount = request('pre_amount');

        $log->save();

        $dealer = Dealer::where('code', $payment->dealer_code)->first();
        $del = explode('::', $dealer->name);
        $dealer_name = implode('', $del);

        if (!empty($otp = Mobileuser::where(['id' => $payment->user_id])->first())) {
            $fcmid = $otp->fcm_id;
            $title = "Sales Tracker";
            $flag = 'collection';
            $detail = $payment->id;
            $amount = $payment->amount;
            $mess = request('remark');
            if (empty($mess)) {
                $mess = 'Collection amount Updated';
            }
            $content = array(
                'Title' => 'Collection Status Updated',
                'Message' => $mess,
                'payment_id' => $payment->id,
                'dealer_name' => $dealer_name,
                'dealer_code' => $dealer->code,
                'dealer_address' => $dealer->address,
                'Amount' => request('amount')
            );
            if (!empty($fcmid)) {
                $notice = new NotificationController();
            }
        }

        return back()->with('Status', "Collection amount Updated.");
    }

    public function direct_list()
    {
        $from = $this->custom_AD_to_BS(date('Y-m-d'));
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
            $users = Mobileuser::get();
            $mobileuser = Mobileuser::pluck('id');
        }
        $payment = DB::table('payments')
            ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->join('banks', 'banks.id', 'payments.bank_id')
            ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'payments.account_num', 'dealers.name as dealer', 'payments.token as tokenvalue', 'payments.id', 'payments.TDS', 'payments.discount', 'payments.payment_type', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank', 'payments.status', 'payments.remarks')
            ->where('payments.payment_type', 'direct')
            ->whereIn('payments.user_id', $mobileuser)
            ->where('collection_date', date('Y-m-d'))
            ->orderBy('payments.created_at', 'DESC')
            ->get();
        $totalcoll = Payment::where('payments.payment_type', 'direct')
            ->whereIn('payments.user_id', $mobileuser)
            ->where('collection_date', date('Y-m-d'))
            ->sum('amount');
        $discount = Payment::where('payments.payment_type', 'direct')
            ->whereIn('payments.user_id', $mobileuser)
            ->where('collection_date', date('Y-m-d'))
            ->sum('discount');

        foreach ($payment as $n) {
            $n->previous_amt = LogDetail::where(['ordercoll_id' => $n->id, 'flag' => "collection"])->latest()->first();
            if (!empty($n->payment_date)) {
                $payment_date = $this->custom_AD_to_BS($n->payment_date);
            } else {
                $payment_date = '';
            }
            $date = $this->custom_AD_to_BS($n->collection_date);
            $n->payment_date = $payment_date;
            $n->collection_date = $date;
            $n->token = $n->tokenvalue;
        }

        $data['payment'] = $payment;
        $data['users'] = $users;
        $data['mr'] = "";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['start'] = "index";
        $data['total'] = $totalcoll;
        $data['discount'] = $discount;
        $data['status'] = "direct";

        return view('dashboards.payments.direct', $data);
    }

    public function direct_filter(Request $request)
    {
        $this->validate($request, [
            'end-date' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:start-date'], //'nullable|date_format:Y-m-d|after_or_equal:start-date',
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:end-date'], //'nullable|required_with_all:end-date|date|date_format:Y-m-d',
            'user' => 'nullable|exists:mobileusers,id'
        ]);
        if ($request['start-date'] > $request['end-date']) {
            return back()->withInput($request->input())->withErrors(['end-date' => 'end date must be after or equal with start date']);
        }
        $totalcoll = 0;
        $discount = 0;
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        // if ($request['start-date'] > $today) {

        //     return redirect('dashboard/collection-cheque')->with('status', 'The start-date must be a date before or equal to today');
        // }
        $from = $this->custom_BS_to_AD($request['start-date']);
        $to = $this->custom_BS_to_AD($request['end-date']);

        $user = $request['user'];
        $fr = $request['start-date'];
        $t = $request['end-date'];

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


        if ($user) {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->join('banks', 'banks.id', 'payments.bank_id')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'dealers.name as dealer', 'payments.account_num', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.discount', 'payments.TDS', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank', 'payments.status', 'payments.remarks')
                ->where('payments.user_id', $user)
                ->whereBetween('collection_date', [$from, $to])
                ->where('payments.payment_type', 'direct')
                ->orderBy('payments.created_at', 'DESC')
                ->get();
            // return $payment;
            // $payment = Payment::where('user_id', $user)->where('payment_type', '=', 'cheque (bearer)')->orWhere('payment_type', '=', 'cash (account payee)')->whereBetween('collection_date', [$from, $to])->get();
        } else {
            $payment = DB::table('payments')
                ->join('mobileusers', 'mobileusers.id', 'payments.user_id')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->join('banks', 'banks.id', 'payments.bank_id')
                ->select('mobileusers.name as user', 'payments.is_edit', 'payments.lat_lng', 'payments.location', 'payments.account_num', 'dealers.name as dealer', 'payments.discount', 'payments.TDS', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.discount', 'payments.TDS', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank', 'payments.status', 'payments.remarks')
                ->whereIn('payments.user_id', $mobileuser)
                ->whereBetween('collection_date', [$from, $to])
                ->where('payments.payment_type', 'direct')
                ->orderBy('payments.created_at', 'DESC')
                ->get();
        }
        foreach ($payment as $n) {
            $n->previous_amt = LogDetail::where(['ordercoll_id' => $n->id, 'flag' => "collection"])->latest()->first();
            $totalcoll = $totalcoll + $n->amount;
            $discount = $discount + $n->discount;

            if (!empty($n->payment_date)) {
                $payment_date = $this->custom_AD_to_BS($n->payment_date);
            } else {
                $payment_date = '';
            }
            $date = $this->custom_AD_to_BS($n->collection_date);
            $n->payment_date = $payment_date;
            $n->collection_date = $date;
            $n->token = $n->tokenvalue;
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
        $data['start'] = "";
        $data['total'] = $totalcoll;
        $data['discount'] = $discount;
        $data['status'] = "direct";
        $data['userid'] = $request->user;

        return view('dashboards.payments.direct', $data);
    }

    public function pop($id)
    {
        $log = LogDetail::where('ordercoll_id', $id)->where('flag', 'collection')->get();
        foreach ($log as $n) {
            $explode = explode('::', $n->date);
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
            $date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;


            $list[] = array(
                'ordercoll_id' => $n->ordercoll_id,
                'status' => $n->status,
                'cheque_no' => $n->cheque_no,
                'previous_status' => $n->previous_status,
                'admin' => $n->admin,
                'remarks' => $n->remarks,
                'date' => $date,
            );
        }
        if (!empty($list)) {
            return view('dashboards.payments.list', ['log' => $list]);
        } else {
            return view('dashboards.payments.list');
        }
    }
    public function cashPop($id)
    {
        $log = LogDetail::with('adminUser')
            ->whereNot('admin', 0)
            ->where('ordercoll_id', $id)
            ->where('flag', 'collection')->get();

        foreach ($log as $n) {
            $explode = explode('::', $n->date);
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
            $date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;

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
            return view('dashboards.payments.cashcoll', ['log' => $list]);
        } else {
            return view('dashboards.payments.cashcoll');
        }
    }
}

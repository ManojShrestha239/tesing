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
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Models\ReceiptImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\Permission\Traits\HasRoles;

class CollectionController extends Controller
{
    use CustomNepaliCalendarConverter, Nepali_Calendar, ValidatesRequests, HasRoles;

    public function index()
    {
        $engto = date('Y-m-d');
        $engfrom = date('Y-m-d');
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
            $areas = Area::whereIn('areacode', $admin_area)->get();
            $users = Mobileuser::whereIn('id', $mobileuser)->get();
        } else {
            $mobileuser = Mobileuser::pluck('id');
            $users = Mobileuser::get();
            $areas = Area::all();
        }
        $payment = Payment::join('mobileusers', 'mobileusers.id', 'payments.user_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->leftjoin('banks', 'banks.id', 'payments.bank_id')
            ->select(
                'mobileusers.name as user',
                'payments.is_edit',
                'payments.location',
                'dealers.name as dealer',
                'dealers.address as dealer_address',
                'payments.token as tokenvalue',
                'payments.id',
                'payments.TDS',
                'payments.discount',
                'payments.payment_type',
                'payments.collection_date',
                'payments.amount',
                'payments.payment_date',
                'payments.cheque_num',
                'payments.receipt_num',
                'banks.name as bank',
                'payments.status',
                'payments.mRemarks',
                'payments.lat_lng'
            )
            ->whereBetween('collection_date', [$engfrom, $engto])
            ->whereIn('user_id', $mobileuser);
        $list = clone $payment;
        $data['payment'] = $list->get();
        $checqu = clone $payment;
        $cash = clone $payment;
        $token = clone $payment;
        $direct = clone $payment;
        $cashcollection = $cash->whereIn('payments.payment_type', ["cash (regular)", "cash (against)"]);
        $data['cash_coll'] = $cashcollection->sum('amount');

        $chequecollection = $checqu->whereIn('payments.payment_type', ["PDC", "cheque (bearer)", "cheque (payee)", 'cheque', 'cash (account payee)']);
        $data['cheque_amount'] = $chequecollection->sum("amount");

        $tokencollection = $token->where('payments.payment_type', '=', 'token');
        $data['token_amount'] = $tokencollection->sum("amount");

        $directcollection = $direct->where('payments.payment_type', '=', 'direct');
        $data['direct_amount'] = $directcollection->sum("amount");
        foreach ($data['payment'] as $pay) {
            $pay->previous_amt = LogDetail::where(['ordercoll_id' => $pay->id, 'flag' => "collection"])->latest()->first();
            if ($pay->payment_date) {
                $payment_date = $this->custom_AD_to_BS($pay->payment_date);
            } else {
                $payment_date = '';
            }
            $collection_date = $this->custom_AD_to_BS($pay->collection_date);
            $pay->collection_date = $collection_date;
            $pay->payment_date = $payment_date;
        }
        $data['users'] = $users;
        $data['header'] = "Collection $from to $to";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['status'] = '';
        $data['areas'] = $areas;
        $data['areacode'] = '';
        return view('dashboards.payments.all', $data);
    }

    public function show($id)
    {
        $data['payment'] = ReceiptImage::where('collection_id', $id)->get();

        return view('dashboards.payments.receiptimg', $data);
    }

    public function update(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'status' => 'required',
            'remark' => 'required|max:255'
        ]);
        if ($valid->fails()) {
            return back()->withErrors($valid);
        }
        $payment = Payment::findorFail($id);


        $date = date('Y-m-d');
        $log = new LogDetail();
        $log->flag = 'collection';
        $log->ordercoll_id = $payment->id;
        $log->cheque_no = $payment->cheque_num;
        $log->date = $date;
        $log->admin = Auth::user()->id;
        $log->user_id = $payment->user_id;
        $log->status = request('status');
        $log->remarks = request('remark');
        $log->previous_status = $payment->status;

        $log->save();

        $payment->status = request('status');
        $payment->remarks = request('remark');

        $payment->save();

        $dealer = Dealer::where('code', $payment->dealer_code)->first();
        $dealer_name = $dealer->name;

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
            if (!empty($fcmid)) {
                $notice = new NotificationController();
                // $notice->individualNotificationSend($fcmid, $title, $flag, $content);
            }
        }
        return back()->with('Status', 'Collection Status Updated.');
    }

    public function log(Request $request, $id)
    {
        $data['log'] = LogDetail::where('ordercoll_id', $id)->get();
        if ($request->status == "token") {
            return view('dashboards.tokens.log', $data);
        } else if ($request->status == "collection") {
            return view('dashboards.payments.list', $data);
        } else if ($request->status == "direct") {
            return view('dashboards.payments.list', $data);
        } else if ($request->status == "token") {
            return view('dashboards.payments.list', $data);
        } else if ($request->status == "all") {
            return view('dashboards.payments.list', $data);
        } else {
            // abort(404);
            return view('dashboards.payments.list', $data);
        }
    }

    public function filter(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'collection_type' => 'nullable|in:cash,cheque,token,direct',
            'start_date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:end_date'],
            'end_date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:start_date']
        ]);

        //nepali date validation
        if ($request['start-date'] > $request['end-date']) {
            return back()->withInput($request->input())->withErrors(['end-date' => 'The end date must be after or equal to the start date.']);
        }

        $from = $this->custom_BS_to_AD($request->start_date);
        $to = $this->custom_BS_to_AD($request->end_date);

        $paymentTypeMap = [
            'cash' => ['cash (against)', 'cash (regular)'],
            'cheque' => ['cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC'],
            'direct' => ['direct'],
            'token' => ['token'],
            'all' => ['cash (against)', 'cash (regular)', 'cheque (bearer)', 'cash (account payee)', 'cheque (payee)', 'cheque', 'PDC', 'direct', 'token']
        ];
        $summaryTypes = [
            'token' => ['token'],
            'direct' => ['direct'],
            'cheque' => ['cheque (bearer)', 'cheque (payee)', 'cheque', 'PDC'],
            'cash' => ['cash (against)', 'cash (regular)']
        ];
        $collectionType = $request->collection_type;
        $payment_type = $paymentTypeMap[$collectionType] ?? $paymentTypeMap['all'];

        if ($request->user) {
            $payment = Payment::where(['payments.user_id' => $request->user])
                ->whereIn('payments.payment_type', $payment_type)
                ->whereBetween('collection_date', [$from, $to]);
            $data['payment'] = $payment->get();
            $data['total'] = $payment->sum("amount");
            $data['discount'] = $payment->sum("discount");
            $data['TDS'] = $payment->sum("TDS");

            $collamt = Payment::where(['payments.user_id' => $request->user])->whereBetween('collection_date', [$from, $to]);
            foreach ($summaryTypes as $key => $types) {
                $data[$key . '_amount'] = $collamt->whereIn('payment_type', $types)->sum('amount');
                $data['discount_' . $key . '_amount'] = $collamt->whereIn('payment_type', $types)->sum('discount');
                $data['tds_' . $key . '_amount'] = $collamt->whereIn('payment_type', $types)->sum('TDS');
            }
            $user = Mobileuser::findorFail($request->user);
            $data['header'] = "$collectionType Collection Of $user->name - ($request->start_date-$request->end_date)";
        } else {
            $payment = Payment::whereIn('payments.payment_type', $payment_type)->whereBetween('collection_date', [$from, $to]);
            $data['payment'] = $payment->get();
            $data['total'] = $payment->sum("amount");
            $data['discount'] = $payment->sum("discount");
            $data['TDS'] = $payment->sum("TDS");

            $collamt = Payment::whereBetween('collection_date', [$from, $to]);
            foreach ($summaryTypes as $key => $types) {
                $data[$key . '_amount'] = $collamt->whereIn('payment_type', $types)->sum('amount');
                $data['discount_' . $key . '_amount'] = $collamt->whereIn('payment_type', $types)->sum('discount');
                $data['tds_' . $key . '_amount'] = $collamt->whereIn('payment_type', $types)->sum('TDS');
            }
            $data['header'] = "$collectionType Collection ($request->start_date-$request->end_date)";
        }

        foreach ($data['payment'] as $value) {
            $value->dealer_address = $value->dealer ? $value->dealer->address : "N/A";
            $value->dealer = $value->dealer ? $value->dealer->name : "N/A";
            $value->bank = $value->bank ? $value->bank->name : "N/A";
            $value->user = $value->user ? $value->user->name : "N/A";
        }

        $data['users'] = Mobileuser::all();
        $data['from'] = $request->start_date;
        $data['to'] = $request->end_date;
        $data['userid'] = $request->user;
        $data['status'] = $request->collection_type;
        if ($request->page == "all") {
            return view('dashboards.payments.all', $data);
        } else {
            if ($request->collection_type == "cash") {
                return view('dashboards.payments.index', $data);
            } else if ($request->collection_type == "cheque") {
                return view('dashboards.payments.cheque', $data);
            } else if ($request->collection_type == "direct") {
                return view('dashboards.payments.direct', $data);
            } else if ($request->collection_type == "token") {
                return view('dashboards.tokens.view', $data);
            } else {
                return view('dashboards.payments.all', $data);
            }
        }
    }
}

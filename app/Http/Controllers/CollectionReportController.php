<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CollectionReport;
use App\Exports\CollectionSummaryReport;
use App\Exports\ZeroReport;
use App\Models\Mobileuser;
use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\DB;

class CollectionReportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index(Request $request)
    {
        // return 1;
        $this->validate($request, [
            'from' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'to' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
        ]);
        if ($request['from'] > $request['to']) {
            return back()->withInput($request->input())->withErrors(['to' => 'end date must be after or equal with start date']);
        }
        $from = '';
        $to = '';
        if ($request->from) {
            $from = $this->custom_BS_to_AD($request->from);
        }
        if ($request->to) {
            $to = $this->custom_BS_to_AD($request->to);
        }

        if ($request->status == "cheque") {
            $status = ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'];
        }
        if ($request->status == 'cash') {
            $status = ['cash (regular)', 'cash (against)'];
        }
        if ($request->status == 'token') {
            $status = ['token'];
        }
        if ($request->status == 'direct') {
            $status = ['direct'];
        }
        $payment = Payment::leftjoin('banks', 'banks.id', 'payments.bank_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->select(
                'payments.id',
                'payments.collection_date',
                'dealers.code',
                'dealers.name',
                'dealers.address',
                'payments.receipt_num',
                'payments.payment_type',
                'payments.cheque_num',
                'payments.discount',
                'payments.TDS',
                'payments.payment_date',
                'banks.name as bank_name',
                'payments.amount',
                'payments.status',
                DB::raw("'' as dep_bank"),
                DB::raw("'' as dep_date"),
                'payments.remarks'
            );

        if (!empty($status) && !empty($user) && !empty($from)) {
            $payments = $payment->where(['payments.user_id' => $user])
                ->whereBetween('collection_date', [$from, $this->to])
                ->whereIn('payment_type', $status)
                ->get();
        } else if (empty($this->status) && !empty($this->user) && !empty($this->from) && !empty($this->to)) {
            $payments = $payment->where(['payments.user_id' => $this->user])
                ->whereBetween('collection_date', [$this->from, $this->to])
                ->get();
        } else if (empty($this->status) && empty($this->user) && !empty($this->from) && !empty($this->to)) {
            $payments = $payment->whereBetween('collection_date', [$this->from, $this->to])
                ->get();
        } else if (!empty($this->status) && empty($this->user) && !empty($this->from) && !empty($this->to)) {
            $payments = $payment->whereBetween('collection_date', [$this->from, $this->to])
                ->whereIn('payment_type', $status)
                ->get();
        } else {
            $payments = $payment->where('collection_date', date('Y-m-d'))->get();
        }
        foreach ($payments as $payment) {
            $date = explode('-', $payment->collection_date);
            $currentNepaliDay = $this->AD_to_BS($date[0], $date[1], $date[2]);
            $nepaliDay = $currentNepaliDay['date'];
            if (strlen($nepaliDay) == 1) {
                $nepaliDay = '0' . $nepaliDay;
            }
            $nepaliMth = $currentNepaliDay['month'];
            if (strlen($nepaliMth) == 1) {
                $nepaliMth = '0' . $nepaliMth;
            }
            $nepaliYr = $currentNepaliDay['year'];
            $date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
            $payment->collection_date = $date;
        }

        // return $payments;

        // ob_end_clean();
        // ob_start();
        Excel::download(new CollectionReport($payments, $status = $request->status, $user = $request->user, $from, $to), 'receiprt report.xlsx');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // return 2;
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'collection_type' => 'nullable|in:cash,cheque,token,direct',
            'from' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'to' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from']
        ]);
        if ($request['from'] > $request['to']) {
            return back()->withInput($request->input())->withErrors(['to' => 'end date must be after or equal with start date']);
        }
        $userinfo = null;
        if ($request->user) {
            $userinfo = Mobileuser::findorFail($request->user);
        }
        $from = '';
        $to = '';
        $user = $request->user;
        if ($request->from) {
            $from = $this->custom_BS_to_AD($request->from);
        }
        if ($request->to) {
            $to = $this->custom_BS_to_AD($request->to);
        }

        if ($request->status == "cheque") {
            $status = ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'];
        }
        if ($request->status == 'cash') {
            $status = ['cash (regular)', 'cash (against)'];
        }
        if ($request->status == 'token') {
            $status = ['token'];
        }
        if ($request->status == 'direct') {
            $status = ['direct'];
        }
        $payment = Payment::with('user', 'dealer', 'bank')->leftjoin('banks', 'banks.id', 'payments.bank_id')
            ->join('dealers', 'dealers.code', 'payments.dealer_code')
            ->select(
                'payments.id',
                'payments.user_id',
                'payments.dealer_code',
                'payments.bank_id',
                'payments.collection_date',
                'dealers.code',
                'dealers.name',
                'dealers.address',
                'payments.receipt_num',
                'payments.payment_type',
                'payments.cheque_num',
                'payments.discount',
                'payments.TDS',
                'payments.payment_date',
                'banks.name as bank_name',
                'payments.amount',
                'payments.status',
                DB::raw("'' as dep_bank"),
                DB::raw("'' as dep_date"),
                'payments.remarks'
            );

        if ($request->status && $user) {
            $payments = $payment->where(['payments.user_id' => $user])
                ->whereBetween('collection_date', [$from, $to])
                ->whereIn('payment_type', $status)
                ->get();
        } else if (!$request->status && $user) {
            $payments = $payment->where(['payments.user_id' => $user])
                ->whereBetween('collection_date', [$from, $to])
                ->get();
        } else if ($request->status && !$user) {
            $payments = $payment->whereBetween('collection_date', [$from, $to])
                ->whereIn('payment_type', $status)
                ->get();
        } else {
            $payments = $payment->whereBetween('collection_date', [$from, $to])
                ->get();
        }
        // foreach ($payments as $payment) {
        //     $payment->collection_date = $this->custom_AD_to_BS($payment->collection_date);
        // }

        // return $payments;

        // ob_end_clean();
        // ob_start();

        return Excel::download(new CollectionReport($payments, $request->status, $userinfo, $from, $to), "$request->status Collection report.xlsx");
    }

    public function zeroReport(Request $request)
    {
        // return $request->all();
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            // 'collection_type' => 'nullable|in:cash,cheque,token',
            'from' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:to'],
            'to' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with_all:from', 'after_or_equal:from']
        ]);

        $dateNepali = explode('-', $request->to);
        $month = $this->get_nepali_month($dateNepali[1]);

        ob_end_clean();
        ob_start();
        if (isset($request->user)) {
            $userName = Mobileuser::findorfail($request->user)->name;
            return Excel::download(new ZeroReport($request->from, $request->to, $month, $request->user), '' . $userName . '_' . $month .  '_zeroReport_' . time() . '.xlsx');
        }
        return Excel::download(new ZeroReport($request->from, $request->to, $month, $request->user), '' . $month .  '_zeroReport_' . time() . '.xlsx');
    }

    public function collectionSummaryReport(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'collection_type' => 'nullable|in:cash,cheque,token,direct',
            'from' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'to' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from']
        ]);
        if ($request['from'] > $request['to']) {
            return back()->withInput($request->input())->withErrors(['to' => 'end date must be after or equal with start date']);
        }
        $userinfo = null;
        if ($request->user) {
            $userinfo = Mobileuser::findorFail($request->user);
        }
        $from = '';
        $to = '';
        $user = $request->user;
        $from = $this->custom_BS_to_AD($request->from);
        $to = $this->custom_BS_to_AD($request->to);

        if ($request->status == "cheque") {
            $status = ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'];
        }
        if ($request->status == 'cash') {
            $status = ['cash (regular)', 'cash (against)'];
        }
        if ($request->status == 'token') {
            $status = ['token'];
        }
        if ($request->status == 'direct') {
            $status = ['direct'];
        }
        $payment = Payment::with('user', 'dealer', 'bank')
            ->groupBy('user_id', 'dealer_code');

        if ($request->status && $user) {
            $payments = $payment->where(['payments.user_id' => $user])
                ->whereBetween('collection_date', [$from, $to])
                ->whereIn('payment_type', $status)
                ->get();
        } else if (!$request->status && $user) {
            $payments = $payment->where(['payments.user_id' => $user])
                ->whereBetween('collection_date', [$from, $to])
                ->get();
        } else if ($request->status && !$user) {
            $payments = $payment->whereBetween('collection_date', [$from, $to])
                ->whereIn('payment_type', $status)
                ->get();
        } else {
            $payments = $payment->whereBetween('collection_date', [$from, $to])
                ->get();
        }

        foreach ($payments as $payment) {
            if ($request->status == "cash") {
                $payment->cashamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount');
                $payment->chequeamt = 0;
                $payment->directamt = 0;
                $payment->tokenamt = 0;
            } else if ($request->status == "cheque") {
                $payment->cashamt = 0;
                $payment->chequeamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'])->sum('amount');
                $payment->directamt = 0;
                $payment->tokenamt = 0;
            } else if ($request->status == "direct") {
                $payment->cashamt = 0;
                $payment->chequeamt = 0;
                $payment->directamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['direct'])->sum('amount');
                $payment->tokenamt = 0;
            } else if ($request->status == "token") {
                $payment->cashamt = 0;
                $payment->chequeamt = 0;
                $payment->directamt = 0;
                $payment->tokenamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['token'])->sum('amount');
            } else {
                $payment->cashamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount');
                $payment->chequeamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'])->sum('amount');
                $payment->directamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['direct'])->sum('amount');
                $payment->tokenamt = Payment::where(['user_id' => $payment->user_id, 'dealer_code' => $payment->dealer_code])->whereIn('payment_type', ['token'])->sum('amount');
            }
        }

        return Excel::download(new collectionSummaryReport($payments, $request->status, $userinfo, $from, $to), "$request->status" . "_Collection report_" . "$user" . "_" . time() . ".xlsx");
    }
}

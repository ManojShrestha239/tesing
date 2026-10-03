<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\LogDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class PaymentController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function collectionHistory(Request $request)
    {
        $user = Auth::user();
        $area_code = explode(' :: ', $user->areas);
        $subarea = explode(' :: ', $user->sub_areas);

        $token = number_format(Payment::where('payments.payment_type', '=', 'token')
            ->where('user_id', $user->id)
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->sum('amount'), 2, '.', '');
        $cash = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
            ->where('user_id', $user->id)
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->where('status', '!=', 'Cancelled')
            ->sum('amount'), 2, '.', '');

        $cashdiscount = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
            ->where('user_id', $user->id)
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->where('status', '!=', 'Cancelled')
            ->sum('discount'), 2, '.', '');

        $cheque = number_format(Payment::where('user_id', $user->id)
            ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
            ->where('status', '!=', 'Bounced')
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->sum('amount'), 2, '.', '');

        $chequediscount = number_format(Payment::where('user_id', $user->id)
            ->where('status', '!=', 'Bounced')
            ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->sum('discount'), 2, '.', '');

        $id = $user->id;

        $data = Payment::leftJoin('banks', 'payments.bank_id', 'banks.id')
            ->leftJoin('dealers', 'payments.dealer_code', '=', 'dealers.code') // join with dealers
            ->select(
                'payments.id',
                'payments.payment_type',
                'payments.dealer_code',
                'dealers.name as dealer_name',
                'dealers.address as dealer_address',
                'payments.token',
                'payments.collection_date',
                'payments.amount',
                'payments.payment_date',
                'payments.cheque_num',
                'payments.receipt_num',
                'banks.name as bank_name',
                'payments.status',
                'payments.mRemarks',
                'payments.remarks'
            )
            ->where('user_id', $id)
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->orderBy('payments.created_at', 'DESC')
            ->paginate(10);

        return response()->json(['data' => $data, 'cheque' => $cheque, 'cash' => $cash, 'cashdiscount' => $cashdiscount, 'chequediscount' => $chequediscount]);
    }

    public function filter(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'dealer' => 'nullable|string|max:191',
            'status' => 'nullable|string|max:191',
            'from' => 'nullable|required_with:to|date|date_format:Y-m-d',
            'subarea' => 'nullable|string|max:191',
            'to' => 'nullable|required_with:from|date|date_format:Y-m-d',
            'sorting' => 'nullable|string|max:191|in:ASC,DESC',
            'payment_type' => 'nullable|in:cheque (bearer),cheque (payee),cash (regular),cash (against),cash (account payee)'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $sort = $request->sorting;
        if (!$sort) {
            $sort = 'DESC';
        }
        $user = Auth::user();
        $cheque = 0;
        $cash = 0;
        $cashdiscount = 0;
        $chequediscount = 0;
        $token = 0;
        if ($request->status) {
            $status = explode(',', $request->status);
        } else {
            $status = [];
        }
        if ($request->from) {
            $valid = Validator::make($request->all(), [
                'to' => 'required'
            ]);
            if ($valid->fails()) {
                return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
            }
        }

        if ($request->dealer && $request->from) {
            $payments = DB::table('payments')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->leftJoin('banks', 'banks.id', 'payments.bank_id')
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->where('payments.user_id', $user->id)
                ->orderBy('payments.created_at', $sort)
                ->select('payments.id', 'payments.payment_type', 'payments.dealer_code', 'payments.token', 'payments.collection_date', 'payments.amount', 'payments.discount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank_name', 'payments.status', 'payments.mRemarks', 'payments.remarks')
                ->paginate(10);

            $cash = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->where('user_id', $user->id)
                ->sum('amount'), 2, '.', '');

            $cashdiscount = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->where('user_id', $user->id)
                ->sum('discount'), 2, '.', '');

            $cheque = number_format(Payment::where('user_id', $user->id)
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('amount'), 2, '.', '');

            $chequediscount = number_format(Payment::where('user_id', $user->id)
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('discount'), 2, '.', '');
        } else if ($request->dealer && !$request->from) {
            $payments = DB::table('payments')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->leftJoin('banks', 'banks.id', 'payments.bank_id')
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->where('payments.user_id', $user->id)
                ->orderBy('payments.created_at', $sort)
                ->select('payments.id', 'payments.payment_type', 'payments.dealer_code', 'payments.token', 'payments.collection_date', 'payments.amount', 'payments.discount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank_name', 'payments.status', 'payments.mRemarks', 'payments.remarks')
                ->paginate(10);

            $cash = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->where('user_id', $user->id)
                ->sum('amount'), 2, '.', '');

            $cashdiscount = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->where('user_id', $user->id)
                ->sum('discount'), 2, '.', '');

            $cheque = number_format(Payment::where('user_id', $user->id)
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('amount'), 2, '.', '');

            $chequediscount = number_format(Payment::where('user_id', $user->id)
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->Where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('discount'), 2, '.', '');
        } else if (!$request->dealer && $request->from) {
            $payments = DB::table('payments')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->leftJoin('banks', 'banks.id', 'payments.bank_id')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->where('payments.user_id', $user->id)
                ->orderBy('payments.created_at', $sort)
                ->select('payments.id', 'payments.payment_type', 'payments.dealer_code', 'payments.token', 'payments.collection_date', 'payments.amount', 'payments.discount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank_name', 'payments.status', 'payments.mRemarks', 'payments.remarks')
                ->paginate(10);

            $cash = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->where('user_id', $user->id)
                ->sum('amount'), 2, '.', '');

            $cashdiscount = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->where('user_id', $user->id)
                ->sum('discount'), 2, '.', '');

            $cheque = number_format(Payment::where('user_id', $user->id)
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('amount'), 2, '.', '');

            $chequediscount = number_format(Payment::where('user_id', $user->id)
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->wherebetween('payments.collection_date', [$request->from, $request->to])
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('discount'), 2, '.', '');
        } else {
            $payments = DB::table('payments')
                ->join('dealers', 'dealers.code', 'payments.dealer_code')
                ->leftJoin('banks', 'banks.id', 'payments.bank_id')
                ->where('payments.user_id', $user->id)
                ->orderBy('payments.created_at', $sort)
                ->select('payments.id', 'payments.payment_type', 'payments.dealer_code', 'payments.token', 'payments.collection_date', 'payments.amount', 'payments.discount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank_name', 'payments.status', 'payments.mRemarks', 'payments.remarks')
                ->paginate(10);

            $cash = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->where('user_id', $user->id)
                ->sum('amount'), 2, '.', '');

            $cashdiscount = number_format(Payment::whereIn('payments.payment_type', ['cash (against)', 'cash (regular)'])
                ->where('user_id', $user->id)
                ->sum('discount'), 2, '.', '');

            $cheque = number_format(Payment::where('user_id', $user->id)
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('amount'), 2, '.', '');

            $chequediscount = number_format(Payment::where('user_id', $user->id)
                ->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])
                ->sum('discount'), 2, '.', '');
        }
        foreach ($payments as $d) {
            $d->collection_date = $this->custom_AD_to_BS($d->collection_date);
            $d->payment_date = $d->payment_date ? $this->custom_AD_to_BS($d->payment_date) : "";
        }
        return response()->json(['data' => $payments, 'cheque' => $cheque, 'cash' => $cash, 'cashdiscount' => $cashdiscount, 'chequediscount' => $chequediscount]);
    }
    public function updatestatus($id, Request $request)
    {
        $valid = Validator::make($request->all(), [
            'status' => 'required|in:Sent to Office,Encashed,Deposited in Bank,Bounced',
            'remark' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $mo = Auth::user();
        $payment = Payment::where('id', $id)->first();
        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => "data not found"], 404);
        }

        $payment->status = $request->status;
        $payment->remarks = $request->remark;

        $payment->save();

        $log = new LogDetail();
        $date = date('Y-m-d');

        $log->ordercoll_id = $id;
        $log->flag = 'collection';
        $log->cheque_no = $payment->cheque_num;
        $log->date = $date;
        $log->mo_user = $mo->id;
        $log->user_id = $payment->user_id;
        $log->status = $request->status;
        $log->remarks = $request->remark;
        $log->previous_status = $payment->status;

        $log->save();

        return response()->json(['status' => 'success', 'message' => 'Status Changed']);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\Denomination;
use App\Models\Mobileuser;
use App\Models\Schedulevisit;
use Illuminate\Http\Request;
use App\Models\Negotiation;
use App\Models\Session;
use App\Models\LogDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Denominationpoint;
use App\Models\SchedulePurpose;
use Illuminate\Support\Facades\Validator;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class PaymentController extends Controller
{
    use CustomNepaliCalendarConverter, Nepali_Calendar;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = date('Y-m-d');
        $user =  Auth::user();
        $user_id = explode('::', $user->id);
        $id = implode('', $user_id);

        $amount = Payment::where('user_id', $id)->where('collection_date', $date)->sum('amount');

        $count = Session::where('date', $date)->where('user_id', $id)->count();
        // $negotiation = Negotiation::where('date', $date)->where('user_id', $id)->get();
        // $count = count($negotiation) + count($session);
        return response()->json(['status' => 'success', 'error_code' => '200', 'collection' => $amount, 'visit' => $count]);
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
            'payment_type' => 'required',
            'dealer_code' => 'required|exists:dealers,code',
            'user_id' => 'required|exists:mobileusers,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_date' => 'nullable|date',
            'bank_id' => "nullable|exists:banks,id",
            'TDS' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'remarks' => 'required|string|max:255',
            'token' => 'nullable|numeric',
            'quantity' => 'nullable|numeric'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $payment = new Payment;
            $schedules = Schedulevisit::where(['user_id' => $request->user_id, 'dealer_code' => $request->dealer_code, 'date' => $request->collection_date, 'is_visit' => 0])->get();
            foreach ($schedules as $schedule) {
                SchedulePurpose::where('visitschedule_id', $schedule->id)->where('purpose_id', $request->purpose_id)->update(['status' => 'visit']);
                $SchedulevisitCount = SchedulePurpose::where(['visitschedule_id' => $schedule->id, 'status' => 'pending'])->count();
                if ($SchedulevisitCount == 0) {
                    Schedulevisit::where(['id' => $schedule->id])->update(['is_visit' => 1]);
                }
            }
            $dealer = Dealer::findorFail($request->dealer_code);
            $payment->areacode = $dealer->area_code;
            $payment->payment_type = $request->payment_type;
            $payment->dealer_code = $request->dealer_code;
            $payment->user_id = $request->user_id;
            $payment->collection_date = $request->collection_date;
            $payment->amount = $request->amount;

            $payment->mRemarks = $request->remarks;


            if ($request['payment_type'] == 'cash (regular)') {
                $payment->receipt_num = $request->receipt_num;
                $payment->status = 'Collected by MO';
                $payment->TDS = $request->TDS;
                $payment->token = 0;
                $payment->discount = $request->discount;
            } elseif ($request['payment_type'] == 'cash (against)') {
                $payment->payment_date = $request->payment_date;
                $payment->receipt_num = $request->receipt_num;
                $payment->cheque_num = $request->cheque_num;

                $payment->bank_id = $request->bank_id;
                $payment->status = 'Collected by MO';
            } elseif ($request['payment_type'] == 'token') {
                $payment->payment_date = $request->payment_date;
                $payment->receipt_num = $request->receipt_num;
                $payment->bank_id = 1;
                $payment->status = 'Collected by MO';
                $payment->TDS = 0;
                $payment->token = 0;
                $payment->discount = 0;
            } elseif ($request['payment_type'] == 'cheque (bearer)') {
                $payment->receipt_num = $request->receipt_num;
                $payment->cheque_num = $request->cheque_num;
                $payment->payment_date = $request->payment_date;

                $payment->bank_id = $request->bank_id;
                $payment->status = 'Collected by MO';
                $payment->TDS = $request->TDS;
                $payment->token = 0;
                $payment->discount = $request->discount;
            } else {
                $payment->receipt_num = $request->receipt_num;
                $payment->cheque_num = $request->cheque_num;
                $payment->payment_date = $request->payment_date;

                $payment->bank_id = $request->bank_id;
                $payment->status = 'Collected by MO';
                $payment->TDS = $request->TDS;
                $payment->token = 0;
                $payment->discount = $request->discount;
            }


            $payment->save();

            if ($request->payment_type == 'token') {
                foreach ($request->denomination as $token) {
                    // $denomination = Denomination::where('id', $token['id'])->first();
                    $denomination = Denomination::where('denomination', $token['denomination'])->first();
                    $qty = $token['quantity'];
                    $data[] = array(
                        'collection_id' => $payment->id,
                        'denomination' => $token['denomination'],
                        'quantity' => $qty,
                        'denomination_id' => $denomination->id,
                        'total' => $token['denomination'] * $qty,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                }
                Denominationpoint::insert($data);
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return  response()->json(['error' => $e->getMessage()], 503);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Payment::findorFail($id);
        $bank_id = $data->bank_id;
        if ($data->payment_type == 'token') {
            $Denominationpoint = Denominationpoint::where('collection_id', $id)->get();
        } else {
            $Denominationpoint = null;
        }

        // $bank_id = explode('::',$data->bank_id);
        $bank = Bank::where('id', $data->bank_id)->first();
        if ($bank) {
            $name = $bank->name;
        } else {
            $name = '';
        }
        $collection = array(
            'id' => $data->id,
            'payment_type' => $data->payment_type,
            'dealer_code' => $data->dealer_code,
            'collection_date' => $data->collection_date,
            'amount' => $data->amount,
            'payment_date' => $data->payment_date,
            'receipt_num' => $data->receipt_num,
            'cheque_num' => $data->cheque_num,
            'bank_name' => $name,
            'TDS' => $data->TDS,
            'discount' => $data->discount,
            'status' => $data->status,
            "remarks" => $data->remarks,
            'mRemaks' => $data->mRemarks,
            'denomination' => $Denominationpoint,
        );
        return response()->json($collection);
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

    public function collectionHistory(Request $request)
    {
        $user = Auth::user();
        $id = $user->id;

        $data = DB::table('payments')
            ->leftJoin('banks', 'payments.bank_id', 'banks.id')
            ->select('payments.id', 'payments.payment_type', 'payments.dealer_code', 'payments.collection_date', 'payments.amount', 'payments.payment_date', 'payments.cheque_num', 'payments.receipt_num', 'banks.name as bank_name', 'payments.status', 'payments.mRemarks', 'payments.remarks')
            ->where('user_id', $id)
            ->orderBy('payments.created_at', 'DESC')
            ->paginate(10);

        foreach ($data as $d) {
            if ($d->payment_type == 'token') {
                $value = Denominationpoint::where('collection_id', $d->id)->get();
                $d->denomination = $value;
            } else {
                $d->denomination = null;
            }
        }
        return response()->json($data);
    }
}

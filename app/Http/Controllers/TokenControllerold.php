<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Models\Denominationpoint;
use App\Models\LogDetail;
use App\Models\Mobileuser;
use App\Models\Payment;
use Carbon\Carbon;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    use Nepali_calendar, CustomNepaliCalendarConverter;
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
            ->select('mobileusers.name as user', 'dealers.name as dealer', 'payments.token as tokenvalue', 'payments.id', 'payments.payment_type', 'payments.TDS', 'payments.discount', 'payments.collection_date', 'payments.amount', 'payments.receipt_num', 'payments.status')
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Denominationpoint::where('collection_id', $id)->get();

        return view('dashboards.tokens.list', ["payments" => $payment]);
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

<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Dealer;
use App\Models\Location;
use App\Models\Mobileuser;
use App\Models\Payment;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use App\Models\VisitPurpose;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index()
    {
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        $date = date('Y-m-d');

        $isASM = Auth::user()->hasRole('ASM');
        $mobileuserIds = collect();
        $usersQuery = Mobileuser::query();

        if ($isASM) {
            $adminAreaCodes = Auth::user()->area->pluck('areacode');

            $mobileuserIds = Area::whereIn('areas.areacode', $adminAreaCodes)
                ->join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');

            $usersQuery->whereIn('id', $mobileuserIds);
        }

        $paymentQuery = Payment::where('collection_date', $date);

        if ($isASM) {
            $paymentQuery->whereIn('user_id', $mobileuserIds);
        }

        $paymentStats = $paymentQuery->selectRaw("
                SUM(amount) as total_amount,
                SUM(CASE WHEN payment_type IN ('cash (regular)', 'cash (against)') THEN amount ELSE 0 END) as cash_amount,
                SUM(CASE WHEN payment_type IN ('cheque (bearer)', 'cheque (payee)') THEN amount ELSE 0 END) as cheque_amount,
                SUM(CASE WHEN payment_type = 'token' THEN amount ELSE 0 END) as token_amount,
                SUM(discount) as total_discount,
                SUM(CASE WHEN payment_type IN ('cash (regular)', 'cash (against)') THEN discount ELSE 0 END) as cash_discount,
                SUM(CASE WHEN payment_type IN ('cheque (bearer)', 'cheque (payee)') THEN discount ELSE 0 END) as cheque_discount
            ")->first();

        $sessionQuery = Session::where('date', $date)
            ->groupBy('user_id')
            ->select('visits.*', DB::raw('count(*) as visit_count'));

        if ($isASM) {
            $sessionQuery->whereIn('user_id', $mobileuserIds);
        } else {
            $sessionQuery->orderBy('visit_count', 'DESC');
        }

        $sessions = $sessionQuery->get();

        $userIds = $sessions->pluck('user_id')->unique()->toArray();

        $visitPurposes = VisitPurpose::where('date', $date)
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id', 'purpose_id')
            ->select('user_id', 'purpose_id', DB::raw('count(*) as purpose_count'))
            ->get()
            ->groupBy('user_id');

        $collections = Payment::where('collection_date', $date)
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->select('user_id', DB::raw('SUM(amount) as collection_amount'))
            ->get()
            ->keyBy('user_id');

        $sessions->each(function ($session) use ($visitPurposes, $collections) {
            $session->purposes = $visitPurposes->get($session->user_id, collect());
            $session->collection = $collections->get($session->user_id)->collection_amount ?? 0;
        });

        $totalVisits = Session::where('date', $date)->when($isASM, function ($query) use ($mobileuserIds) {
            return $query->whereIn('user_id', $mobileuserIds);
        })->count();

        $data = [
            'users' => $usersQuery->get(),
            's' => $totalVisits,
            'amt' => $paymentStats->total_amount ?? 0,
            'cashamt' => $paymentStats->cash_amount ?? 0,
            'chequeamt' => $paymentStats->cheque_amount ?? 0,
            'tokenamt' => $paymentStats->token_amount ?? 0,
            'discount' => $paymentStats->total_discount ?? 0,
            'discountcash' => $paymentStats->cash_discount ?? 0,
            'discountcheque' => $paymentStats->cheque_discount ?? 0,
            'list' => $sessions,
            'from' => $today,
            'to' => $today,
        ];

        return view('dashboards.visit-reports.daily', $data);
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'end-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'user' => 'nullable|exists:mobileusers,id'
        ]);

        if ($validated['start-date'] > $validated['end-date']) {
            return back()->withInput()->withErrors(['end-date' => 'End date must be after start date']);
        }

        $todayBS = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($validated['start-date'] > $todayBS) {
            return redirect('/visithistory')->with('status', 'Start date must be before or equal to today');
        }

        $from = $this->custom_BS_to_AD($validated['start-date']);
        $to = $this->custom_BS_to_AD($validated['end-date']);
        $selectedUser = $validated['user'] ?? null;

        $isASM = Auth::user()->hasRole('ASM');

        if ($isASM) {
            $adminArea = Auth::user()->area->pluck('areacode');

            $mobileUserIds = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $adminArea)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');

            $users = Mobileuser::whereIn('id', $mobileUserIds)->get();
        } else {
            $users = Mobileuser::all();
            $mobileUserIds = $users->pluck('id')->toArray();
        }

        $sessionQuery = Session::whereBetween('date', [$from, $to]);

        if ($selectedUser) {
            $sessionQuery->where('user_id', $selectedUser);
        } elseif ($isASM) {
            $sessionQuery->whereIn('user_id', $mobileUserIds);
        }

        $sessions = $sessionQuery->groupBy('user_id')
            ->select('visits.*', DB::raw('count(*) as visit_count'))
            ->get();

        $paymentQuery = Payment::whereBetween('collection_date', [$from, $to]);

        if ($selectedUser) {
            $paymentQuery->where('user_id', $selectedUser);
        } elseif ($isASM) {
            $paymentQuery->whereIn('user_id', $mobileUserIds);
        }

        $baseQuery = clone $paymentQuery;
        $paymentStats = [
            'total_amount' => $baseQuery->sum('amount'),
            'total_discount' => $baseQuery->sum('discount'),
        ];

        $cashQuery = clone $paymentQuery;
        $paymentStats['cash_amount'] = $cashQuery->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount');
        $paymentStats['cash_discount'] = $cashQuery->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('discount');

        $chequeQuery = clone $paymentQuery;
        $paymentStats['cheque_amount'] = $chequeQuery->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('amount');
        $paymentStats['cheque_discount'] = $chequeQuery->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('discount');

        $tokenQuery = clone $paymentQuery;
        $paymentStats['token_amount'] = $tokenQuery->where('payment_type', 'token')->sum('amount');

        $totalVisitQuery = Session::whereBetween('date', [$from, $to]);

        if ($selectedUser) {
            $totalVisitQuery->where('user_id', $selectedUser);
        } elseif ($isASM) {
            $totalVisitQuery->whereIn('user_id', $mobileUserIds);
        }

        $totalVisit = $totalVisitQuery->count();

        $userIds = $sessions->pluck('user_id')->unique()->toArray();

        $purposes = VisitPurpose::whereBetween('date', [$from, $to])
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id', 'purpose_id')
            ->select('user_id', 'purpose_id', DB::raw('count(*) as visit_count'))
            ->get()
            ->groupBy('user_id');

        $collections = Payment::whereBetween('collection_date', [$from, $to])
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->select('user_id', DB::raw('SUM(amount) as total_collection'))
            ->get()
            ->keyBy('user_id')
            ->map(function ($item) {
                return $item->total_collection;
            });

        foreach ($sessions as $visit) {
            $userId = $visit->user_id;
            $visit->purposes = $purposes->get($userId, collect());
            $visit->collection = $collections->get($userId, 0);
        }

        $userName = '';
        $userId = '';
        if ($selectedUser) {
            $userInfo = Mobileuser::select('name', 'id')->where('id', $selectedUser)->first();
            $userName = $userInfo->name ?? '';
            $userId = $userInfo->id ?? '';
        }

        $data = [
            'users' => $users,
            's' => $totalVisit,
            'amt' => $paymentStats['total_amount'],
            'cashamt' => $paymentStats['cash_amount'],
            'chequeamt' => $paymentStats['cheque_amount'],
            'tokenamt' => $paymentStats['token_amount'],
            'discount' => $paymentStats['total_discount'],
            'discountcash' => $paymentStats['cash_discount'],
            'discountcheque' => $paymentStats['cheque_discount'],
            'list' => $sessions,
            'from' => $validated['start-date'],
            'to' => $validated['end-date'],
            'user' => $selectedUser,
            'userid' => $selectedUser,
            'name' => $userName,
            'useid' => $userId,
        ];

        return view('dashboards.visit-reports.daily', $data);
    }

    public function show(Request $request, $id)
    {
        $request->validate([
            'type' => 'nullable|in:dealer,retailer',
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $from = null;
        $to = null;

        if ($request->filled(['from', 'to'])) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);

            if ($from > $to) {
                return back()->with('status', 'From date must be smaller or equal than to');
            }
        }

        $isDealer = $request->type === 'dealer';
        $isRetailer = $request->type === 'retailer';

        if ($isDealer) {
            $dealersQuery = Dealer::whereNull('main_dealer_code');
        } else if ($isRetailer) {
            $dealersQuery = Dealer::whereNotNull('main_dealer_code');
        } else {
            $dealersQuery = Dealer::query();
        }

        $dealers = $dealersQuery->get();
        $dealerCodes = $dealers->pluck('code')->toArray();

        $mouser = Mobileuser::findOrFail($id);

        $paymentAggregates = Payment::where('user_id', $id)
            ->whereIn('dealer_code', $dealerCodes)
            ->when($from && $to, function ($query) use ($from, $to) {
                return $query->whereBetween('collection_date', [$from, $to]);
            })
            ->selectRaw('
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(CASE WHEN payment_type IN ("cash (regular)", "cash (against)") THEN amount ELSE 0 END), 0) as cash_amount,
                COALESCE(SUM(CASE WHEN payment_type IN ("cheque (bearer)", "cheque (payee)") THEN amount ELSE 0 END), 0) as cheque_amount,
                COALESCE(SUM(CASE WHEN payment_type = "token" THEN amount ELSE 0 END), 0) as token_amount,
                COALESCE(SUM(CASE WHEN payment_type IN ("cash (regular)", "cash (against)") THEN discount ELSE 0 END), 0) as cash_discount,
                COALESCE(SUM(CASE WHEN payment_type IN ("cheque (bearer)", "cheque (payee)") THEN discount ELSE 0 END), 0) as cheque_discount
            ')
            ->first();

        $sessionQuery = Session::where('user_id', $id)
            ->where(function ($q) use ($dealerCodes, $isRetailer, $isDealer) {
                $q->whereIn('dealer_code', $dealerCodes)
                    ->orWhere(function ($q2) use ($isRetailer, $isDealer) {
                        $q2->whereNull('dealer_code');

                        if ($isRetailer) {
                            $q2->where('is_retailer', true);
                        } elseif ($isDealer) {
                            $q2->where('is_retailer', false);
                        }
                    });
            });

        if ($from && $to) {
            $sessionQuery->whereBetween('date', [$from, $to]);
        }

        $sessions = $sessionQuery->orderBy('date', 'DESC')->get();

        if ($sessions->isNotEmpty()) {
            $sessionDates = $sessions->pluck('date')->unique();

            $locations = Location::where('user_id', $id)
                ->whereIn('day', $sessionDates)
                ->get()
                ->keyBy('day');

            $sessions->each(function ($attendance) use ($locations) {
                $location = $locations[$attendance->date] ?? null;
                $attendance->distance = $this->calculateDistanceFromLocation($location);
            });
        }

        if (request()->expectsJson()) {
            return response()->json([
                'mouser' => $mouser,
                'users' => Mobileuser::pluck('name', 'id'),
                'type' => $request->type,
                's' => 0,
                'amt' => $paymentAggregates->total_amount,
                'cashamt' => $paymentAggregates->cash_amount,
                'chequeamt' => $paymentAggregates->cheque_amount,
                'tokenamt' => $paymentAggregates->token_amount,
                'discount' => $paymentAggregates->total_discount,
                'discountcash' => $paymentAggregates->cash_discount,
                'discountcheque' => $paymentAggregates->cheque_discount,
                'list' => $sessions,
                'from' => $request->from,
                'to' => $request->to,
            ]);
        }


        return view('dashboards.visit-reports.show', [
            'mouser' => $mouser,
            'users' => Mobileuser::pluck('name', 'id'),
            'type' => $request->type,
            's' => 0,
            'amt' => $paymentAggregates->total_amount,
            'cashamt' => $paymentAggregates->cash_amount,
            'chequeamt' => $paymentAggregates->cheque_amount,
            'tokenamt' => $paymentAggregates->token_amount,
            'discount' => $paymentAggregates->total_discount,
            'discountcash' => $paymentAggregates->cash_discount,
            'discountcheque' => $paymentAggregates->cheque_discount,
            'list' => $sessions,
            'from' => $request->from,
            'to' => $request->to,
        ]);
    }

    private function calculateDistanceFromLocation($location)
    {
        if (!$location || empty($location->coordinates)) {
            return 0;
        }

        $coordinates = $location->coordinates;
        $distance = 0;

        for ($i = 1; $i < count($coordinates); $i++) {
            $lat1 = $coordinates[$i - 1]['latitude'];
            $lon1 = $coordinates[$i - 1]['longitude'];
            $lat2 = $coordinates[$i]['latitude'];
            $lon2 = $coordinates[$i]['longitude'];

            $c_d = $this->calculate_distance($lat1, $lon1, $lat2, $lon2, "K");
            $distance += is_nan($c_d) ? 0 : $c_d;
        }

        return $distance;
    }

    public function convertDate($date)
    {
        return $this->custom_AD_to_BS($date);
    }

    private function calculate_distance($lat1, $lon1, $lat2, $lon2, $unit)
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") { //Kilometers
            return ($miles * 1.609344);
        } else if ($unit == "N") { //Nautical Miles
            return ($miles * 0.8684);
        } else if ($unit == "MT") { //Meter
            return ($miles * 1.609344 * 1000);
        } else { //Miles
            return $miles;
        }
    }

    public function getVisitLocationDetails(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:online,offline,unlisted',
        ]);

        $model = Session::with([
            'user:id,name',
            'dealer:code,name,latitude,longitude',
        ])
            ->select(['id', 'user_id', 'dealer_code', 'lat_lng', 'date', 'start_time', 'end_time'])
            ->findOrFail($id);

        $array = explode(',', $model->lat_lng);
        $data['lat'] = $array[0];
        $data['lng'] = $array[1];
        $data['model'] = $model;

        if ($array[0] == '' || $array[1] == '') {
            return back()->with('error', 'Location data not available for this ' . $request->type);
        }
        return view('dashboards.visit-reports.map', $data);
    }
}

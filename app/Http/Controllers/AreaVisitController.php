<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Dealer;
use App\Models\Payment;
use App\Models\Session;
use App\Models\VisitPurpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;

class AreaVisitController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index()
    {
        $todayAD = date('Y-m-d');
        $todayBS = $this->custom_AD_to_BS($todayAD);
        $isASM = Auth::user()->hasRole('ASM');

        if ($isASM) {
            $adminArea = Auth::user()->area->pluck('areacode');
            $areas = Area::whereIn('areacode', $adminArea)->get();
            $areaCodes = $areas->pluck('areacode')->toArray();

            $mobileUserIds = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $areaCodes)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
        } else {
            $areas = Area::all();
            $areaCodes = $areas->pluck('areacode')->toArray();
        }

        $sessionQuery = Session::where('date', $todayAD);
        if ($isASM) {
            $sessionQuery->whereIn('areacode', $areaCodes);
        }
        $sessions = $sessionQuery->groupBy('areacode')
            ->select('visits.*', DB::raw('count(*) as visit_count'))
            ->get();

        $paymentQuery = Payment::where('collection_date', $todayAD);
        if ($isASM) {
            $paymentQuery->whereIn('user_id', $mobileUserIds);
        }

        $baseQuery = clone $paymentQuery;
        $paymentStats = [
            'total_amount' => $baseQuery->sum('amount'),
            'cash_amount' => (clone $paymentQuery)->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount'),
            'cheque_amount' => (clone $paymentQuery)->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('amount'),
            'token_amount' => (clone $paymentQuery)->where('payment_type', 'token')->sum('amount'),
            'total_discount' => $baseQuery->sum('discount'),
            'cash_discount' => (clone $paymentQuery)->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('discount'),
            'cheque_discount' => (clone $paymentQuery)->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('discount'),
        ];

        $sessionAreaCodes = $sessions->pluck('areacode')->unique()->toArray();

        $purposes = VisitPurpose::where('date', $todayAD)
            ->whereIn('areacode', $sessionAreaCodes)
            ->groupBy('areacode', 'purpose_id')
            ->select('areacode', 'purpose_id', DB::raw('count(*) as visit_count'))
            ->get()
            ->groupBy('areacode');

        $collections = Payment::where('collection_date', $todayAD)
            ->whereIn('areacode', $sessionAreaCodes)
            ->groupBy('areacode')
            ->select('areacode', DB::raw('SUM(amount) as total_collection'))
            ->get()
            ->keyBy('areacode')
            ->map(function ($item) {
                return $item->total_collection;
            });

        $dealerCounts = Dealer::whereIn('area_code', $sessionAreaCodes)
            ->groupBy('area_code')
            ->select('area_code', DB::raw('count(*) as dealer_count'))
            ->get()
            ->keyBy('area_code')
            ->map(function ($item) {
                return $item->dealer_count;
            });

        foreach ($sessions as $visit) {
            $areaCode = $visit->areacode;
            $visit->purposes = $purposes->get($areaCode, collect());
            $visit->collection = $collections->get($areaCode, 0);
            $visit->dealer_count = $dealerCounts->get($areaCode, 0);
        }

        $data = [
            'areas' => $areas,
            'list' => $sessions,
            'from' => $todayBS,
            'to' => $todayBS,
            'user' => '',
            'amt' => $paymentStats['total_amount'],
            'cashamt' => $paymentStats['cash_amount'],
            'chequeamt' => $paymentStats['cheque_amount'],
            'tokenamt' => $paymentStats['token_amount'],
            'discount' => $paymentStats['total_discount'],
            'discountcash' => $paymentStats['cash_discount'],
            'discountcheque' => $paymentStats['cheque_discount'],
        ];

        return view('dashboards.areavisit-reports.index', $data);
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'area' => 'nullable|exists:areas,areacode',
            'end-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);

        //nepali date validation
        if ($request['start-date'] > $request['end-date']) {
            return back()->withInput($request->input())->withErrors(['end-date' => 'The end date must be after or equal to the start date.']);
        }

        $startDate = $validated['start-date'];
        $endDate = $validated['end-date'];
        $selectedArea = $validated['area'] ?? null;

        $todayBS = $this->custom_AD_to_BS(date('Y-m-d'));

        if ($startDate > $todayBS) {
            return redirect('/dashboard/visit/areas')->with('status', 'Start date must be before or equal to today');
        }

        $from = $this->custom_BS_to_AD($startDate);
        $to = $this->custom_BS_to_AD($endDate);

        $isASM = Auth::user()->hasRole('ASM');

        if ($isASM) {
            $adminAreas = Auth::user()->area->pluck('areacode');
            $areas = Area::whereIn('areacode', $adminAreas)->get();

            $mobileUserIds = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $adminAreas)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
        } else {
            $areas = Area::all();
            $adminAreas = $areas->pluck('areacode');
        }

        $sessionQuery = Session::whereBetween('date', [$from, $to]);

        if ($selectedArea) {
            $mobileUsers = Area::with('mobileuser')
                ->where('areacode', $selectedArea)
                ->first()
                ->mobileuser
                ->pluck('id');

            $sessionQuery->where('areacode', $selectedArea);
        } elseif ($isASM) {
            $sessionQuery->whereIn('areacode', $adminAreas);
        }

        $sessions = $sessionQuery->groupBy('areacode')
            ->select('visits.*', DB::raw('count(*) as visit_count'))
            ->get();

        $paymentQuery = Payment::whereBetween('collection_date', [$from, $to]);

        if ($selectedArea) {
            $paymentQuery->where('areacode', $selectedArea);
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

        $areaCodes = $sessions->pluck('areacode')->unique()->toArray();

        $purposes = VisitPurpose::whereBetween('date', [$from, $to])
            ->whereIn('areacode', $areaCodes)
            ->groupBy('areacode', 'purpose_id')
            ->select('areacode', 'purpose_id', DB::raw('count(*) as visit_count'))
            ->get()
            ->groupBy('areacode');

        $collections = Payment::whereBetween('collection_date', [$from, $to])
            ->whereIn('areacode', $areaCodes)
            ->groupBy('areacode')
            ->select('areacode', DB::raw('SUM(amount) as total_collection'))
            ->get()
            ->keyBy('areacode')
            ->map(function ($item) {
                return $item->total_collection;
            });

        $dealerCounts = Dealer::whereIn('area_code', $areaCodes)
            ->groupBy('area_code')
            ->select('area_code', DB::raw('count(*) as dealer_count'))
            ->get()
            ->keyBy('area_code')
            ->map(function ($item) {
                return $item->dealer_count;
            });

        foreach ($sessions as $visit) {
            $areaCode = $visit->areacode;
            $visit->purposes = $purposes->get($areaCode, collect());
            $visit->collection = $collections->get($areaCode, 0);
            $visit->dealer_count = $dealerCounts->get($areaCode, 0);
        }

        $areaName = '';
        $areaCode = '';
        if ($selectedArea) {
            $reqArea = Area::where('areacode', $selectedArea)->first();
            $areaName = $reqArea->area ?? '';
            $areaCode = $reqArea->areacode ?? '';
        }

        $data = [
            'areas' => $areas,
            'list' => $sessions,
            'from' => $startDate,
            'to' => $endDate,
            'user' => '',
            'amt' => $paymentStats['total_amount'],
            'cashamt' => $paymentStats['cash_amount'],
            'chequeamt' => $paymentStats['cheque_amount'],
            'tokenamt' => $paymentStats['token_amount'],
            'discount' => $paymentStats['total_discount'],
            'discountcash' => $paymentStats['cash_discount'],
            'discountcheque' => $paymentStats['cheque_discount'],
            'ar' => $areaName,
            'arcode' => $areaCode,
        ];

        return view('dashboards.areavisit-reports.index', $data);
    }

    public function show(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:dealer,retailer',
            'from' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'to' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'after_or_equal:from', 'required_with:from'],
        ]);

        $hasDateRange = !empty($validated['from']) && !empty($validated['to']);

        $from = $hasDateRange ? $this->custom_BS_to_AD($validated['from']) : null;
        $to = $hasDateRange ? $this->custom_BS_to_AD($validated['to']) : null;

        $isUnlisted = $id === "Unlisted_dealer_visit";

        if ($isUnlisted) {
            $area = new \stdClass();
            $area->id = '';
            $area->areacode = 'Unlisted_dealer_visit';
            $area->area = 'Unlisted Area Visit';
        } else {
            $area = Area::findOrFail($id);
        }

        $isDealer = strtolower($request->input('type')) === 'dealer';
        $isRetailer = strtolower($request->input('type')) === 'retailer';

        $dealerQuery = Dealer::where('area_code', $id);

        if ($isDealer) {
            $dealerQuery->whereNull('main_dealer_code');
        } elseif ($isRetailer) {
            $dealerQuery->whereNotNull('main_dealer_code');
        }

        $dealerCodes = $dealerQuery->pluck('code');

        $sessionQuery = Session::query();

        if ($isUnlisted) {
            $sessionQuery->whereNull('areacode');
        } else {
            $sessionQuery->where('areacode', $id)
                ->whereIn('dealer_code', $dealerCodes);
        }

        if ($isDealer) {
            $sessionQuery->where('is_retailer', false);
        } elseif ($isRetailer) {
            $sessionQuery->where('is_retailer', true);
        }

        if ($hasDateRange) {
            $sessionQuery->whereBetween('date', [$from, $to]);
        }

        $sessions = $sessionQuery->get();

        $paymentStats = [
            'total_amount' => 0,
            'cash_amount' => 0,
            'cheque_amount' => 0,
            'token_amount' => 0,
            'total_discount' => 0,
            'cash_discount' => 0,
            'cheque_discount' => 0,
        ];

        if (!$isUnlisted) {
            $paymentQuery = Payment::where('areacode', $id);

            if ($hasDateRange) {
                $paymentQuery->whereBetween('collection_date', [$from, $to]);
            }

            $baseQuery = clone $paymentQuery;

            $paymentStats = [
                'total_amount' => $baseQuery->sum('amount'),
                'cash_amount' => (clone $paymentQuery)->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount'),
                'cheque_amount' => (clone $paymentQuery)->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('amount'),
                'token_amount' => (clone $paymentQuery)->where('payment_type', 'token')->sum('amount'),
                'total_discount' => $baseQuery->sum('discount'),
                'cash_discount' => (clone $paymentQuery)->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('discount'),
                'cheque_discount' => (clone $paymentQuery)->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('discount'),
            ];
        }

        $dealerCodes = $sessions->pluck('dealer_code')->unique()->filter()->values();

        if (!$isUnlisted && $dealerCodes->isNotEmpty()) {
            $purposeQuery = VisitPurpose::whereIn('dealer_code', $dealerCodes);

            if ($hasDateRange) {
                $purposeQuery->whereBetween('date', [$from, $to]);
            } else {
                $purposeQuery->where('areacode', $id);
            }

            $purposes = $purposeQuery->groupBy('dealer_code', 'purpose_id')
                ->select('dealer_code', 'purpose_id', DB::raw('count(*) as visit_count'))
                ->get()
                ->groupBy('dealer_code');
        } else {
            $purposes = collect();
        }

        if (!$isUnlisted && $dealerCodes->isNotEmpty()) {
            $collectionQuery = Payment::whereIn('dealer_code', $dealerCodes);

            if ($hasDateRange) {
                $collectionQuery->whereBetween('collection_date', [$from, $to]);
            } else {
                $collectionQuery->where('areacode', $id);
            }

            $collections = $collectionQuery->groupBy('dealer_code')
                ->select('dealer_code', DB::raw('SUM(amount) as total_collection'))
                ->get()
                ->keyBy('dealer_code')
                ->map(function ($item) {
                    return $item->total_collection;
                });
        } else {
            $collections = collect();
        }

        foreach ($sessions as $visit) {
            if ($isUnlisted) {
                $visit->purposes = collect();
                $visit->collection = 0;
            } else {
                $visit->purposes = $purposes->get($visit->dealer_code, collect());
                $visit->collection = $collections->get($visit->dealer_code, 0);
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'areas' => $area,
                'list' => $sessions,
                'type' => $request->input('type'),
                'from' => $hasDateRange ? $validated['from'] : '',
                'to' => $hasDateRange ? $validated['to'] : '',
                'amt' => $paymentStats['total_amount'],
                'cashamt' => $paymentStats['cash_amount'],
                'chequeamt' => $paymentStats['cheque_amount'],
                'tokenamt' => $paymentStats['token_amount'],
                'discount' => $paymentStats['total_discount'],
                'discountcash' => $paymentStats['cash_discount'],
                'discountcheque' => $paymentStats['cheque_discount'],
            ]);
        }

        $data = [
            'areas' => $area,
            'list' => $sessions,
            'type' => $request->input('type'),
            'from' => $hasDateRange ? $validated['from'] : '',
            'to' => $hasDateRange ? $validated['to'] : '',
            'amt' => $paymentStats['total_amount'],
            'cashamt' => $paymentStats['cash_amount'],
            'chequeamt' => $paymentStats['cheque_amount'],
            'tokenamt' => $paymentStats['token_amount'],
            'discount' => $paymentStats['total_discount'],
            'discountcash' => $paymentStats['cash_discount'],
            'discountcheque' => $paymentStats['cheque_discount'],
        ];

        return view('dashboards.areavisit-reports.show', $data);
    }
}

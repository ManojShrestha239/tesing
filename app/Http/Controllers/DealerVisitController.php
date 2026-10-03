<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Payment;
use App\Models\Session;
use App\Models\VisitPurpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;

class DealerVisitController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index()
    {
        $date = date('Y-m-d');
        $today = $this->custom_AD_to_BS($date);

        $paymentQuery = Payment::where('collection_date', $date);
        $sessionQuery = Session::where('date', $date);

        if (Auth::user()->hasRole('ASM')) {
            $adminAreaCodes = Auth::user()->area->pluck('areacode');
            $dealers = Dealer::whereIn('area_code', $adminAreaCodes)->get();
            $dealerCodes = $dealers->pluck('code');

            $paymentQuery->whereIn('dealer_code', $dealerCodes);
            $sessionQuery->whereIn('dealer_code', $dealerCodes);
        } else {
            $dealers = Dealer::all();
            $dealerCodes = $dealers->pluck('code');
        }

        $payments = $paymentQuery->get();

        $amt = $payments->sum('amount');

        $cashTypes = ['cash (regular)', 'cash (against)'];
        $chequeTypes = ['cheque (bearer)', 'cheque (payee)'];

        $cashPayments = $payments->whereIn('payment_type', $cashTypes);
        $chequePayments = $payments->whereIn('payment_type', $chequeTypes);
        $tokenPayments = $payments->where('payment_type', 'token');

        $cashamt = $cashPayments->sum('amount');
        $chequeamt = $chequePayments->sum('amount');
        $tokenamt = $tokenPayments->sum('amount');

        $discount = $payments->sum('discount');
        $cashdiscount = $cashPayments->sum('discount');
        $chequediscount = $chequePayments->sum('discount');

        $session = $sessionQuery->groupBy('dealer_code')
            ->select('dealer_code', DB::raw('count(*) as visit_count'))
            ->get();

        $dealerCodesFromSessions = $session->pluck('dealer_code');

        $purposesByDealer = VisitPurpose::where('date', $date)
            ->whereIn('dealer_code', $dealerCodesFromSessions)
            ->groupBy('dealer_code', 'purpose_id')
            ->select('dealer_code', 'purpose_id', DB::raw('count(*) as visit_count'))
            ->get()
            ->groupBy('dealer_code');

        $collectionsByDealer = $payments->whereIn('dealer_code', $dealerCodesFromSessions)
            ->groupBy('dealer_code')
            ->map(function ($payments) {
                return $payments->sum('amount');
            });

        foreach ($session as $visit) {
            $visit->purposes = $purposesByDealer->get($visit->dealer_code, collect());
            $visit->collection = $collectionsByDealer->get($visit->dealer_code, 0);
        }

        return view('dashboards.dealervisit-reports.index', [
            'dealers' => $dealers,
            'de' => '',
            'list' => $session,
            'from' => $today,
            'to' => $today,
            'amt' => $amt,
            'cashamt' => $cashamt,
            'chequeamt' => $chequeamt,
            'tokenamt' => $tokenamt,
            'discount' => $discount,
            'discountcash' => $cashdiscount,
            'discountcheque' => $chequediscount,
        ]);
    }

    public function filter(Request $request)
    {
        $validated = $this->validate($request, [
            'type' => 'nullable|in:dealer,retailer',
            'dealer' => 'nullable|exists:dealers,code',
            'end-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'start-date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);

        $startDate = $validated['start-date'];
        $endDate = $validated['end-date'];
        $selectedDealer = $validated['dealer'] ?? null;

        $isDealer = strtolower($validated['type']) === 'dealer';
        $isRetailer = strtolower($validated['type']) === 'retailer';

        if ($startDate > $endDate) {
            return back()->withInput()->withErrors(['end-date' => 'End date must be after start date']);
        }

        $todayBS = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($startDate > $todayBS) {
            return redirect('/dashboard/visithistory-dealer')->with('status', 'Start date must be before or equal to today');
        }

        $from = $this->custom_BS_to_AD($startDate);
        $to = $this->custom_BS_to_AD($endDate);

        if ($isDealer) {
            $dealerQuery = Dealer::whereNull('main_dealer_code');
        } elseif ($isRetailer) {
            $dealerQuery = Dealer::whereNotNull('main_dealer_code');
        } else {
            $dealerQuery = Dealer::query();
        }

        if (Auth::user()->hasRole('ASM')) {
            $adminArea = Auth::user()->area->pluck('areacode');
            $dealerQuery->whereIn('area_code', $adminArea);
        }

        $dealers = $dealerQuery->get();
        $dealerCodes = $dealers->pluck('code')->toArray();

        $dealerName = '';
        if ($selectedDealer) {
            $dealerInfo = $dealers->where('code', $selectedDealer)->first();
            $dealerName = $dealerInfo->name ?? '';
        }

        $sessions = $this->getVisitSessions($selectedDealer, $dealerCodes, $from, $to);

        $paymentStats = $this->getPaymentStatistics($selectedDealer, $dealerCodes, $from, $to);

        $sessionDealerCodes = $sessions->pluck('dealer_code')->unique()->toArray();
        $purposes = $this->getVisitPurposes($sessionDealerCodes, $from, $to);

        $sessions->each(function ($session) use ($purposes, $from, $to) {
            $session->purposes = $purposes->get($session->dealer_code, collect());
            $session->collection = Payment::where('dealer_code', $session->dealer_code)
                ->whereBetween('collection_date', [$from, $to])
                ->sum('amount');
        });

        $data = [
            'dealers' => $dealers,
            'de' => $dealerName,
            'request' => $request,
            'list' => $sessions,
            'from' => $startDate,
            'to' => $endDate,
            'dealerid' => $selectedDealer,
            'amt' => $paymentStats['total_amount'],
            'cashamt' => $paymentStats['cash_amount'],
            'chequeamt' => $paymentStats['cheque_amount'],
            'tokenamt' => $paymentStats['token_amount'],
            'discount' => $paymentStats['total_discount'],
            'discountcash' => $paymentStats['cash_discount'],
            'discountcheque' => $paymentStats['cheque_discount'],
        ];

        return view('dashboards.dealervisit-reports.index', $data);
    }

    private function getVisitSessions($selectedDealer, $dealerCodes, $from, $to)
    {
        $query = Session::whereNotNull('dealer_code')
            ->whereBetween('visits.date', [$from, $to]);

        if ($selectedDealer) {
            $query->where('dealer_code', $selectedDealer);
        } else {
            $query->whereIn('dealer_code', $dealerCodes);
        }

        return $query->select('visits.*', DB::raw('count(*) as visit_count'))
            ->groupBy('dealer_code')
            ->get();
    }

    private function getPaymentStatistics($selectedDealer, $dealerCodes, $from, $to)
    {
        $query = Payment::whereBetween('collection_date', [$from, $to]);

        if ($selectedDealer) {
            $query->where('dealer_code', $selectedDealer);
        } else {
            $query->whereIn('dealer_code', $dealerCodes);
        }

        return [
            'total_amount' => $query->sum('amount'),
            'cash_amount' => (clone $query)->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount'),
            'cheque_amount' => (clone $query)->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('amount'),
            'token_amount' => (clone $query)->where('payment_type', 'token')->sum('amount'),
            'total_discount' => $query->sum('discount'),
            'cash_discount' => (clone $query)->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('discount'),
            'cheque_discount' => (clone $query)->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('discount'),
        ];
    }

    private function getVisitPurposes($dealerCodes, $from, $to)
    {
        if (empty($dealerCodes)) {
            return collect();
        }

        return VisitPurpose::whereIn('dealer_code', $dealerCodes)
            ->whereBetween('date', [$from, $to])
            ->groupBy('dealer_code', 'purpose_id')
            ->select('dealer_code', 'purpose_id', DB::raw('count(*) as visit_count'))
            ->get()
            ->groupBy('dealer_code');
    }

    // public function show($id, Request $request)
    // {
    //     $this->validate($request, [
    //         'from' =>  ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'], //'nullable|date_format:Y-m-d|after_or_equal:start-date',
    //         'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
    //     ]);

    //     if ($request['from'] > $request['to']) {
    //         return back()->withInput($request->input())->withErrors(['to' => 'end date must be after or equal with start date']);
    //     }
    //     if ($id == "Unlisted_dealer_visit") {
    //         $dealer = new \stdClass();

    //         $dealer->code = "Unlisted_dealer_visit";
    //         $dealer->name = "Unlisted_dealer_visit";
    //     } else {
    //         $dealer = Dealer::findorfail($id);
    //     }
    //     // die();
    //     $amt = 0;
    //     $cashamt = 0;
    //     $chequeamt = 0;

    //     $discount = 0;
    //     $cashdiscount = 0;
    //     $chequediscount = 0;

    //     if ($request->from && $request->to) {
    //         $from = $this->custom_BS_to_AD($request->from);
    //         $to = $this->custom_BS_to_AD($request->to);
    //         if ($id == "Unlisted_dealer_visit") {
    //             $session = Session::whereNull('dealer_code')->whereBetween('date', [$from, $to])->get();
    //         } else {
    //             // $dealers = Dealer::all();
    //             $amt = Payment::whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('amount');
    //             $cashamt = Payment::whereIn('payment_type', ['cash (regular)', 'cash (against)'])->whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('amount');
    //             $chequeamt = Payment::whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('amount');
    //             $tokenamt = Payment::where('payment_type', 'token')->whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('amount');


    //             $discount = Payment::whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('discount');
    //             $cashdiscount = Payment::whereIn('payment_type', ['cash (regular)', 'cash (against)'])->whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('discount');
    //             $chequediscount = Payment::whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->whereBetween('collection_date', [$from, $to])->where('dealer_code', $id)->sum('discount');

    //             $session = Session::where('dealer_code', $id)->whereBetween('date', [$from, $to])->get();
    //         }
    //         foreach ($session as $visit) {
    //             $visit->purposes = VisitPurpose::where('dealer_code', $visit->dealer_code)->whereBetween('date', [$from, $to])->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))->get();
    //             $visit->collection = Payment::where('dealer_code', $visit->dealer_code)->whereBetween('collection_date', [$from, $to])->sum('amount');
    //         }
    //     } else {
    //         $from = "";
    //         $to = "";
    //         if ($id == "Unlisted_dealer_visit") {
    //             $session = Session::whereNull('dealer_code')->get();
    //         } else {
    //             $amt = Payment::where('dealer_code', $id)->sum('amount');
    //             $cashamt = Payment::whereIn('payment_type', ['cash (regular)', 'cash (against)'])->where('dealer_code', $id)->sum('amount');
    //             $chequeamt = Payment::whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->where('dealer_code', $id)->sum('amount');
    //             $tokenamt = Payment::where('payment_type', 'token')->where('dealer_code', $id)->sum('amount');

    //             $discount = Payment::where('dealer_code', $id)->sum('discount');
    //             $cashdiscount = Payment::whereIn('payment_type', ['cash (regular)', 'cash (against)'])->where('dealer_code', $id)->sum('discount');
    //             $chequediscount = Payment::whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->where('dealer_code', $id)->sum('discount');

    //             $session = Session::where('dealer_code', $id)->get();
    //         }
    //         foreach ($session as $visit) {
    //             $visit->purposes = VisitPurpose::where('dealer_code', $visit->dealer_code)->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))->get();
    //             $visit->collection = Payment::where('dealer_code', $visit->dealer_code)->sum('amount');
    //         }
    //     }

    //     $data['dealer'] = $dealer;
    //     $data['de'] = "";
    //     $data['list'] = $session;
    //     $data['from'] = $request->from;
    //     $data['to'] = $request->to;
    //     $data['amt'] = $amt;
    //     $data['cashamt'] = $cashamt;
    //     $data['chequeamt'] = $chequeamt;
    //     $data['tokenamt'] = $tokenamt;
    //     $data['discount'] = $discount;
    //     $data['discountcash'] = $cashdiscount;
    //     $data['discountcheque'] = $chequediscount;
    //     return view('dashboards.dealervisit-reports.show', $data);
    // }

    public function show($id, Request $request)
    {
        $validated = $this->validate($request, [
            'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);

        if (!empty($validated['from']) && !empty($validated['to']) && $validated['from'] > $validated['to']) {
            return back()->withInput()->withErrors(['to' => 'End date must be after or equal to start date']);
        }

        $isUnlisted = $id === "Unlisted_dealer_visit";
        if ($isUnlisted) {
            $dealer = new \stdClass();
            $dealer->code = "Unlisted_dealer_visit";
            $dealer->name = "Unlisted_dealer_visit";
            $dealerCode = null;
        } else {
            $dealer = Dealer::findOrFail($id);
            $dealerCode = $dealer->code;
        }

        $hasDateRange = !empty($validated['from']) && !empty($validated['to']);

        if ($hasDateRange) {
            $fromAD = $this->custom_BS_to_AD($validated['from']);
            $toAD = $this->custom_BS_to_AD($validated['to']);
        }

        $paymentStats = [
            'total_amount' => 0,
            'cash_amount' => 0,
            'cheque_amount' => 0,
            'token_amount' => 0,
            'total_discount' => 0,
            'cash_discount' => 0,
            'cheque_discount' => 0,
        ];

        if ($isUnlisted) {
            $sessionQuery = Session::whereNull('dealer_code');
            if ($hasDateRange) {
                $sessionQuery->whereBetween('date', [$fromAD, $toAD]);
            }
            $sessions = $sessionQuery->get();
        } else {
            $paymentQuery = Payment::where('dealer_code', $dealerCode);
            if ($hasDateRange) {
                $paymentQuery->whereBetween('collection_date', [$fromAD, $toAD]);
            }

            $paymentStats['total_amount'] = $paymentQuery->sum('amount');
            $paymentStats['total_discount'] = $paymentQuery->sum('discount');

            $cashQuery = clone $paymentQuery;
            $paymentStats['cash_amount'] = $cashQuery->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('amount');
            $paymentStats['cash_discount'] = $cashQuery->whereIn('payment_type', ['cash (regular)', 'cash (against)'])->sum('discount');

            $chequeQuery = clone $paymentQuery;
            $paymentStats['cheque_amount'] = $chequeQuery->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('amount');
            $paymentStats['cheque_discount'] = $chequeQuery->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)'])->sum('discount');

            $tokenQuery = clone $paymentQuery;
            $paymentStats['token_amount'] = $tokenQuery->where('payment_type', 'token')->sum('amount');

            $sessionQuery = Session::where('dealer_code', $dealerCode);
            if ($hasDateRange) {
                $sessionQuery->whereBetween('date', [$fromAD, $toAD]);
            }
            $sessions = $sessionQuery->get();
        }

        $dealerCodes = $sessions->pluck('dealer_code')->unique()->filter()->values();

        if ($dealerCodes->isNotEmpty()) {
            $purposeQuery = VisitPurpose::whereIn('dealer_code', $dealerCodes);
            if ($hasDateRange) {
                $purposeQuery->whereBetween('date', [$fromAD, $toAD]);
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
                $collectionQuery->whereBetween('collection_date', [$fromAD, $toAD]);
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
            $visit->purposes = $purposes->get($visit->dealer_code, collect());
            $visit->collection = $collections->get($visit->dealer_code, 0);
        }

        $data = [
            'dealer' => $dealer,
            'de' => "",
            'list' => $sessions,
            'from' => $validated['from'] ?? '',
            'to' => $validated['to'] ?? '',
            'amt' => $paymentStats['total_amount'],
            'cashamt' => $paymentStats['cash_amount'],
            'chequeamt' => $paymentStats['cheque_amount'],
            'tokenamt' => $paymentStats['token_amount'],
            'discount' => $paymentStats['total_discount'],
            'discountcash' => $paymentStats['cash_discount'],
            'discountcheque' => $paymentStats['cheque_discount'],
        ];

        return view('dashboards.dealervisit-reports.show', $data);
    }
}

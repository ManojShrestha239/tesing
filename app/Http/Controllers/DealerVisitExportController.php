<?php

namespace App\Http\Controllers;

use App\Exports\DealerVisitDetailedReport;
use App\Exports\DealervisitReport;
use App\Models\Dealer;
use App\Models\Payment;
use App\Models\Session;
use App\Models\VisitPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;

class DealerVisitExportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function dealer_visit_report(Request $request)
    {
        if ($request->dealer == "Unlisted_dealer_visit") {
            $valid = Validator::make($request->all(), [
                'dealer' => 'required',
                'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
                'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
            ]);
        } else {
            $valid = Validator::make($request->all(), [
                'dealer' => 'nullable|exists:dealers,code',
                'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
                'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
            ]);
        }
        if ($valid->fails()) {
            // return response()->json(['status' => 'success', 'message' => '']);
            return redirect('/dashboard/areavisit')->withErrors($valid)->withInput();
        }
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $dealer = Dealer::whereIn('area_code', $admin_area);
            $dealer_code = clone $dealer->pluck('code');
        } else {
            $dealer_code = Dealer::pluck('code');
        }
        if ($request->from && $request->to) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
        } else {
            $from = '';
            $to = '';
        }
        if ($from && $request->dealer) {
            if ($request->dealer == "Unlisted_dealer_visit") {
                $session = Session::whereNull('dealer_code')
                    ->whereBetween('visits.date', [$from, $to])
                    ->select('visits.*', DB::raw('count(*) as visit_count'))
                    ->groupBy('dealer_code')
                    ->get();
            } else {
                $session = Session::where(['dealer_code' => $request->dealer])
                    ->whereBetween('visits.date', [$from, $to])
                    ->select('visits.*', DB::raw('count(*) as visit_count'))
                    ->groupBy('dealer_code')
                    ->get();
            }
        } else if (!$from && $request->dealer) {
            if ($request->dealer == "Unlisted_dealer_visit") {
                $session = Session::whereNull('dealer_code')
                    ->select('visits.*', DB::raw('count(*) as visit_count'))
                    ->groupBy('dealer_code')
                    ->get();
            } else {
                $session = Session::where(['dealer_code' => $request->dealer])
                    ->select('visits.*', DB::raw('count(*) as visit_count'))
                    ->groupBy('dealer_code')
                    ->get();
            }
        } else if ($from && !$request->dealer) {
            $session = Session::whereBetween('visits.date', [$from, $to])
                ->whereIn('dealer_code', $dealer_code)
                ->orWhereNull('dealer_code')
                ->select('visits.*', DB::raw('count(*) as visit_count'))
                ->groupBy('dealer_code')
                ->get();
        } else {
            $session = Session::select('visits.*', DB::raw('count(*) as visit_count'))
                ->whereIn('dealer_code', $dealer_code)
                ->orWhereNull('dealer_code')
                ->groupBy('dealer_code')
                ->get();
        }
        if ($from) {
            foreach ($session as $visit) {
                $visit->purposes = VisitPurpose::where('dealer_code', $visit->dealer_code)
                    ->whereBetween('date', [$from, $to])
                    ->groupBy('purpose_id')
                    ->select('purpose_id', DB::raw('count(*)as visit_count'))
                    ->get();

                $visit->collection = Payment::where('dealer_code', $visit->dealer_code)->sum('amount');
            }
        } else {
            foreach ($session as $visit) {
                $visit->purposes = VisitPurpose::where('dealer_code', $visit->dealer_code)
                    ->groupBy('purpose_id')
                    ->select('purpose_id', DB::raw('count(*)as visit_count'))
                    ->get();

                $visit->collection = Payment::where('dealer_code', $visit->dealer_code)->sum('amount');
            }
        }
        if ($request->dealer == "Unlisted_dealer_visit") {
            $dealer = new \stdClass();

            $dealer->code = "Unlisted";
            $dealer->name = "Unlisted Dealer";
        } else {
            if ($request->dealer) {
                $dealer = Dealer::findorFail($request->dealer);
            } else {
                $dealer = '';
            }
        }
        // return $session;
        return Excel::download(new DealervisitReport($session, $dealer, $request->from, $request->to), 'Dealer visit report.xlsx');
    }
    public function dealer_visit_detailed_report(Request $request)
    {
        // return $request->all();
        if ($request->dealer == "Unlisted_dealer_visit") {
            $valid = Validator::make($request->all(), [
                'dealer' => 'required',
                'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
                'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
            ]);
        } else {
            $valid = Validator::make($request->all(), [
                'dealer' => 'required|exists:dealers,code',
                'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
                'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
            ]);
        }
        if ($valid->fails()) {
            // return response()->json(['status' => 'success', 'message' => '']);
            return redirect('/dashboard/areavisit')->withErrors($valid)->withInput();
        }
        if ($request->dealer == "Unlisted_dealer_visit") {
            $dealer = new \stdClass();

            $dealer->code = "Unlisted_dealer_visit";
            $dealer->name = "Unlisted_dealer_visit";
        } else {
            $dealer = Dealer::findorfail($request->dealer);
        }
        if ($request->from && $request->to) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
            if ($request->dealer == "Unlisted_dealer_visit") {
                $session = Session::whereNull('dealer_code')->whereBetween('date', [$from, $to])->get();
            } else {
                $session = Session::where('dealer_code', $request->dealer)->whereBetween('date', [$from, $to])->get();
            }
            foreach ($session as $visit) {
                $visit->purposes = VisitPurpose::where('dealer_code', $visit->dealer_code)->whereBetween('date', [$from, $to])->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))->get();

                $visit->collection = Payment::where('dealer_code', $visit->dealer_code)->whereBetween('collection_date', [$from, $to])->sum('amount');
            }
        } else {
            if ($request->dealer == "Unlisted_dealer_visit") {
                $session = Session::whereNull('dealer_code')->get();
            } else {
                $session = Session::where('dealer_code', $request->dealer)->get();
            }
            foreach ($session as $visit) {
                $visit->purposes = VisitPurpose::where('dealer_code', $visit->dealer_code)->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))->get();

                $visit->collection = Payment::where('dealer_code', $visit->dealer_code)->sum('amount');
            }
        }

        return Excel::download(new DealerVisitDetailedReport($session, $dealer, $request->from, $request->to), "Report of $dealer->code _$request->from _$request->to _report.xlsx");
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\AreaVisitDetailedReport;
use App\Exports\AreaVisitReport;
use App\Models\Area;
use App\Models\Dealer;
use App\Models\Mobileuser;
use App\Models\Payment;
use App\Models\Session;
use App\Models\VisitPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AreaVisitExportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function area_visit_Report(Request $request)
    {
        // return $request->all();
        $valid = Validator::make($request->all(), [
            'area' => 'nullable|exists:areas,areacode',
            'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);
        if ($valid->fails()) {
            // return response()->json(['status' => 'success', 'message' => '']);
            return redirect('/visitarea')->withErrors($valid)->withInput();
        }
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $areas = Area::whereIn('areacode', $admin_area)->get();

            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
            ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
            ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
        } else {
            $mobileuser = Mobileuser::pluck('id');
        }
        if ($request->from && $request->to) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
        } else {
            $from = '';
            $to = '';
        }
        if (!$from && !$request->area) {
            $session = Session::whereIn('user_id', $mobileuser)
                ->groupBy('areacode')
                ->select('visits.*', DB::raw('count(*)as visit_count'))
                ->get();
        } else if ($from && !$request->area) {
            $session = Session::whereIn('user_id', $mobileuser)
                ->groupBy('areacode')
                ->whereBetween('date', [$from, $to])
                ->select('visits.*', DB::raw('count(*)as visit_count'))
                ->get();
        } else if (!$from && $request->area) {
            $session = Session::groupBy('areacode')
            ->where('areacode', $request->area)
                ->select('visits.*', DB::raw('count(*)as visit_count'))
                ->get();
        } else {
            $session = Session::groupBy('areacode')
            ->where('areacode', $request->area)
                ->whereBetween('date', [$from, $to])
                ->select('visits.*', DB::raw('count(*)as visit_count'))
                ->get();
        }
        // return $session;
        foreach ($session as $visit) {
            if ($request->from) {
                $visit->purposes = VisitPurpose::where('areacode', $visit->areacode)
                    ->whereBetween('date', [$from, $to])
                    ->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))
                    ->get();

                $visit->collection = Payment::where('areacode', $visit->areacode)->whereBetween('collection_date', [$from, $to])->sum('amount');
                $visit->dealer_count = Dealer::where('area_code', $visit->areacode)->count();
            } else {
                $visit->purposes = VisitPurpose::where('areacode', $visit->areacode)
                    ->groupBy('purpose_id')
                    ->select('purpose_id', DB::raw('count(*)as visit_count'))
                    ->get();

                $visit->collection = Payment::where('areacode', $visit->areacode)->sum('amount');
                $visit->dealer_count = Dealer::where('area_code', $visit->areacode)->count();
            }
        }
        if ($request->area) {
            $area = Area::findorFail($request->area);
        } else {
            $area = "";
        }
        // return $session;
        return Excel::download(new AreaVisitReport($session, $area, $request->from, $request->to), 'Area visit report.xlsx');
    }
    public function Area_visit_Detailed_Report(Request $request)
    {
        // return $request->all();
        if ($request->area == "Unlisted_dealer_visit") {
            $valid = Validator::make($request->all(), [
                'area' => 'required',
                'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
                'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
            ]);
        } else {
            $valid = Validator::make($request->all(), [
                'area' => 'required|exists:areas,areacode',
                'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
                'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
            ]);
        }
        if ($valid->fails()) {
            // return response()->json(['status' => 'success', 'message' => '']);
            return redirect('/dashboard/areavisit')->withErrors($valid)->withInput();
        }

        if ($request->from && $request->to) {
            $from = $request->from;
            $to = $request->to;
        } else {
            $from = '';
            $to = '';
        }
        if ($request->area == "Unlisted_dealer_visit") {
            $area = new \stdClass();

            $area->id = '';
            $area->areacode = 'Unlisted_dealer_visit';
            $area->area = 'Unlisted Area Visit';
        } else {
            $area = Area::findorFail($request->area);
        }
        if (!$from) {
            if ($request->area == "Unlisted_dealer_visit") {
                $session = Session::select('visits.*')
                ->whereNull('areacode')
                ->get();
            } else {
                $session = Session::where('areacode', $request->area)
                    ->select('visits.*')
                    ->get();
            }
        } else {
            if ($request->area == "Unlisted_dealer_visit") {
                $session = Session::whereBetween('date', [$from, $to])
                    ->whereNull('areacode')
                    ->select('visits.*')
                    ->get();
            } else {
                $session = Session::where('areacode', $request->area)
                    ->whereBetween('date', [$from, $to])
                    ->select('visits.*')
                    ->get();
            }
        }
        // return $session;
        foreach ($session as $visit) {
            if ($from) {
                $visit->purposes = VisitPurpose::where('areacode', $visit->areacode)
                    ->whereBetween('date', [$from, $to])
                    ->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))
                    ->get();

                $visit->collection = Payment::where('areacode', $visit->areacode)->whereBetween('collection_date', [$from, $to])->sum('amount');
                $visit->dealer_count = Dealer::where('area_code', $visit->areacode)->count();
            } else {
                $visit->purposes = VisitPurpose::where('areacode', $visit->areacode)
                    ->groupBy('purpose_id')
                    ->select('purpose_id', DB::raw('count(*)as visit_count'))
                    ->get();

                $visit->collection = Payment::where('areacode', $visit->areacode)->sum('amount');
                $visit->dealer_count = Dealer::where('area_code', $visit->areacode)->count();
            }
        }
        // return $session;
        return Excel::download(new AreaVisitDetailedReport($session, $area, $request->from, $request->to), "Area visit of $area->areacode _$from _$to _report.xlsx");
    }
}

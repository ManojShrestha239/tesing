<?php

namespace App\Http\Controllers;

use App\Exports\UnlistedDetailedReport;
use App\Exports\UnlistedReport;
use App\Models\Area;
use App\Models\Mobileuser;
use App\Models\Session;
use App\Models\VisitPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UnlistedExportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function unlisted_visit_Report(Request $request)
    {
        // return $request->all();
        $valid = Validator::make($request->all(), [
            // 'user' => 'nullable|exists:mobileusers,id',
            'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);
        if ($valid->fails()) {
            return redirect('/dashboard/visithistory')->withInput()->withErrors($valid);
        }
        if (!($request->from || $request->to)) {
            $from = '';
            $to = '';
        } else {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
        }
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            // $users = Mobileuser::whereIn('id', $mobileuser)->get();
        } else {
            $mobileuser = Mobileuser::pluck('id');
        }
        // return $mobileuser;
        if ($from) {
            // return $from;
            // $session = Session::groupBy('user_id')->whereBetween('date', [$from, $to])->orderBy('created_at', 'DESC')->get();
            $session = Session::whereIn('user_id', $mobileuser)
                ->where('status', 'Unlisted')
                ->whereBetween('date', [$from, $to])
                ->select('visits.*', DB::raw('count(*)as visit_count'))
                ->groupBy('user_id')
                ->orderBy('created_at', 'DESC')
                ->get();
        }
        // else if ($request->user && $from) {
        //     $session = Session::groupBy('user_id')->where('user_id', $request->user)->where('status','Unlisted')->whereBetween('date', [$from, $to])->select('visits.*', DB::raw('count(*)as visit_count'))->orderBy('created_at', 'DESC')->get();
        // } else if ($request->user && !$from) {
        //     $session = Session::groupBy('user_id')->where('user_id', $request->user)->where('status','Unlisted')->select('visits.*', DB::raw('count(*)as visit_count'))->orderBy('created_at', 'DESC')->get();
        // }
        else {
            $session = Session::whereIn('user_id', $mobileuser)
                ->where('status', 'Unlisted')
                ->select('visits.*', DB::raw('count(*)as visit_count'))
                ->groupBy('user_id')
                ->orderBy('created_at', 'DESC')
                ->get();
        }
        foreach ($session as $visit) {
            // $visit->visit_count = VisitPurpose::where('user_id', $visit->user_id)->count();
            $visit->purposes = VisitPurpose::where('user_id', $visit->user_id)->where('status', 'Unlisted')->groupBy('purpose_id')->select('purpose_id', DB::raw('count(*)as visit_count'))->get();

            // $visit->collection = Payment::where('user_id', $visit->user_id)->sum('amount');
        }
        // return $session[0];
        // return $session[0];
        // if (count($session) > 0) {
        return Excel::download(new UnlistedReport($session, $request->from, $request->to), 'Visit report.xlsx');
        // }
    }
    public function unlisted_visit_Detailed_Report(Request $request)
    {
        // return $request->all();
        $valid = Validator::make($request->all(), [
            'user' => 'required|exists:mobileusers,id',
            'from' => ['nullable', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['nullable', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);
        if ($valid->fails()) {
            return redirect('/dashboard/visithistory')->withInput()->withErrors($valid);
        }
        if ($request->from && $request->to) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
        } else {
            $from = '';
            $to = '';
        }

        $user = Mobileuser::findorFail($request->user);
        if ($from) {
            $session = Session::where('user_id', $request->user)->where('status', 'Unlisted')->whereBetween('date', [$from, $to])->orderBy('date', 'DESC')->get();
        } else {
            $session = Session::where('user_id', $request->user)->where('status', 'Unlisted')->orderBy('date', 'DESC')->get();
        }
        // return $session[0]->visits;
        // return "$user->name"."_".$user->id.$from ."_". $to."_report_".".xlsx";
        return Excel::download(new UnlistedDetailedReport($session, $request->from, $request->to, $user), "$user->name" . "_" . $user->id . $from . "_" . $to . "_report" . ".xlsx");
    }
}

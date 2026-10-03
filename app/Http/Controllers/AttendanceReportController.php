<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Mobileuser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use App\Exports\AttendanceReport;
use App\Exports\staffSummary;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AttendanceStatus;
use App\Models\Location;

class AttendanceReportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances = Attendance::groupBy('date')->select('attendances.*', DB::raw('count(*) as punch'))->get();
        foreach ($attendances as $attendance) {
            // return $attendance->date;
            // $attendancecount = Attendance::where('date',$attendance->date)->count();
            // $attendance->punch = $attendancecount;
            if ($attendance->punch > 1) {
                $checkout = Attendance::where('date', $attendance->date)->orderBy('id', 'DESC')->first();
                $attendance->checkout_date = $checkout->checkin_date;
            } else {
                $attendance->checkout_date = null;
            }
        }
        $users = Mobileuser::all();
        return view('dashboards.attendanceReport.show', ['users' => $users]);
        // $data['users'] = [];
        // $data['username'] = '';
        // // $data['']
        // return view('dashboards.attendanceReport.show',$data);
        // ,['attendances'=>$attendances,'name'=>'','users'=>[]]
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
        //
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
    public function search(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'from' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'], //'nullable|required_with:to|date_format:Y-m-d',
            'to' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'], //'nullable|required_with:from|date_format:Y-m-d|after:from'
        ]);
        if ($request->from > $request->to) {
            return back()->withInput($request->input())->withErrors(['to' => 'to date must be after from date']);
        }
        if (empty($request->user) && empty($request->from) && empty($request->to)) {
            Session::flash('status', 'All Field cannot be empty');
            return back();
        }
        if (!empty($request->from)) {
            $from = explode('-', $request->from);
            $from = $this->BS_to_AD($from[0], $from[1], $from[2]);
            $to = explode('-', $request->to);
            $to = $this->BS_to_AD($to[0], $to[1], $to[2]);

            $nepaliDay1 = $from['date'];
            $nepaliMth1 = $from['month'];
            if (strlen($nepaliDay1) == 1) {
                $nepaliDay1 = '0' . $nepaliDay1;
            }
            if (strlen($nepaliMth1) == 1) {
                $nepaliMth1 = '0' . $nepaliMth1;
            }
            $nepaliYr1 = $from['year'];
            $from = $nepaliYr1 . '-' . $nepaliMth1 . '-' . $nepaliDay1;

            $nepaliDay2 = $to['date'];
            $nepaliMth2 = $to['month'];
            if (strlen($nepaliDay2) == 1) {
                $nepaliDay2 = '0' . $nepaliDay2;
            }
            if (strlen($nepaliMth2) == 1) {
                $nepaliMth2 = '0' . $nepaliMth2;
            }
            $nepaliYr = $to['year'];
            $to = $nepaliYr . '-' . $nepaliMth2 . '-' . $nepaliDay2;
        }
        if (!empty($request->user) && empty($request->from) && empty($request->to)) {
            $attendances = Attendance::where('mobileuser_id', $request->user)
                ->groupBy('date')
                ->select('attendances.*', DB::raw('count(*) as punch'))
                ->get();
        }
        if (!empty($request->user) && !empty($request->from) && !empty($request->to)) {
            $attendances = Attendance::where('mobileuser_id', $request->user)
                ->whereBetween('date', [$from, $to])
                ->groupBy('date')
                ->select('attendances.*', DB::raw('count(*) as punch'))
                ->get();
        }
        if (empty($request->user) && !empty($request->from) && !empty($request->to)) {
            $attendances = Attendance::whereBetween('date', [$from, $to])
                ->groupBy(['date', 'mobileuser_id'])
                ->select('attendances.*', DB::raw('count(*) as punch'))
                ->get();
            // return $attendances;

        }
        foreach ($attendances as $attendance) {
            if ($attendance->punch > 1) {
                $checkout = Attendance::where('date', $attendance->date)->orderBy('id', 'DESC')->first();
                $attendance->checkout_date = $checkout->checkin_date;
                $attendance->status = $checkout->status;
            } else {
                $attendance->checkout_date = null;
            }
        }
        $user = Mobileuser::all();
        $userinfo = Mobileuser::where('id', $request->user)->first();
        if ($userinfo) {
            $username = $userinfo->name;
        } else {
            $username = '';
        }

        return view('dashboards.attendanceReport.show', ['attendances' => $attendances, 'users' => $user, 'name' => '', 'from' => $request->from, 'to' => $request->to, 'frominput' => $request->from, 'toinput' => $request->to, 'userinput' => $request->user, 'username' => $username]);
    }
    public function download(Request $request)
    {

        $this->validate($request, [
            'user' => 'required|exists:mobileusers,id',
            'from' => ['required', 'required_with:to', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'to' => ['required', 'required_with:from', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/']
        ]);
        if ($request['from'] > $request['to']) {
            return back()->withInput($request->input())->withErrors(['to' => 'end date must be after or equal with start date']);
        }
        $mobileuser = Mobileuser::findorFail($request->user);
        if ($request->from) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);
            $start = new \DateTime($from);
            $end = new \DateTime($to);

            $interval = $end->diff($start);
            $total_days = $interval->format("%a");

            if ($total_days > 32) {
                return back()->withInput()->withErrors(['to' => 'date range cannot be more than 32 days']);
            }
            $attendancestatus = AttendanceStatus::with('mobileuser')
                ->where('mobileuser_id', $request->user)
                ->whereBetween('date', [$from, $to])
                ->select('*', DB::raw('count(*) as total_punch'))
                ->groupBy(['mobileuser_id', 'date'])
                ->get();
        } else {
            $from = '';
            $to = '';
            $attendancestatus = AttendanceStatus::where('mobileuser_id', $request->user)->groupBy(['mobileuser_id', 'date'])->get();
        }
        $days = $start->diff($end, true)->days;

        $saturday = intval($days / 7) + ($start->format('N') + $days % 7 >= 6); // No of saturday
        $present = $attendancestatus->count();
        $absent = $total_days - ($saturday + $present);
        foreach ($attendancestatus as $at_status) {
            $checkout = AttendanceStatus::where(['attendance_id' => $at_status->attendance_id, 'status' => 'checkout'])
                ->orderBy('id', 'DESC')
                ->first();

            $late = strtotime($at_status->time) - strtotime('10:00:00');
            $late = gmdate("H:i:s", ($late < 0) ? 0 : $late);
            $early = strtotime('10:00:00') - strtotime($at_status->time);
            $early = gmdate("H:i:s", ($early < 0) ? 0 : $early);

            $at_status->early = $early;
            $at_status->late = $late;
            if ($checkout) {
                $latecheckout = strtotime($checkout->time) - strtotime('17:00:00');
                $latecheckout = gmdate("H:i:s", ($latecheckout < 0) ? 0 : $latecheckout);
                $earlycheckout = strtotime('17:00:00') - strtotime($checkout->time);
                $earlycheckout = gmdate("H:i:s", ($earlycheckout < 0) ? 0 : $earlycheckout);
                $at_status->latecheckout = $latecheckout;
                $at_status->earlycheckout = $earlycheckout;
            } else {
                $at_status->latecheckout = 0;
                $at_status->earlycheckout = 0;
            }

            $at_status->checkout = ($checkout) ? $checkout->time : '';
            if ($checkout) {
                $at_status->status = $checkout->status;
            }
        }
        foreach ($attendancestatus as $at_status) {
            $location = Location::where(['user_id' => $at_status->mobileuser_id, 'day' => $at_status->date])->first();
            if (!$location) {
                $at_status->distace = '';
            } else {
                $coordinates = $location->coordinates;
                $distance = 0;
                if (count($coordinates) > 0) {
                    foreach ($coordinates as $key => $coordinate) {
                        if ($key == 0) {
                            continue;
                        }
                        $lat1 = $coordinates[$key - 1]['latitude'];
                        $lon1 = $coordinates[$key - 1]['longitude'];
                        $lat2 = $coordinate['latitude'];
                        $lon2 = $coordinate['longitude'];
                        $unit = "K";
                        // $distance = $distance + $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);\
                        $c_d = $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
                        if (is_nan($c_d)) {
                            $c_d = 0;
                        }
                        $distance = $distance + $c_d;
                    }
                    $at_status->distance = number_format($distance, 4) . " KM";
                }
            }
            $checkout = AttendanceStatus::where(['attendance_id' => $at_status->attendance_id, 'status' => 'checkout'])
                ->orderBy('id', 'DESC')
                ->first();

            $late = strtotime($at_status->time) - strtotime('10:00:00');
            $late = gmdate("H:i:s", ($late < 0) ? 0 : $late);
            $early = strtotime('10:00:00') - strtotime($at_status->time);
            $early = gmdate("H:i:s", ($early < 0) ? 0 : $early);

            $at_status->early = $early;
            $at_status->late = $late;
            if ($checkout) {
                $latecheckout = strtotime($checkout->time) - strtotime('17:00:00');
                $latecheckout = gmdate("H:i:s", ($latecheckout < 0) ? 0 : $latecheckout);
                $earlycheckout = strtotime('17:00:00') - strtotime($checkout->time);
                $earlycheckout = gmdate("H:i:s", ($earlycheckout < 0) ? 0 : $earlycheckout);
                $at_status->latecheckout = $latecheckout;
                $at_status->earlycheckout = $earlycheckout;
            } else {
                $at_status->latecheckout = 0;
                $at_status->earlycheckout = 0;
            }

            $at_status->checkout = ($checkout) ? $checkout->time : '';
            if ($checkout) {
                $at_status->status = $checkout->status;
            }
        }
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        // $data['attendances'] = $attendancestatus;
        $data['userinput'] = $mobileuser->id;
        $data['present'] = $present;
        $data['absent'] = $absent;
        // return $attendancestatus;

        // $absent = 0;
        // $present = 0;
        return Excel::download(new AttendanceReport($attendancestatus, $mobileuser, $data), $mobileuser->name . '.xlsx');
    }
    public function summary(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'to' =>  ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
            'from' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to']
        ]);
        if ($request['from'] > $request['to']) {
            return back()->withInput($request->input())->withErrors(['to' => 'end date must be after or equal with start date']);
        }
        if (!($request->from || $request->to)) {
            $data['from'] = '';
            $data['to'] = '';
            $data['attendances'] = [];
            $data['userinput'] = '';
            $data['users'] = Mobileuser::all();
            return view('dashboards.attendanceReport.summary', $data);
        }
        $from = $this->custom_BS_to_AD($request->from);
        $to = $this->custom_BS_to_AD($request->to);
        $start = new \DateTime($from);
        $end = new \DateTime($to);
        $intime = "10:00:00";
        $outtime = "17:00:00";

        $interval = $end->diff($start);
        $total_days = $interval->format("%a") + 1;

        if ($total_days > 32) {
            return back()->withInput()->withErrors(['to' => 'date range cannot be more than 32 days']);
        }
        $days = $start->diff($end, true)->days;

        $saturday = intval($days / 7) + ($start->format('N') + $days % 7 >= 6);

        if ($request->user) {
            $data['userinput'] = $request->user;
            $attendances = Attendance::with('mobileuser')
                ->where('mobileuser_id', $request->user)
                ->whereBetween('date', [$from, $to])
                ->select('attendances.*', DB::raw('count(*) as total_present'), DB::raw('sum(totalkm) as final_km'))
                ->groupBy('mobileuser_id')
                ->get();
        } else {
            $data['userinput'] = '';
            $attendances = Attendance::with('mobileuser')
                ->whereBetween('date', [$from, $to])
                ->select('attendances.*', DB::raw('count(*) as total_present'), DB::raw('sum(totalkm) as final_km'))
                ->groupBy('mobileuser_id')
                ->get();
        }
        $i = 1;
        foreach ($attendances as $key => $attendance) {
            $status = AttendanceStatus::where('mobileuser_id', $attendance->mobileuser_id)->whereBetween('date', [$from, $to]);
            $travel_distance = 0;
            $location = Location::where(['user_id' => $attendance->mobileuser_id])->whereBetween('day', [$from, $to])->get();
            if (count($location) == 0) {
                $attendance->distance = $travel_distance;
            } else {
                foreach ($location as $l) {
                    $distance = 0;
                    $coordinates = $l->coordinates;
                    if (count($coordinates) > 0) {
                        foreach ($coordinates as $key => $coordinate) {
                            if ($key == 0) {
                                continue;
                            }
                            $lat1 = $coordinates[$key - 1]['latitude'];
                            $lon1 = $coordinates[$key - 1]['longitude'];
                            $lat2 = $coordinate['latitude'];
                            $lon2 = $coordinate['longitude'];
                            $unit = "K";
                            // $distance = $distance + $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);\
                            $c_d = $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
                            if (is_nan($c_d)) {
                                $c_d = 0;
                            }
                            $distance = $distance + $c_d;
                        }
                        $travel_distance = $travel_distance + round($distance, 3);
                    }
                }
                $attendance->distance = $travel_distance . " KM";
            }
            // calculation for late attendance
            $late = clone $status;
            $late = $late->where('status', 'checkin')
                ->whereTime('time', '>', '10:01:00')
                ->groupBy(['mobileuser_id', 'date'])
                ->select(DB::raw("SUM(time_to_sec(timediff(time,'10:01:00'))) as datetime"))
                ->get()
                ->sum('datetime');

            // calculation for early attendance
            $early = clone $status;
            $early = $early->where('status', 'checkin')
                ->whereTime('time', '<', '10:00:00')
                ->groupBy(['mobileuser_id', 'date'])
                ->select('*', DB::raw("SUM(time_to_sec(timediff('10:00:00',time))) as datetime"))
                ->get()
                ->sum('datetime');

            $latecheckout = clone $status;
            $latecheckout = $latecheckout->where('status', ['checkout', 'breakstart', 'breakend'])
                ->whereTime('time', '>', '17:00:00')
                ->groupBy(['mobileuser_id', 'date'])
                ->select(DB::raw("SUM(time_to_sec(timediff(time,'17:00:00'))) as datetime"))
                ->get()
                ->sum('datetime');

            // calculation for early attendance
            $earlycheckout = clone $status;
            $earlycheckout = $earlycheckout->whereIn('status', ['checkout', 'breakstart', 'breakend'])
                ->whereTime('time', '<', '17:00:00')
                ->groupBy(['mobileuser_id', 'date'])
                ->select('*', DB::raw("SUM(time_to_sec(timediff('17:00:00',time))) as datetime"))
                ->get()
                ->sum('datetime');

            $absent = $total_days - $attendance->total_present;
            if ($absent > 0) {
                // $ot = 0;
                $absent = $absent;
            } else {
                $absent = 0;
            }
            $attendance->total_days = $total_days;
            $attendance->late = gmdate("H:i:s", $late);
            $attendance->early = gmdate("H:i:s", $early);
            $attendance->latecheckout = gmdate("H:i:s", $latecheckout);
            $attendance->earlycheckout = gmdate("H:i:s", $earlycheckout);
            $attendance->absent = $absent;
            $attendance->intime = $intime;
            $attendance->outtime = $outtime;
            $i++;
        }
        $data['attendances'] = $attendances;
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            $data['users'] = Mobileuser::whereIn('id', $mobileuser)->get();
        } else {
            $data['users'] = Mobileuser::get();
        }
        $from = $request->from;
        $to = $request->to;
        // return view('dashboards.attendanceReport.summary',$data);
        // return $attendances;
        // ob_end_clean();
        // ob_start();
        return Excel::download(new staffSummary($attendances, $from, $to, $total_days), 'staff attendance.xlsx');
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
}

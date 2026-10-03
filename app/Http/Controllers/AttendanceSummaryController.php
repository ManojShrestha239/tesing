<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Exports\AttendanceReport;
use Illuminate\Http\Request;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Models\Location;
use App\Models\Mobileuser;

class AttendanceSummaryController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    // public function index(Request $request)
    // {
    //     // dd(1);
    //     $this->validate($request, [
    //         'user' => 'nullable|exists:mobileusers,id',
    //         'to' =>  ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
    //         'from' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to']
    //     ]);
    //     if (!($request->from || $request->to)) {
    //         $data['from'] = '';
    //         $data['to'] = '';
    //         $data['attendances'] = [];
    //         $data['userinput'] = '';
    //         $data['users'] = Mobileuser::all();
    //         return view('dashboards.attendanceReport.summary', $data);
    //     }
    //     $from = $this->custom_BS_to_AD($request->from);
    //     $to = $this->custom_BS_to_AD($request->to);
    //     $start = new \DateTime($from);
    //     $end = new \DateTime($to);

    //     $interval = $end->diff($start);
    //     $total_days = $interval->format("%a") + 1;

    //     if ($total_days > 32) {
    //         return back()->withInput()->withErrors(['to' => 'date range cannot be more than 32 days']);
    //     }
    //     $days = $start->diff($end, true)->days;

    //     $saturday = intval($days / 7) + ($start->format('N') + $days % 7 >= 6);

    //     if ($request->user) {
    //         $data['userinput'] = $request->user;
    //         $attendances = Attendance::with('mobileuser')->where('mobileuser_id', $request->user)->whereBetween('date', [$from, $to])->select('attendances.*', DB::raw('count(*) as total_present'), DB::raw('sum(totalkm) as final_km'))->groupBy('mobileuser_id')->get();
    //     } else {
    //         $data['userinput'] = '';
    //         $attendances = Attendance::with('mobileuser')->whereBetween('date', [$from, $to])->select('attendances.*', DB::raw('count(*) as total_present'), DB::raw('sum(totalkm) as final_km'))->groupBy('mobileuser_id')->get();
    //     }
    //     foreach ($attendances as $attendance) {
    //         $travel_distance = 0;
    //         $location = Location::where(['user_id' => $attendance->mobileuser_id])->whereBetween('day', [$from, $to])->get();
    //         if (count($location) == 0) {
    //             $attendance->distance = $travel_distance;
    //         } else {
    //             foreach ($location as $l) {
    //                 $distance = 0;
    //                 $coordinates = $l->coordinates;
    //                 if (count($coordinates) > 1) {
    //                     foreach ($coordinates as $key => $coordinate) {
    //                         if ($key == 0) {
    //                             continue;
    //                         }
    //                         $lat1 = $coordinates[$key - 1]['latitude'];
    //                         $lon1 = $coordinates[$key - 1]['longitude'];
    //                         $lat2 = $coordinate['latitude'];
    //                         $lon2 = $coordinate['longitude'];
    //                         $unit = "K";
    //                         $c_d = $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
    //                         if (is_nan($c_d)) {
    //                             $c_d = 0;
    //                         }
    //                         $distance = $distance + $c_d;
    //                     }
    //                     $travel_distance = $travel_distance + $distance;
    //                 }
    //             }
    //             $attendance->distance = $travel_distance;
    //         }
    //         $status = AttendanceStatus::where('mobileuser_id', $attendance->mobileuser_id)->whereBetween('date', [$from, $to]);

    //         // calculation for late attendance
    //         $late = clone $status;
    //         $late = $late->where('status', 'checkin')
    //             ->whereTime('time', '>', '10:01:00')
    //             ->groupBy(['mobileuser_id', 'date'])
    //             ->select(DB::raw("SUM(time_to_sec(timediff(time,'10:01:00')/3600)) as datetime"))
    //             ->get()
    //             ->sum('datetime');

    //         // calculation for early attendance
    //         $early = clone $status;
    //         $early = $early->where('status', 'checkin')
    //             ->whereTime('time', '<', '10:00:00')
    //             ->groupBy(['mobileuser_id', 'date'])
    //             ->select('*', DB::raw("SUM(time_to_sec(timediff('10:00:00',time)/3600)) as datetime"))
    //             ->get()
    //             ->sum('datetime');

    //         // count for normal time attendance
    //         $normal = clone $status;
    //         $normal = $normal->where('status', 'checkin')
    //             ->whereTime('time', '10:00:00')
    //             ->groupBy(['mobileuser_id', 'date'])
    //             ->count();

    //         // count for actual time attendance
    //         $actual = clone $status;
    //         $actual = $actual->where('status', 'checkin')
    //             ->whereTime('time', '10:00:00')
    //             ->groupBy(['mobileuser_id', 'date'])
    //             ->count();
    //         $absent = $total_days - $attendance->total_present;
    //         if ($absent > 0) {
    //             $ot = 0;
    //             $absent = $absent;
    //         } else {
    //             $ot = $attendance->total_present - $total_days;
    //             $absent = 0;
    //         }
    //         $attendance->total_days = $total_days;
    //         $attendance->normal = $normal;
    //         $attendance->actual = $actual;
    //         $attendance->late = $late;
    //         $attendance->early = $early;
    //         $attendance->absent = $absent;
    //         $attendance->ot = $ot;
    //         $attendance->weekend = $saturday;
    //         $attendance->holiday = $saturday;
    //     }
    //     $data['attendances'] = $attendances;
    //     if (Auth::user()->hasRole('ASM')) {
    //         $admin_area = collect(Auth::user()->area);
    //         $admin_area = $admin_area->pluck('areacode');
    //         $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
    //             ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
    //             ->whereIn('area_code', $admin_area)
    //             ->groupBy('mobileusers.id')
    //             ->pluck('mobileusers.id');
    //         $data['users'] = Mobileuser::whereIn('id', $mobileuser)->get();
    //     } else {
    //         $data['users'] = Mobileuser::get();
    //     }
    //     $data['from'] = $request->from;
    //     $data['to'] = $request->to;
    //     return view('dashboards.attendanceReport.summary', $data);
    // }

    public function index(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'to' =>  ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
            'from' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to']
        ]);

        if (!$request->filled(['from', 'to'])) {
            return view('dashboards.attendanceReport.summary', [
                'from'        => '',
                'to'          => '',
                'attendances' => [],
                'userinput'   => '',
                'users'       => Mobileuser::all(),
            ]);
        }

        $from = $this->custom_BS_to_AD($request->from);
        $to = $this->custom_BS_to_AD($request->to);
        $start = new \DateTime($from);
        $end = new \DateTime($to);

        $officeCheckinTime = config('app.Office_START_TIME', '10:00:00');
        $officeCheckoutTime = config('app.Office_END_TIME', '17:00:00');

        $interval = $end->diff($start);
        $total_days = $interval->format("%a") + 1;

        if ($total_days > 32) {
            return back()->withInput()->withErrors(['to' => 'date range cannot be more than 32 days']);
        }
        $days = $start->diff($end, true)->days;

        $saturday = intval($days / 7) + ($start->format('N') + $days % 7 >= 6);

        $data['userinput'] = $request->user ?? '';

        $attendanceSummary = Attendance::select(
            'mobileuser_id',
            DB::raw('COUNT(*) as total_present'),
            DB::raw('SUM(totalkm) as final_km')
        )
            ->whereBetween('date', [$from, $to])
            ->groupBy('mobileuser_id');

        $attendances = Attendance::with('mobileuser')
            ->whereBetween('date', [$from, $to])
            ->when($request->user, function ($q) use ($request) {
                $q->where('attendances.mobileuser_id', $request->user);
            })
            ->leftJoinSub($attendanceSummary, 'summary', function ($join) {
                $join->on('attendances.mobileuser_id', '=', 'summary.mobileuser_id');
            })
            ->select(
                'attendances.*',
                'summary.total_present',
                'summary.final_km'
            )
            ->groupBy('attendances.mobileuser_id')
            ->get();

        $userIds = $attendances->pluck('mobileuser_id')->unique();

        $locationsByUser = Location::whereIn('user_id', $userIds)
            ->whereBetween('day', [$from, $to])
            ->get()
            ->groupBy('user_id');

        foreach ($attendances as $attendance) {
            $travelDistance = 0;
            $userLocations = $locationsByUser[$attendance->mobileuser_id] ?? null;
            if (!$userLocations) {
                $attendance->distance = 0;
                continue;
            }

            // foreach ($userLocations as $location) {
            //     $coords = $location->coordinates;
            //     if (!is_array($coords) || count($coords) < 2) {
            //         continue;
            //     }
            //     $prev = $coords[0];
            //     for ($i = 1, $len = count($coords); $i < $len; $i++) {

            //         $curr = $coords[$i];

            //         if (
            //             empty($prev['latitude']) || empty($prev['longitude']) ||
            //             empty($curr['latitude']) || empty($curr['longitude'])
            //         ) {
            //             $prev = $curr;
            //             continue;
            //         }

            //         $distance = $this->haversineKm(
            //             $prev['latitude'],
            //             $prev['longitude'],
            //             $curr['latitude'],
            //             $curr['longitude']
            //         );

            //         if (!is_nan($distance)) {
            //             $travelDistance += $distance;
            //         }

            //         $prev = $curr;
            //     }
            // }

            // ================= FIRST CHECK-IN BASE QUERY =================
            $baseCheckin = AttendanceStatus::where('mobileuser_id', $attendance->mobileuser_id)
                ->whereBetween('date', [$from, $to])
                ->where('status', 'checkin')
                ->select(
                    'mobileuser_id',
                    'date',
                    DB::raw('MIN(time) as first_checkin')
                )
                ->groupBy('mobileuser_id', 'date');

            // ================= LATE =================
            $lateDaysCheckin = DB::query()
                ->fromSub($baseCheckin, 't')
                ->whereTime('first_checkin', '>', $officeCheckinTime)
                ->count();

            // ================= EARLY =================
            $earlyDaysCheckin = DB::query()
                ->fromSub($baseCheckin, 't')
                ->whereTime('first_checkin', '<', $officeCheckinTime)
                ->count();

            // ================= FIRST CHECK-OUT BASE QUERY =================
            $baseCheckout = AttendanceStatus::where('mobileuser_id', $attendance->mobileuser_id)
                ->whereBetween('date', [$from, $to])
                ->where('status', 'checkout')
                ->select(
                    'mobileuser_id',
                    'date',
                    DB::raw('MAX(time) as last_checkout')
                )
                ->groupBy('mobileuser_id', 'date');

            // ================= LATE =================
            $lateDaysCheckout = DB::query()
                ->fromSub($baseCheckout, 't')
                ->whereTime('last_checkout', '>', $officeCheckoutTime)
                ->count();

            // ================= EARLY =================
            $earlyDaysCheckout = DB::query()
                ->fromSub($baseCheckout, 't')
                ->whereTime('last_checkout', '<', $officeCheckoutTime)
                ->count();

            $absent = $total_days - $attendance->total_present;
            if ($absent > 0) {
                $absent = $absent;
            } else {
                $absent = 0;
            }
            $attendance->total_days = $total_days;
            $attendance->early = $earlyDaysCheckin;
            $attendance->late = $lateDaysCheckin;
            $attendance->earlycheckout = $earlyDaysCheckout;
            $attendance->latecheckout = $lateDaysCheckout;
            $attendance->absent = $absent;
            $attendance->weekend = $saturday;
            $attendance->holiday = $saturday;
            $attendance->distance = round($travelDistance, 2);
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
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        return view('dashboards.attendanceReport.summary', $data);
    }

    private function haversineKm($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // KM

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }

    public function show(Request $request, $id)
    {
        $this->validate($request, [
            'from' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:to'],
            'to' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required_with:from'],
        ]);
        $mobileuser = Mobileuser::findorFail($id);
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
            $attendancestatus = AttendanceStatus::with('mobileuser')->where('mobileuser_id', $id)->whereBetween('date', [$from, $to])->select('*', DB::raw('count(*) as total_punch'))->groupBy(['mobileuser_id', 'date'])->get();
        } else {
            $from = '';
            $to = '';
            $attendancestatus = AttendanceStatus::where('mobileuser_id', $id)->groupBy(['mobileuser_id', 'date'])->get();
        }
        foreach ($attendancestatus as $at_status) {
            $location = Location::where(['user_id' => $at_status->mobileuser_id, 'day' => $at_status->date])->first();
            if (!$location) {
                $at_status->distance = 0;
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
                        $distance = $distance + $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
                    }
                    $at_status->distance = number_format($distance, 4) . " KM";
                }
            }
            $checkout = AttendanceStatus::where(['attendance_id' => $at_status->attendance_id, 'status' => 'checkout'])->orderBy('id', 'DESC')->first();

            $at_status->checkout = ($checkout) ? $checkout->time : '';
            if ($checkout) {
                $at_status->status = $checkout->status;
            }
        }
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        $data['attendances'] = $attendancestatus;
        $data['userinput'] = $mobileuser->id;
        $data['username'] = $mobileuser->name;
        $data['users'] = Mobileuser::all();

        return view('dashboards.attendanceReport.show', $data);
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

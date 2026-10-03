<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceImage;
use App\Models\Mobileuser;
use Illuminate\Support\Facades\DB;
use App\Traits\FCM;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use App\Models\Location;

class AttendanceController extends Controller
{
    use FCM;
    use Nepali_Calendar;
    use CustomNepaliCalendarConverter;

    function index()
    {
        $date = Date('Y-m-d');
        $user = Mobileuser::with(['daily_attendance' => function ($query) {
            $query->where('date', date('Y-m-d'));
        }])->where('status', 'Active')->get();
        foreach ($user as $l) {
            // $checkout = Attendance::where('mobileuser_id', $l->id)->where('date', $date)->first();
            // $l->attend = $checkout;
            $l->flag = 'single';
            if ($l->daily_attendance) {
                // return $l->daily_attendance;
                $location = Location::where(['user_id' => $l->id, 'day' => $date])->first();
                if (!$location) {
                    $l->distace = '';
                } else {
                    $coordinates = $location->coordinates;
                    $distance = 0;
                    if (count($coordinates) > 0) {
                        // return 1;
                        foreach ($coordinates as $key => $coordinate) {
                            if ($key == 0) {
                                continue;
                            }
                            $lat1 = $coordinates[$key - 1]['latitude'];
                            $lon1 = $coordinates[$key - 1]['longitude'];
                            $lat2 = $coordinate['latitude'];
                            $lon2 = $coordinate['longitude'];
                            $unit = "K";
                            // $distance = $distance + $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
                            $c_d = $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
                            if (is_nan($c_d)) {
                                $c_d = 0;
                            }
                            $distance = $distance + $c_d;
                        }
                        $l->distance = number_format($distance, 4) . " KM";
                    }
                }
            } else {
                // return "sadasdd";
                $l->distance = '';
            }
        }
        $today = $this->custom_AD_to_BS($date);

        $data['lists'] = $user;
        $data['users'] = $user;
        $data['date'] = $today;
        $data['name'] = '';
        $data['today'] = $today;
        return view('dashboards.attendance.list', $data);
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
    function view($id)
    {
        $list = Attendance::where('mobileuser_id', $id)
            ->select('id', 'mobileuser_id', 'date', 'checkin_date', DB::raw('MAX(status) AS status'), 'created_at', 'updated_at')
            ->orderBy('created_at', 'ASC')
            ->groupBy('date')
            ->get();
        $mo = Mobileuser::findorFail($id);

        return view('dashboards.attendance.view', ['lists' => $list, 'mo' => $mo]);
    }
    public function show($id, Request $request)
    {
        $this->validate($request, [
            'date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
        ]);
        $data['attendances'] = Attendance::with('attendance_status', 'mobileuser')->findOrFail($id);
        $date = $this->custom_BS_to_AD($request->date);
        $location = Location::where(['user_id' => $data['attendances']->mobileuser_id, 'day' => $date])->first();

        if (!empty($location)) {
            $coordinates = $location->coordinates;
            $distance = 0;
            if (count($coordinates) > 1) {
                foreach ($coordinates as $key => $coordinate) {
                    if ($key == 0) {
                        continue;
                    }
                    $lat1 = $coordinates[$key - 1]['latitude'];
                    $lon1 = $coordinates[$key - 1]['longitude'];
                    $lat2 = $coordinate['latitude'];
                    $lon2 = $coordinate['longitude'];
                    $unit = "K";

                    $c_d = $this->calculate_distance($lat1, $lon1, $lat2, $lon2, $unit);
                    if (is_nan($c_d)) {
                        $c_d = 0;
                    }
                    $distance = $distance + $c_d;
                }
            }
        }

        $data['distance'] = number_format($distance ?? 0, 4) . " KM";
        return view('dashboards.attendance.daily', $data);
    }
    public function filter(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            // 'end_date'=>['nullable','required_with:date','regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
        ]);
        // $mobileuser = Mobileuser::findorFail($id);
        if (!$request->user && !$request->date) {
            return back()->with('status', 'Both field cannot be empty.');
        }
        $list = '';
        $date = $this->custom_BS_to_AD($request->date);
        if ($request->user) {
            $list = Mobileuser::with(['daily_attendance' => function ($query) use ($date) {
                $query->where('date', $date);
            }])->where('id', $request->user)->get();
        } else {
            $list = Mobileuser::with(['daily_attendance' => function ($query) use ($date) {
                $query->where('date', $date);
            }])->get();
        }
        // $user = Mobileuser::with(['daily_attendance' => function ($query) use($date) {
        //     $query->where('date', $date);
        // }])->get();
        $user = Mobileuser::get();
        // foreach ($user as $l) {
        //     // $checkout = Attendance::where('mobileuser_id', $l->id)->where('date', $date)->first();
        //     // $l->attend = $checkout;
        //     // $l->flag = 'single';
        // }

        $data['lists'] = $list;
        $data['users'] = $user;
        $data['date'] = $request->date;
        $data['userid'] = $request->user;
        $data['name'] = '';
        $data['today'] = $request->date;
        return view('dashboards.attendance.list', $data);
    }
    public function attendanceimageshow(Request $request, $id)
    {
        // return $request->all();
        $date = $this->custom_BS_to_AD($request->date);
        $data['date'] = $request->date;

        $data['attendance'] = Attendance::where(['mobileuser_id' => $id, 'date' => $date])->first();
        $data['attendanceimages'] = AttendanceImage::where(['mobileuser_id' => $id, 'date' => $date])->get();

        return view('dashboards.attendance.attendanceimg', $data);
    }

    public function updatekmformshow($id, Request $request)
    {
        // return $request->all();
        $data['attendance'] = Attendance::findorfail($id);
        return view('dashboards.attendance.updatekm', $data);
    }

    public function updatekmformpost($id, Request $request)
    {
        $attendacne = Attendance::findorfail($id);

        $attendacne->checkinkm = $request->checkin;
        $attendacne->checkoutkm = $request->checkout;
        $attendacne->totalkm = $request->checkout - $request->checkin;

        $attendacne->save();

        return back()->with('Status', "Update Kilometer.");
    }
}

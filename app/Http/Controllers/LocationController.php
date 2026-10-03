<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Area;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mobileuser;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductSize;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\DeviceLog;

class LocationController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ldate = date('Y-m-d');
        $dates = $this->custom_AD_to_BS($ldate);
        $rol = DB::table('roles')->get();
        $arr = array();
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            $dealers = Dealer::with('area')
                ->whereHas('area', function ($query) {
                    $query->where('areas.status', '=', 'Published');
                })
                ->where('active_status', 'Active')
                ->whereIn('area_code', $admin_area)
                ->get();
            $currentLoc = Location::where('day', $ldate)->whereIn('user_id', $mobileuser)->get();
        } else {
            $currentLoc = Location::where('day', $ldate)->get();
            $dealers = Dealer::with('area')
                ->whereHas('area', function ($query) {
                    $query->where('areas.status', '=', 'Published');
                })
                ->where('active_status', 'Active')
                ->get();
        }
        if (!empty($currentLoc)) {
            foreach ($currentLoc as $loc) {
                $mouser = Mobileuser::findorFail($loc->user_id);
                $new = $loc['coordinates'];
                if (!empty($new)) {
                    foreach ($new as $latData) {
                    }
                    $newArr = array('name' => $mouser->name, 'lat' => $latData['latitude'], 'lng' => $latData['longitude'], 'is_exact' => 'true');
                    $arr[] = ((object) $newArr);
                }
            }
        }
        // $array_final = json_encode($arr, JSON_NUMERIC_CHECK);
        $data['total_order'] = Order::sum('total_price');
        // $data['pending'] = Order::where('status', 'Pending')->sum('total_price');
        // $data['cancelled'] = Order::where('status', 'Declined')->sum('total_price');
        $data['today_order'] = Order::where('date', date('Y-m-d'))->sum('total_price');
        $unlisted = Session::where('status', 'Unlisted')->count();
        $total_onlinevisit =  Session::where('status', '!=', 'Unlisted')->count();
        $data['today_onlinevisit'] = Session::where('date', date('Y-m-d'))->where('status', '!=', 'Unlisted')->count();
        $data['today_unlisted'] = Session::where('date', date('Y-m-d'))->where('status', 'Unlisted')->count();
        $data['unlisted'] = $unlisted;
        $data['total_visit'] = $unlisted + $total_onlinevisit;
        $data['today_totalvisit'] = $data['today_onlinevisit'] + $data['today_unlisted'];
        $data['currentLoc'] = json_encode($arr, JSON_NUMERIC_CHECK);
        $data['users'] = $currentLoc;
        $data['rol'] = $rol;
        $data['date'] = $dates;
        $data['dealers'] = $dealers;
        $data['total_product'] = ProductSize::where('status', 1)->count();
        $data['collection'] = Payment::sum('amount');
        $data['today_collection'] = Payment::where('collection_date', date('Y-m-d'))->sum('amount');
        $data['present'] = Attendance::where('date', date('Y-m-d'))->distinct()->count('mobileuser_id');
        return view('dashboards.map.representatives', $data);
    }

    public function indexReplace(Request $request)
    {
        $this->validate($request, [
            'role' => 'required',
            'date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
        ]);


        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($request['date'] > $today) {


            return redirect('dashboard')->with('status', 'Date must be a date before or equal to today');
        }
        $dates = $this->custom_BS_to_AD($request->date);

        $rol = DB::table('roles')->get();
        $arr = array();

        // $currentLoc = Location::where('day', $dates)->where('role_id', '=', '1')->get();
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $dealers = Dealer::with('area')
                ->whereHas('area', function ($query) {
                    $query->where('areas.status', '=', 'Published');
                })
                ->where('active_status', 'Active')
                ->whereIn('area_code', $admin_area)
                ->get();
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_mobileuser.area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            $currentLoc = Location::where('day', $dates)->whereIn('user_id', $mobileuser)->get();
        } else {
            $currentLoc = Location::where('day', $dates)->get();
            $dealers = Dealer::with('area')
                ->whereHas('area', function ($query) {
                    $query->where('areas.status', '=', 'Published');
                })
                ->where('active_status', 'Active')
                ->get();
        }

        if (!empty($currentLoc)) {
            foreach ($currentLoc as $loc) {
                $new = $loc['coordinates'];
                $mouser = Mobileuser::findorFail($loc->user_id);

                //                $data = json_decode($new, TRUE);
                if (!empty($new)) {
                    foreach ($new as $latData) {

                        // $newArr = $x = end($latData->coordinates);
                        //  $arr[] = ((object) $newArr);
                    }
                    $newArr = array('name' => $mouser->name, 'lat' => $latData['latitude'], 'lng' => $latData['longitude'], 'is_exact' => 'true');
                    $arr[] = ((object) $newArr);
                }
            }
        }
        $array_final = json_encode($arr, JSON_NUMERIC_CHECK);
        $d = $request['date'];
        // $dealers = Dealer::where('active_status', 'Active')->get();
        //$locations = DB::table('dealers')->where('status', 'approved')->get();

        $data['total_order'] = Order::sum('total_price');
        // $data['pending'] = Order::where('status', 'Pending')->sum('total_price');
        // $data['cancelled'] = Order::where('status', 'Declined')->sum('total_price');
        $data['today_order'] = Order::where('date', $dates)->sum('total_price');
        $unlisted = Session::where('status', 'Unlisted')->count();
        $total_onlinevisit = Session::count();
        $data['today_onlinevisit'] = Session::where('date', $dates)->count();
        $data['today_unlisted'] = Session::where('date', $dates)->where('status', 'Unlisted')->count();
        $data['unlisted'] = $unlisted;
        $data['total_visit'] = $unlisted + $total_onlinevisit;
        $data['today_totalvisit'] = $data['today_onlinevisit'] + $data['today_unlisted'];
        $data['users'] = $currentLoc;
        $data['rol'] = $rol;
        $data['role'] = $request->role;
        $data['date'] = $request->date;
        $data['dealers'] = $dealers;
        $data['total_product'] = ProductSize::where('status', 1)->count();
        $data['collection'] = Payment::sum('amount');
        $data['today_collection'] = Payment::where('collection_date', $dates)->sum('amount');
        $data['present'] = Attendance::where('date', $dates)->distinct()->count('mobileuser_id');

        $data['currentLoc'] = json_encode($arr, JSON_NUMERIC_CHECK);
        return view('dashboards.map.representatives', $data);
    }

    public function individual(Request $request, $id, $date = null)
    {
        // 1. Determine the date string to use
        if ($date !== null) {
            // Validate the URL date parameter (expects YYYY-MM-DD)
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/", $date)) {
                return redirect('/dashboard');
            }
            $dateInput = $date;           // use URL date
        } else {
            // Use request input 'date', or fallback to today
            $dateInput = $request->input('date', date('Y-m-d'));
        }

        // Convert the input date (presumably BS) to AD for database queries
        $dates = $this->custom_BS_to_AD($dateInput);   // or use BS_to_AD() if that's your helper

        // 2. Fetch user info and areas
        $userinfo = Mobileuser::findOrFail($id);
        $rol = DB::table('roles')->where('id', $userinfo->role_id)->get();
        $areas = explode(' :: ', $userinfo->areas);

        $areaNames = [];
        foreach ($areas as $code) {
            $areaNames[] = Area::where('areacode', $code)->pluck('area')->first();
        }
        $areasList = [['areas' => $areaNames]];

        // 3. Fetch location data for the given day
        $currentLoc = Location::where('day', $dates)
            ->where('user_id', $id)
            ->get();

        $arr = [];
        $minTimeData = null;
        $maxTimeData = null;

        if ($currentLoc->isNotEmpty()) {
            foreach ($currentLoc as $loc) {
                if (empty($loc->coordinates)) {
                    continue;
                }
                foreach ($loc->coordinates as $latData) {
                    $arr[] = (object) [
                        'address' => [
                            'lat' => $latData['latitude'],
                            'lng' => $latData['longitude'],
                        ],
                        'title' => $latData['time'],
                    ];

                    // Track earliest and latest coordinates
                    if ($minTimeData === null || strtotime($latData['time']) < strtotime($minTimeData['time'])) {
                        $minTimeData = $latData;
                    }
                    if ($maxTimeData === null || strtotime($latData['time']) > strtotime($maxTimeData['time'])) {
                        $maxTimeData = $latData;
                    }
                }
            }
        }

        // 4. Get check‑in and last‑known addresses (if data exists)
        $lastlocation = '';
        $checkinaddress = '';
        $last_synced_on = '';

        if ($maxTimeData && $minTimeData) {
            $last_synced_on = $maxTimeData['time'];
            $lastlocation = $this->getAddress($maxTimeData['latitude'], $maxTimeData['longitude']);
            $checkinaddress = $this->getAddress($minTimeData['latitude'], $minTimeData['longitude']);
        }

        // 5. Sort locations by time
        usort($arr, function ($a, $b) {
            return strtotime($a->title) <=> strtotime($b->title);
        });

        $count = count($arr);
        $array_final = json_encode($arr, JSON_NUMERIC_CHECK);

        // 6. Attendance record
        $attendance = Attendance::where(['date' => $dates, 'mobileuser_id' => $id])->first();
        if ($attendance) {
            $attendance->totalkm_odometer = number_format($attendance->checkoutkm - $attendance->checkinkm, 2);
            $attendance->totalkm_gps = number_format($this->calculateGpsDistanceKm($currentLoc), 2);
        }

        // 7. Device logs
        $deviceLogs = null;
        $devicelogModel = DeviceLog::where('mobileuser_id', $id)
            ->whereDate('created_at', $dates)
            ->first();
        if ($devicelogModel) {
            $deviceLogs = json_decode($devicelogModel->device_info, true);
        }

        // 8. Dealers and visits
        $dealers = Dealer::whereIn('area_code', $areas)
            ->where('active_status', 'Active')
            ->get();

        $visits = Session::with(['visits.purposes'])   // eager load purposes (if needed)
            ->where('user_id', $id)
            ->where('date', $dates)
            ->where('status', '!=', 'Unlisted')
            ->get();

        // 9. Prepare view data
        $data = [
            'userinfo'        => $userinfo,
            'currentLoc'      => $array_final,
            'rol'             => $rol,
            'areas'           => $areasList,
            'date'            => $dateInput,          // original input date (for display)
            'count'           => $count,
            'dealers'         => $dealers,
            'visits'          => $visits,
            'checkinaddress'  => $checkinaddress,
            'lastlocation'    => $lastlocation,
            'last_synced_on'  => $last_synced_on,
            'attendance'      => $attendance,
            'deviceLogs'      => $deviceLogs,
            'gps_distance_km' => $attendance ? $attendance->totalkm_gps : null,
        ];

        return view('dashboards.map.singleRepresentative', $data);
    }

    // public function individual(Request $request, $id, $date)
    // {
    //     if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/", $date)) {
    //         return redirect('/dashboard');
    //     }

    //     $userinfo = Mobileuser::findorFail($id);
    //     $rol = DB::table('roles')->where('id', $userinfo->role_id)->get();
    //     $areas = explode(' :: ', $userinfo->areas);

    //     foreach ($areas as $pur) {
    //         $area[] = Area::where('areacode', $pur)->pluck('area')->first();
    //     }

    //     $list[] = array(
    //         "areas" => $area,
    //     );
    //     $area = null;

    //     $dat = $request->date;

    //     if (!empty($dat)) {
    //         $dates = $this->custom_BS_to_AD($dat);
    //     } else {
    //         $dates = date('Y-m-d');
    //     }
    //     $arr = array();
    //     $currentLoc = Location::where('day', $dates)
    //         ->where('user_id', $id)
    //         ->get();

    //     $arr = [];
    //     $minTimeData = null;
    //     $maxTimeData = null;
    //     if ($currentLoc->isNotEmpty()) {
    //         foreach ($currentLoc as $loc) {
    //             if (empty($loc->coordinates)) {
    //                 continue;
    //             }
    //             foreach ($loc->coordinates as $latData) {
    //                 $arr[] = (object) [
    //                     'address' => [
    //                         'lat' => $latData['latitude'],
    //                         'lng' => $latData['longitude'],
    //                     ],
    //                     'title' => $latData['time'],
    //                 ];
    //                 if ($minTimeData === null || strtotime($latData['time']) < strtotime($minTimeData['time'])) {
    //                     $minTimeData = $latData;
    //                 }

    //                 if ($maxTimeData === null || strtotime($latData['time']) > strtotime($maxTimeData['time'])) {
    //                     $maxTimeData = $latData;
    //                 }
    //             }
    //         }
    //     }


    //     if (isset($maxTimeData)) {
    //         $last_synced_on = $maxTimeData['time'];
    //         $data['maxTimeData'] = $maxTimeData;
    //         $lat = $maxTimeData['latitude'];
    //         $long = $maxTimeData['longitude'];
    //         $lastlocation = $this->getAddress($lat, $long);

    //         $lat = $minTimeData['latitude'];
    //         $long = $minTimeData['longitude'];
    //         $checkinaddress = $this->getAddress($lat, $long);
    //     }

    //     $data['lastlocation'] = $lastlocation;
    //     $data['checkinaddress'] = $checkinaddress;
    //     $attendance = Attendance::where(['date' => $dates, 'mobileuser_id' => $id])->first();
    //     usort($arr, function ($a, $b) {
    //         $timeA = strtotime($a->title);
    //         $timeB = strtotime($b->title);
    //         return $timeA <=> $timeB;
    //     });
    //     $count = count($arr);
    //     $array_final = json_encode($arr, JSON_NUMERIC_CHECK);


    //     $history = DB::table('visits')->where('user_id', $id)->where('date', $dat)->get();

    //     if (empty($history)) {
    //         echo "No Data Found";
    //     }

    //     $devicelogModel = DeviceLog::where('mobileuser_id', $id)->whereDate('created_at', $dates)->first();
    //     if (isset($devicelogModel) && !empty($devicelogModel)) {
    //         $data['deviceLogs'] = json_decode($devicelogModel->device_info, true);
    //     }
    //     $dealers = Dealer::whereIn('area_code', $areas)->where('active_status', 'Active')->get(); //Dealer::where('active_status','Active')->get();
    //     $data['last_synced_on'] = $last_synced_on;

    //     $gpsDistanceKm = $this->calculateGpsDistanceKm($currentLoc);

    //     $data['attendance'] = $attendance;
    //     if ($attendance) {
    //         $data['attendance']->totalkm_odometer = number_format($attendance->checkoutkm - $attendance->checkinkm, 2);
    //         $data['attendance']->totalkm_gps = number_format($gpsDistanceKm, 2);
    //     }
    //     $data['gps_distance_km'] = $gpsDistanceKm;

    //     $data['userinfo'] = $userinfo;
    //     $data['currentLoc'] = $array_final;
    //     $data['rol'] = $rol;
    //     $data['areas'] = $list;
    //     $data['date'] = $date;
    //     $data['count'] = $count;
    //     $data['dealers'] = $dealers;
    //     $data['visits'] = $visit = Session::with(['visits.purposes'])->where(['user_id' => $id, 'date' => $dates, ['status', '!=', 'Unlisted']])->get();

    //     // return $data;
    //     return view('dashboards.map.singleRepresentative', $data);
    // }

    /////////////////////////////////
    private function calculateGpsDistanceKm($currentLoc): float
    {
        $jitterThresholdMeters = 8;   // ignore GPS noise while stationary
        $maxPlausibleSpeedKmh = 180;  // reject bad-fix "teleport" segments

        $allPoints = collect($currentLoc)->flatMap(function ($loc) {
            $coords = $loc->coordinates;
            if (is_string($coords)) {
                $coords = json_decode($coords, true) ?? [];
            }
            return $coords ?? [];
        });

        $clean = $allPoints
            ->filter(fn($p) => isset($p['latitude'], $p['longitude'], $p['time']))
            ->unique(fn($p) => $p['latitude'] . ',' . $p['longitude'] . ',' . $p['time']) // drops exact dupes
            ->sortBy(fn($p) => strtotime($p['time']))
            ->values();

        $totalMeters = 0.0;
        $prev = null;

        foreach ($clean as $curr) {
            if ($prev !== null) {
                $segmentMeters = $this->haversineMeters(
                    (float) $prev['latitude'],
                    (float) $prev['longitude'],
                    (float) $curr['latitude'],
                    (float) $curr['longitude']
                );

                $seconds = max(strtotime($curr['time']) - strtotime($prev['time']), 1);
                $speedKmh = ($segmentMeters / 1000) / ($seconds / 3600);

                if ($segmentMeters >= $jitterThresholdMeters && $speedKmh <= $maxPlausibleSpeedKmh) {
                    $totalMeters += $segmentMeters;
                }
            }
            $prev = $curr;
        }

        return round($totalMeters / 1000, 3); // km
    }

    private function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
    ////////////////////////////////

    public function ind(Request $request, $id)
    {
        $ldate = date('Y-m-d');
        $currentNepaliDay = $this->AD_to_BS(date('Y'), date('m'), date('d'));
        $nepaliDay = $currentNepaliDay['date'];
        $nepaliMth = $currentNepaliDay['month'];
        if (strlen($nepaliDay) == 1) {
            $nepaliDay = '0' . $nepaliDay;
        }
        if (strlen($nepaliMth) == 1) {
            $nepaliMth = '0' . $nepaliMth;
        }
        $nepaliYr = $currentNepaliDay['year'];
        $today = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;

        $dat = $request->date;
        $d = explode('-', $dat);
        // $oldDateUnix = strtotime($date);
        $engDay = $this->BS_to_AD($d[0], $d[1], $d[2]);
        $nepaliDay = $engDay['date'];
        $nepaliMth = $engDay['month'];
        if (strlen($nepaliDay) == 1) {
            $nepaliDay = '0' . $nepaliDay;
        }
        if (strlen($nepaliMth) == 1) {
            $nepaliMth = '0' . $nepaliMth;
        }
        $nepaliYr = $engDay['year'];
        $dates = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;



        $rol = DB::table('roles')->get();
        $userinfo = Mobileuser::find($id);

        $areas = explode(' :: ', $userinfo->areas);
        foreach ($areas as $pur) {
            $area[] = Area::where('areacode', $pur)->pluck('area')->first();
            // $area[] = json_decode(Area::where('areacode', $pur)->pluck('area'),true)[0];
        }

        $list[] = array(
            "areas" => $area,
        );
        $area = null;

        $dat = $request['date'];

        $arr = array();

        $currentLoc = Location::where('day', $dates)->where('user_id', $id)->get();
        $lastlocation = '';
        $last_synced_on = '';
        $checkinaddress = '';
        if (count($currentLoc) > 0) {
            // return 1;
            $startdate = $currentLoc[0]['coordinates'][0];
            foreach ($currentLoc as $loc) {
                $new = $loc['coordinates'];

                if (!empty($new)) {
                    foreach ($new as $latData) {
                        $newArr = array("address" => array("lat" => $latData['latitude'], "lng" => $latData['longitude'],), 'title' => $latData['time']);
                        $arr[] = ((object) $newArr);

                        $lastdata = $latData;
                    }
                }
            }
        }
        if (isset($lastdata)) {
            $last_synced_on = $lastdata['time'];
            $data['lastdata'] = $lastdata;
            $lat = $lastdata['latitude'];
            $long = $lastdata['longitude'];
            $lastlocation = $this->getAddress($lat, $long);

            $lat = $startdate['latitude'];
            $long = $startdate['longitude'];
            $checkinaddress = $this->getAddress($lat, $long);
        }
        $data['lastlocation'] = $lastlocation;
        $data['checkinaddress'] = $checkinaddress;
        $attendance = Attendance::where(['date' => $dates, 'mobileuser_id' => $id])->first();
        $count = count($arr);
        $array_final = json_encode($arr, JSON_NUMERIC_CHECK);

        $history = DB::table('visits')->where('user_id', $id)->where('date', $dat)->get();

        $dealers = Dealer::whereIn('area_code', $areas)->where('active_status', 'Active')->get();
        $visits = Session::where(['user_id' => $id, 'date' => $dates, ['status', '!=', 'Unlisted']])->get();
        $devicelogModel = DeviceLog::where('mobileuser_id', $id)->whereDate('created_at', $dates)->first();
        if (isset($devicelogModel) && !empty($devicelogModel)) {
            $deviceLogs = json_decode($devicelogModel->device_info, true);
        }

        return view('dashboards.map.singleRepresentative', [
            'userinfo' => $userinfo,
            'currentLoc' => $array_final,
            'visits' => $visits,
            'rol' => $rol,
            'areas' => $list,
            'date' => $request['date'],
            'count' => $count,
            'dealers' => $dealers,
            'checkinaddress' => $checkinaddress,
            'lastlocation' => $lastlocation,
            'last_synced_on' => $last_synced_on,
            'attendance' => $attendance,
            'deviceLogs' => $deviceLogs ?? null
        ]);
        // return view('dashboards.map.singleRepresentative', ['userinfo' => $userinfo, 'currentLoc' => $array_final, 'rol' => $rol, 'areas' => $list, 'date' => $request['date'], 'dealers' => $dealers, 'visits' => $visits]);
    }

    public function userLocation(Request $request, $id)
    {
        $request->validate([
            'deviceLog' => 'required|string',
        ]);
        $mobileuser = Mobileuser::findOrFail($id);

        $data['mobileuser'] = $mobileuser;
        $data['deviceLog'] = json_decode($request->deviceLog);

        return view('dashboards.mobileusers.map', $data);
    }

    private function getAddress($lat, $long)
    {
        $geocode = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false&key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&v=3";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geocode);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($response);
        // return response()->json($output);
        foreach ($output->results as $result) {
            if ($result->types[0] == "route") {
                // dd($result);
                return $result->formatted_address;
            }
        }
    }
}

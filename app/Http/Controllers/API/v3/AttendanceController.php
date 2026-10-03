<?php

namespace App\Http\Controllers\API\v3;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\AttendanceStatus;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $rules = array(
            'latitude' => 'required_if:flag,==,checkin|required_if:flag,==,checkout',
            'longitude' => 'required_if:flag,==,checkin|required_if:flag,==,checkout',
            // 'address' => 'required_if:flag,==,checkin|required_if:flag,==,checkout',
            'flag' => 'required|in:checkin,breakstart,breakend,checkout',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s'
        );
        $valid = Validator::make($request->all(), $rules);

        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $user = Auth::user();
        $date = $request->date;
        $attendance = Attendance::where(['mobileuser_id'=>$user->id,'date'=>$date])->first();

        if(!$attendance){
            $attendance = Attendance::create([
                'mobileuser_id' => $user->id,
                'checkin_date' => $request->time,
                'date' => $request->date,
                'status' => $request->flag,
                // 'address' => $request->address,
            ]);
        }
        $attendance_status = new AttendanceStatus;

        $attendance_status->attendance_id = $attendance->id;
        $attendance_status->mobileuser_id = $attendance->mobileuser_id;
        $attendance_status->date = $request->date;
        $attendance_status->time = $request->time;
        $attendance_status->status = $request->flag;
        // $attendance_status->address = $request->address;

        $attendance_status->save();
        if ($request->flag=="checkin"||$request->flag=="checkout") {
            $location = Location::where(['day' => date('Y-m-d'), 'user_id' => $user->id])->first();

            if (!$location) {
                $cordinates[] = array(
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'time' => date('H:i:s')
                );

                $location = new Location();

                $location->user_id = $user->id;
                $location->day = date('Y-m-d');
                $location->coordinates = $cordinates;
                $location->user_id = $user->id;
                $location->role_id = $user->role_id;

                $location->save();
            } else {
                $cordinates[] = array(
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'time' => date('H:i:s')
                );
                $location->coordinates = array_merge($location->coordinates, $cordinates);

                $location->save();
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Thank You.', 'flag' => $request->flag]);
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
    public function offline(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.flag' => 'required|in:checkin,checkout,breakstart,breakend',
            'data.*.date' => 'required|date_format:Y-m-d',
            'data.*.time' => 'required|date_format:H:i:s',
            'data.*.latitude' => 'required_if:data.*.flag,==,checkin|required_if:data.*.flag,==,checkout',
            'data.*.longitude' => 'required_if:data.*.flag,==,checkin|required_if:data.*.flag,==,checkout',
            // 'data.*.address' => 'required_if:data.*.flag,==,checkin|required_if:data.*.flag,==,checkout'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $date = Date('Y-m-d');
        $time = Date('H:i:s');
        $user = Auth::user();

        foreach ($request->data as $d) {
            $attendance = Attendance::where(['mobileuser_id'=>$user->id,'date'=>$d['date']])->first();
            if(!$attendance){
                $attendance = Attendance::create([
                'status' => $d['flag'],
                'mobileuser_id' => $user->id,
                'date' => $d['date'],
                'checkin_date' => $d['time'],
                // 'address' => (isset($d['address']) ? $d['address'] : ''),
                ]);
            }
            $attendance_status = new AttendanceStatus;

            $attendance_status->attendance_id = $attendance->id;
            $attendance_status->mobileuser_id = $attendance->mobileuser_id;
            $attendance_status->date = $d['date'];
            $attendance_status->time = $d['time'];
            $attendance_status->status = $d['flag'];
            // $attendance_status->address = $d['address'];

            $attendance_status->save();
            $new[] = array(
                'status' => $d['flag'],
                'mobileuser_id' => $user->id,
                'date' => $d['date'],
                'checkin_date' => $d['time'],
                // 'address' => (isset($d['address']) ? $d['address'] : ''),
                'created_at' => Date('Y-m-d H:s:i'),
                'created_at' => Date('Y-m-d H:s:i'),
            );
            // if ($d['address']) {
                $location = Location::where(['user_id' => $user->id, 'day' => $d['date']])->first();

                if (!$location) {
                    $cordinates[] = array(
                        'latitude' => $d['latitude'],
                        'longitude' => $d['longitude'],
                        'time' => $d['time']
                    );

                    $location = new Location();

                    $location->user_id = $user->id;
                    $location->day = $d['date'];
                    $location->coordinates = $cordinates;
                    $location->user_id = $user->id;
                    $location->role_id = $user->role_id;

                    $location->save();
                    $cordinates = [];
                } else {
                    $cordinates[] = array(
                        'latitude' => $d['latitude'],
                        'longitude' => $d['longitude'],
                        'time' => $d['time']
                    );
                    $location->coordinates = array_merge($location->coordinates, $cordinates);

                    $location->save();
                    $cordinates = [];
                }
            }
        // }
        // Attendance::insert($new);
        return response()->json(['status' => 'success', 'message' => 'Thank You.']);
    }
}

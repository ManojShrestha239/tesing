<?php

namespace App\Http\Controllers\API\v4;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceImage;
use App\Models\AttendanceStatus;
use App\Models\Location;
use App\Models\Mobileuser;
use App\Traits\FCM;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\Nepali_Calendar;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    use Nepali_Calendar, FCM;

    function online(Request $request)
    {
        $rules = array(
            'latitude' => 'required_if:flag,==,checkin|required_if:flag,==,checkout',
            'longitude' => 'required_if:flag,==,checkin|required_if:flag,==,checkout',
            'flag' => 'required|in:checkin,breakstart,breakend,checkout',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'images' => 'required_if:flag,==,checkin|required_if:flag,==,checkout|mimes:jpg,jpeg,png',
        );
        $valid = Validator::make($request->all(), $rules);

        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        try {
            $user = Mobileuser::where('remember_token', substr($request->header("Authorization"), 7))->first();
            $date = $request->date;
            $attendance = Attendance::where(['mobileuser_id' => $user->id, 'date' => $date])->first();

            if (!$attendance) {
                $attendance = Attendance::create([
                    'mobileuser_id' => $user->id,
                    'checkin_date' => $request->time,
                    'date' => $request->date,
                    'status' => $request->flag,
                    'address' => null,
                ]);
            }
            $attendance_status = new AttendanceStatus();

            $attendance_status->attendance_id = $attendance->id;
            $attendance_status->mobileuser_id = $attendance->mobileuser_id;
            $attendance_status->date = $request->date;
            $attendance_status->time = $request->time;
            $attendance_status->status = $request->flag;
            $attendance_status->address = null;

            $attendance_status->save();

            //Attendance Image
            if (isset($request->images)) {

                $file = File::get($request->images);
                $filename = time() . '_' . $request->images->getClientOriginalName();
                Storage::disk('public')->put('attendance/' . $filename, $file);

                $attendance_image = new AttendanceImage();

                $attendance_image->attendance_id = $attendance->id;
                $attendance_image->attendancestatus_id = $attendance_status->id;
                $attendance_image->mobileuser_id = $attendance->mobileuser_id;
                $attendance_image->image = $filename;
                $attendance_image->date = $request->date;
                $attendance_image->time = $request->time;
                $attendance_image->status = $request->flag;

                $attendance_image->save();
            }
            if ($request->flag == "checkin" || $request->flag == "checkout") {
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
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function offline(Request $request)
    {
        $data = json_decode($request->data, true);

        $valid = Validator::make($data, [
            '*.flag' => 'required|in:checkin,checkout,breakstart,breakend',
            '*.date' => 'required|date_format:Y-m-d',
            '*.time' => 'required|date_format:H:i:s',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }

        try {
            $date = Date('Y-m-d');
            $time = Date('H:i:s');
            $user = Mobileuser::where('remember_token', substr($request->header("Authorization"), 7))->first();

            foreach ($data as $key => $d) {
                if ($d['flag'] == 'checkin' || $d['flag'] == 'checkout') {
                    $imagevalidation = Validator::make($request->all(), [
                        "image.$key" => 'nullable|mimes:jpg,jpeg,png,webp'
                    ]);
                    if ($imagevalidation->fails()) {
                        return response()->json(
                            ['status' => 'error', 'message' => $imagevalidation->errors()],
                            422
                        );
                    }
                }
                $attendance = Attendance::where(['mobileuser_id' => $user->id, 'date' => $d['date']])->first();
                if (!$attendance) {
                    $attendance = Attendance::create([
                        'status' => $d['flag'],
                        'mobileuser_id' => $user->id,
                        'date' => $d['date'],
                        'checkin_date' => $d['time'],
                        'address' => (isset($d['address']) ? $d['address'] : ''),
                    ]);
                }
                $attendance_status = new AttendanceStatus();

                $attendance_status->attendance_id = $attendance->id;
                $attendance_status->mobileuser_id = $attendance->mobileuser_id;
                $attendance_status->date = $d['date'];
                $attendance_status->time = $d['time'];
                $attendance_status->status = $d['flag'];
                $attendance_status->address = (isset($d['address']) ? $d['address'] : '');

                $attendance_status->save();


                //Attendance Image
                if (isset($request->image[$key])) {

                    $file = File::get($request->image[$key]);
                    $filename = time() . '_' . $request->image[$key]->getClientOriginalName();
                    Storage::disk('public')->put('attendance/' . $filename, $file);

                    $attendance_image = new AttendanceImage();

                    $attendance_image->attendance_id = $attendance->id;
                    $attendance_image->attendancestatus_id = $attendance_status->id;
                    $attendance_image->mobileuser_id = $attendance->mobileuser_id;
                    $attendance_image->image = $filename;
                    $attendance_image->date = $d['date'];
                    $attendance_image->time = $d['time'];
                    $attendance_image->status = $d['flag'];

                    $attendance_image->save();
                    // }
                }


                $new[] = array(
                    'status' => $d['flag'],
                    'mobileuser_id' => $user->id,
                    'date' => $d['date'],
                    'checkin_date' => $d['time'],
                    'address' => (isset($d['address']) ? $d['address'] : ''),
                    'created_at' => Date('Y-m-d H:s:i'),
                    'created_at' => Date('Y-m-d H:s:i'),
                );
                if ((isset($d['address']) ? $d['address'] : '')) {
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
            }
            Attendance::insert($new);
            return response()->json(['status' => 'success', 'message' => 'Thank You.']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // adding fcm to senior
    public function onlinewithkm(Request $request)
    {
        $rules = array(
            'flag' => 'required|in:checkin,breakstart,breakend,checkout',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'images' => 'required_if:flag,checkin,checkout|mimes:jpg,jpeg,png',
            'km' => 'nullable|required_if:flag,checkin,checkout|min:0|numeric',
            'visit_method' => 'nullable|required_if:flag,checkin,checkout|in:Combined_Tour,Meeting,Single,Leave',
            'device_model' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',

        );
        $valid = Validator::make($request->all(), $rules);

        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $date = $request->date;

            $lastStatus = $user->todayLatestAttendance ? strtolower($user->todayLatestAttendance->status) : null;
            $currentFlag = strtolower($request->flag);

            $errorMessageForSameStatus = [
                'checkin'    => 'You are already checked in. Please check out before attempting to check in again.',
                'checkout'   => 'You are not currently checked in. Please check in before attempting to check out.',
                'breakstart' => 'You are already on a break. Please end your current break before starting a new one.',
                'breakend'   => 'You are not currently on a break. Please start a break before attempting to end it.'
            ];

            if ($lastStatus === $currentFlag) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessageForSameStatus[$currentFlag] ?? 'Invalid status.'
                ], 400);
            }

            if ($lastStatus === 'breakstart' && $currentFlag !== 'breakend') {
                return response()->json([
                    'status' => 'error',
                    'message' => "You are currently on a break. Please end the ongoing break before proceeding with a new {$request->flag}."
                ], 400);
            }

            $attendance = Attendance::with('lastStatus')
                ->where(['mobileuser_id' => $user->id, 'date' => $date])->first();

            $lastStatus = $attendance ? $attendance->lastStatus : null;

            if (!$attendance) {
                $attendance = Attendance::create([
                    'mobileuser_id' => $user->id,
                    'checkin_date' => $request->time,
                    'date' => $request->date,
                    'status' => $currentFlag,
                    'address' => null,
                    'checkinkm' => $request->km,
                    'visit_method' => $request->visit_method,
                    'device_model' => $request->device_model,
                    'remarks' => $request->remarks,
                ]);
            } else {
                if ($currentFlag == 'checkin' || $currentFlag == 'checkout') {
                    $attendance->checkoutkm = $request->km;
                }
                $attendance->status = $currentFlag;
                $attendance->device_model = $request->device_model;
                $attendance->remarks = $request->remarks;
                $attendance->save();
            }
            $attendance_status = new AttendanceStatus();

            $attendance_status->attendance_id = $attendance->id;
            $attendance_status->mobileuser_id = $attendance->mobileuser_id;
            $attendance_status->date = $request->date;
            $attendance_status->time = $request->time;
            $attendance_status->status = $currentFlag;
            $attendance_status->address = null;
            $attendance_status->km = $request->km;
            $attendance_status->visit_method = $request->visit_method;
            $attendance_status->device_model = $request->device_model;
            $attendance_status->remarks = $request->remarks;


            $attendance_status->save();

            //Attendance Image
            if (isset($request->images)) {
                $file = File::get($request->images);
                $filename = time() . '_' . $request->images->getClientOriginalName();
                Storage::disk('public')->put('attendance/' . $filename, $file);

                $attendance_image = new AttendanceImage();

                $attendance_image->attendance_id = $attendance->id;
                $attendance_image->attendancestatus_id = $attendance_status->id;
                $attendance_image->mobileuser_id = $attendance->mobileuser_id;
                $attendance_image->image = $filename;
                $attendance_image->date = $request->date;
                $attendance_image->time = $request->time;
                $attendance_image->status = $currentFlag;

                $attendance_image->save();
                // }
            }
            if ($currentFlag == "checkin" || $currentFlag == "checkout") {
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

            if ($currentFlag == 'checkin' || $currentFlag == 'checkout') {
                $this->sendFCMSenior($user, $currentFlag, $request->time);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Thank You.', 'flag' => $currentFlag]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAttendance(Request $request)
    {
        $request->validate([
            'year' => 'required|digits:4',
            'month' => 'required|digits_between:1,2|min:1|max:12|gt:0',
        ]);

        try {
            $year = $request->year;
            $month = $request->month;
            $totalDaysInMonth = $this->get_nepali_month_range($year, $month);
            $startDate = $this->BS_to_AD_YMD($year, $month, 1);
            $endDate = $this->BS_to_AD_YMD($year, $month, $totalDaysInMonth);

            $auth = Auth::user();
            if ($startDate != false || $endDate != false) {
                $attendance = Attendance::with('attendance_status')
                    ->where('mobileuser_id', $auth->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderBy('id', 'desc')->get();
            } else {
                $attendance = Attendance::with('attendance_status')
                    ->where('mobileuser_id', $auth->id)
                    ->orderBy('id', 'desc')->get();
            }
            return response()->json(['status' => 'success', 'data' => $attendance]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'message' => $e->getMessage()], 500);
        }
    }

    private function sendFCMSenior(Mobileuser $user, $status, $time): void
    {
        $user->loadMissing(['teamLeaders:id,name,fcm_id']);

        $tokens = $user->teamLeaders
            ->pluck('fcm_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return;
        }

        $dateTime = date('g:i A', strtotime($time));

        $title = 'Team Attendance Update';
        $body  = "{$user->name} just {$status} at {$dateTime}";
        $data  = [
            'route_channel' => 'my-team',
            'user_id' => $user->id
        ];

        // One OAuth fetch, N device sends, aggregated report
        $report = $this->sendMulticast($tokens, $title, $body, $data);

        // Clean up dead tokens
        if (! empty($report['invalid_tokens'])) {
            Mobileuser::whereIn('fcm_id', $report['invalid_tokens'])
                ->update(['fcm_id' => null]);
        }
    }
}

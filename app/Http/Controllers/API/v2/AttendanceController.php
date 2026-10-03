<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class AttendanceController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index(Request $request)
    {
        $userId = Auth::id();

        // ✅ Validate inputs early
        $validated = $request->validate([
            'year'  => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year  = $validated['year'] ?? null;
        $month = $validated['month'] ?? null;

        // ✅ Common eager loads
        $with = [
            'attendance_status:id,attendance_id,date,time,status,km,visit_method,remarks',
            'attendance_status.image:attendancestatus_id,image'
        ];

        // =========================
        // 📅 Monthly Data
        // =========================
        if ($year && $month) {

            $month = str_pad($month, 2, '0', STR_PAD_LEFT);

            $startDateAD = $this->custom_BS_to_AD("$year-$month-01");
            $endDateAD   = $this->custom_BS_to_AD("$year-$month-32");

            // ✅ Fetch attendances (Added orderBy to sort latest date to the top)
            $attendances = Attendance::with($with)
                ->where('mobileuser_id', $userId)
                ->whereBetween('date', [$startDateAD, $endDateAD])
                ->select([
                    'id',
                    'mobileuser_id',
                    'date',
                    'checkin_date',
                    'status',
                    'visit_method',
                    'totalkm',
                    'remarks'
                ])
                ->orderBy('date', 'DESC') // ⚡ Order by descending date here
                ->get();

            // ✅ Optimized status fetch (no full collection ops)
            $statuses = AttendanceStatus::select('date', 'status')
                ->where('mobileuser_id', $userId)
                ->whereBetween('date', [$startDateAD, $endDateAD])
                ->latest('id')
                ->orderBy('date', 'DESC')
                ->get()
                ->unique('date') // still ok, but lighter dataset
                ->keyBy('date');

            // ✅ Transform (no mutation side-effects)
            $data = $attendances->map(function ($attendance) use ($statuses) {

                $originalDate = $attendance->date;

                return [
                    ...$attendance->toArray(),
                    'status' => $statuses[$originalDate]->status ?? $attendance->status,
                    'date'   => $this->custom_AD_to_BS($originalDate),
                ];
            });

            return response()->json([
                'status' => 'Success',
                'data'   => $data
            ]);
        }

        // =========================
        // 📍 Today’s Latest Record
        // =========================
        $today = now()->toDateString();

        $attend = Attendance::with($with)
            ->where('mobileuser_id', $userId)
            ->whereDate('date', $today)
            ->latest('id')
            ->first();

        if (!$attend) {
            return response()->json([
                'status' => 'Success',
                'data'   => null
            ]);
        }

        // ✅ Single optimized query
        $latestStatus = AttendanceStatus::select('status')
            ->where('mobileuser_id', $userId)
            ->whereDate('date', $today)
            ->latest('id')
            ->orderBy('date', 'DESC')
            ->value('status'); // ⚡ no model hydration

        // ✅ Fixed spread operator so it merges perfectly
        $data = [
            ...$attend->toArray(),
            'status' => $latestStatus ?? $attend->status,
            'date'   => $this->custom_AD_to_BS($attend->date),
        ];

        return response()->json([
            'status' => 'Success',
            'data'   => $data
        ]);
    }

    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'flag' => 'required|in:checkin,checkout,breakstart,breakend',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $date = Date('Y-m-d');
        $time = Date('H:i:s');
        $user = $user =  Auth::user();
        $attendance = Attendance::where(['mobileuser_id' => $user->id, 'date' => $date])->first();
        if (!$attendance) {
            $attendance = Attendance::create([
                'mobileuser_id' => $user->id,
                'checkin_date' => $request->time,
                'date' => $request->date,
                'status' => $request->flag
            ]);
        }
        $attendance_status = new AttendanceStatus;

        $attendance_status->attendance_id = $attendance->id;
        $attendance_status->mobileuser_id = $attendance->mobileuser_id;
        $attendance_status->date = $request->date;
        $attendance_status->time = $request->time;
        $attendance_status->status = $request->flag;

        $attendance_status->save();

        return response()->json(['status' => 'success', 'message' => 'Thank You.', 'flag' => $request->flag]);
    }

    public function offline(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.flag' => 'required|in:checkin,checkout,breakstart,breakend',
            'data.*.date' => 'required|date_format:Y-m-d',
            'data.*.time' => 'required|date_format:H:i:s',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }

        $user = Auth::user();

        foreach ($request->data as $d) {
            $attendance = Attendance::where(['mobileuser_id' => $user->id, 'date' => $d['date']])->first();
            if (!$attendance) {
                $attendance = Attendance::create([
                    'mobileuser_id' => $user->id,
                    'checkin_date' => $d['time'],
                    'date' => $d['date'],
                    'status' => $d['flag']
                ]);
            }
            $attendance_status = new AttendanceStatus;

            $attendance_status->attendance_id = $attendance->id;
            $attendance_status->mobileuser_id = $attendance->mobileuser_id;
            $attendance_status->date = $d['date'];
            $attendance_status->time = $d['time'];
            $attendance_status->status = $d['flag'];

            $attendance_status->save();
        }
        // Attendance::insert($new);
        return response()->json(['status' => 'success', 'message' => 'Thank You.']);
    }

    private function calculate_distance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
    {
        $theta = $lon1 - $lon2;

        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            cos(deg2rad($theta));

        $dist = rad2deg(acos($dist));
        $miles = $dist * 60 * 1.1515;

        switch (strtoupper($unit)) {
            case 'K':
                return $miles * 1.609344;
            case 'N':
                return $miles * 0.8684;
            case 'MT':
                return $miles * 1609.344;
            default:
                return $miles;
        }
    }
}

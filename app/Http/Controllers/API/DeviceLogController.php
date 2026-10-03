<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceLog;
use App\Traits\Geocodable;
use Illuminate\Http\Request;

class DeviceLogController extends Controller
{
    use Geocodable;

    public function store(Request $request)
    {
        $request->validate([
            '*.wifi_status' => 'required|in:0,1',
            '*.gps_status' => 'required|in:0,1',
            '*.mobile_data_status' => 'required|in:0,1',
            '*.is_battery_optimized' => 'required|in:0,1',
            '*.is_gps_mocked' => 'nullable|in:0,1',
            '*.latitude' => 'nullable|numeric',
            '*.longitude' => 'nullable|numeric',
            '*.logged_at' => 'required|integer',
        ]);

        $auth = auth()->user();
        $authId = $auth->id;

        $deviceLogs = [];

        $sec = $request->all()[0]['logged_at'] / 1000;

        $date = date('Y-m-d', $sec);

        foreach ($request->all() as $log) {
            $deviceLogs[] = [
                'wifi_status' => $log['wifi_status'],
                'gps_status' => $log['gps_status'],
                'mobile_data_status' => $log['mobile_data_status'],
                'is_battery_optimized' => $log['is_battery_optimized'],
                'is_gps_mocked' => $log['is_gps_mocked'] ?? 0,
                'latitude' => (float) ($log['latitude'] ?? null),
                'longitude' => (float) ($log['longitude'] ?? null),
                'location' => null,
                'time' => date('H:i:s', $log['logged_at'] / 1000),
                'logged_at' => date('Y-m-d H:i:s', $log['logged_at'] / 1000),
            ];
        }

        $deviceLog = DeviceLog::where('mobileuser_id', $authId)
            ->where('date', $date)
            ->first();

        if (!$deviceLog) {
            DeviceLog::create([
                'mobileuser_id' => $authId,
                'date' => $date,
                'device_info' => json_encode($deviceLogs),
            ]);
            $responseLogs = $deviceLogs;
        } else {
            $existingLogs = json_decode($deviceLog->device_info, true);
            if (!is_array($existingLogs)) {
                $existingLogs = empty($existingLogs) ? [] : [$existingLogs];
            }
            $mergedLogs = array_merge($existingLogs, $deviceLogs);
            $deviceLog->update([
                'device_info' => json_encode($mergedLogs),
            ]);
            $responseLogs = $mergedLogs;
        }

        return response()->json([
            'message' => 'Device logs stored successfully',
            'data' => $responseLogs,
        ], 201);
    }
}

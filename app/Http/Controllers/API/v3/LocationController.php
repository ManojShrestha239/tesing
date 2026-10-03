<?php

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Mobileuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $payload = $request->all();
        if (!is_array($payload) || empty($payload)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data format'], 422);
        }

        $validator = Validator::make($payload, [
            '*' => 'required|array',
            '*.user_id' => 'required|exists:mobileusers,id',
            '*.day' => 'required|date_format:Y-m-d',
            '*.coordinates' => 'required|array',
            '*.coordinates.*.longitude' => 'required|numeric',
            '*.coordinates.*.latitude' => 'required|numeric',
            '*.coordinates.*.time' => 'required|date_format:H:i:s',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $auth = auth('api')->user();

        if ($auth->is_tracking_enabled === false) {
            return response()->json(['status' => 'error', 'message' => 'Tracking is disabled for this user'], 403);
        }

        DB::beginTransaction();
        try {
            $userIds = collect($payload)->pluck('user_id')->unique()->toArray();
            $days = collect($payload)->pluck('day')->unique()->toArray();
            $existing = Location::whereIn('user_id', $userIds)->whereIn('day', $days)->get()->keyBy(function ($item) {
                return $item->user_id . '_' . $item->day;
            });

            foreach ($payload as $data) {
                $key = $data['user_id'] . '_' . $data['day'];
                $coordinates = $data['coordinates'];
                if (isset($existing[$key])) {
                    $locate = $existing[$key];
                    $oldCoords = is_array($locate->coordinates) ? $locate->coordinates : json_decode($locate->coordinates, true);
                    $locate->coordinates = array_merge($oldCoords ?? [], $coordinates);
                } else {
                    $locate = new Location();
                    $locate->user_id = $data['user_id'];
                    $locate->day = $data['day'];
                    $locate->role_id = Mobileuser::find($data['user_id'])->role_id;
                    $locate->coordinates = $coordinates;
                }
                $locate->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
        return response()->json(['status' => 'success', 'error code' => '200', 'message' => 'Added'], 200);
    }
}

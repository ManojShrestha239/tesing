<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\UnlistedDealer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DealerController extends Controller
{
    public function index()
    {
        $dealer = Dealer::where('active_status', 'Active')->formaindealer()->get();
        return response()->json($dealer);
    }

    public function update(Request $request, $code)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $dealer = Dealer::find($code);
        if (!$dealer) {
            return response()->json(['status' => 'error', 'error_code' => '404', 'message' => 'Dealer not found'], 404);
        }

        if (in_array($dealer->status, ['pending', 'not-approved'])) {
            $dealer->latitude = $request->latitude;
            $dealer->longitude = $request->longitude;
            $dealer->status = 'approved';
            $dealer->save();
            return response()->json(['status' => 'success', 'error_code' => '200', 'message' => 'Updated']);
        }

        return response()->json(['status' => 'error', 'error_code' => '401', 'message' => 'Already Updated']);
    }

    public function dealerWithboundry(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'isRetailer' => 'sometimes',
        ]);

        $lat  = $request->latitude;
        $lng  = $request->longitude;

        $subAreas = array_filter(
            explode(' :: ', Auth::user()->sub_areas ?? '')
        );

        if (empty($subAreas)) {
            return response()->json([]);
        }

        $distanceKm = optional(DB::table('settings')->first())->distance ?? 50;
        $distanceKm = $distanceKm / 1000;

        $haversine = "
                        (6371 * 2 * ASIN(
                            SQRT(
                                POWER(SIN((? - latitude) * PI() / 180 / 2), 2) +
                                COS(? * PI() / 180) * COS(latitude * PI() / 180) *
                                POWER(SIN((? - longitude) * PI() / 180 / 2), 2)
                            )
                        ))
                    ";

        $dealers = Dealer::select(
            'code',
            'name',
            'address',
            'main_dealer_code',
            'latitude',
            'longitude',
            'email',
            'mobile',
            'phone'
        )
            ->selectRaw("$haversine AS distance", [$lat, $lat, $lng])
            ->whereIn('subarea', $subAreas)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active_status', 'Active')
            ->where('status', 'approved')
            ->having('distance', '<=', $distanceKm)
            ->orderBy('distance')
            ->get();

        return response()->json($dealers);
    }

    public function position(Request $request)
    {
        $user = Auth::user();
        $areas = array_filter(array_map('trim', explode('::', $user->areas ?? '')));
        $subareas = array_filter(array_map('trim', explode('::', $user->sub_areas ?? '')));

        if (empty($areas) || empty($subareas)) {
            return response()->json([]);
        }

        $dealers = Dealer::whereIn('area_code', $areas)
            ->whereIn('subarea', $subareas)
            // ->whereNull('latitude')
            // ->whereNull('longitude')
            ->where('status', 'not-approved')
            ->where('active_status', 'Active')
            ->select(['code', 'name', 'address', 'latitude', 'longitude', 'email', 'mobile', 'phone', 'area_code', 'subarea', 'status', 'active_status'])
            ->get();

        return response()->json($dealers);
    }

    public function reposition(Request $request)
    {
        $user = Auth::user();
        $areas = array_filter(array_map('trim', explode('::', $user->areas ?? '')));
        $subareas = array_filter(array_map('trim', explode('::', $user->sub_areas ?? '')));

        if (empty($areas) || empty($subareas)) {
            return response()->json([]);
        }

        $dealers = Dealer::whereIn('area_code', $areas)
            ->whereIn('subarea', $subareas)
            ->where('status', 'pending')
            ->where('active_status', 'Active')
            ->select(['code', 'name', 'address', 'latitude', 'longitude', 'email', 'mobile', 'phone', 'area_code', 'subarea', 'status', 'active_status'])
            ->get();

        return response()->json($dealers);
    }


    public function unlistedDealers()
    {
        $authUser = auth()->user();

        $area_code = explode(' :: ', $authUser->areas);
        $subarea = explode(' :: ', $authUser->sub_areas);

        $data['unlisted_dealer'] = UnlistedDealer::whereIn('area_code', $area_code)
            ->whereIn('subarea', $subarea)
            ->active()
            ->get();
        return response()->json([
            'status'  => 'success',
            'message' => 'Session created successfully.',
            'data'    => $data,
        ]);
    }
}

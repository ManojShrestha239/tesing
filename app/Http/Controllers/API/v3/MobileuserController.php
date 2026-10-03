<?php

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Purpose;
use App\Models\Mobileuser;
use App\Models\Area;
use App\Models\Dealer;
use App\Models\Subarea;
use App\Models\Denomination;
use App\Models\UnlistedDealer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MobileuserController extends Controller
{
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required',
            'device' => 'required',
            'fcm_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors(),
                'token' => null
            ], 422);
        }

        if (strtolower(config('app.env')) === 'local') {
            $emails = ['info@bihanitech.com', 'info@postman.com', 'info@manoj.com', 'info@mobileuser.com'];
        } else {
            $emails = ['info@bihanitech.com'];
        }

        try {
            $mo = Mobileuser::with([
                'latestAttendance',
                'todayLatestAttendance',
            ])->where(['email' => $request->email])->first();
            $credentials = $request->only('email', 'password');
            $token = auth('api')->attempt($credentials);
            if (!$token || !$mo) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => '401',
                    'message' => 'Credentials do not match',
                    'token' => null
                ], 401);
            }

            if ($mo->status != 'Active') {
                return response()->json([
                    'status' => 'account_locked',
                    'error_code' => '423',
                    'message' => 'User account is locked or inactive.',
                    'token' => null
                ], 423);
            }

            $denomination = Denomination::orderBy('id', 'desc')->get();
            $todayAttendance = $mo->todayLatestAttendance ? $mo->todayLatestAttendance->status : null;
            $lastAttendance = $mo->latestAttendance ? $mo->latestAttendance->status : null;
            $banks = Bank::get();
            $purpose = Purpose::get();
            $area_code = explode(' :: ', $mo->areas);
            $subarea = explode(' :: ', $mo->sub_areas);
            $area = Area::with(['subarea' => function ($query) use ($subarea) {
                $query->whereIn('subarea', $subarea);
            }])->whereIn('areacode', $area_code)->get();
            $sub = Subarea::whereIn('subarea', $subarea)->get();
            $dealer = Dealer::where('active_status', 'Active')->formaindealer()
                ->whereIn('area_code', $area_code)
                ->whereIn('subarea', $subarea)
                ->get();
            $unlistedDealer = UnlistedDealer::whereIn('area_code', $area_code)
                ->whereIn('subarea', $subarea)
                ->active()
                ->get();
            foreach ($sub as $s) {
                foreach ($dealer as $d) {
                    if ($d->subarea == $s->subarea) {
                        $d->areastatus = 1;
                    }
                }
            }
            foreach ($dealer as $d) {
                if (!isset($d->areastatus)) {
                    $d->areastatus = 0;
                }
            }

            $topicFCM = $this->topicFCM();
            $packagePermissions = $this->packagePermissions($request);


            $makeResponse = function (
                $status,
                $code,
                $message,
                $user,
                $token,
                $area,
                $sub,
                $todayAttendance,
                $lastAttendance,
                $denomination,
                $banks,
                $purpose,
                $dealer,
                $unlistedDealer,
                $topicFCM,
                $packagePermissions
            ) {
                return response()->json([
                    'status' => $status,
                    'error_code' => $code,
                    'message' => $message,
                    'user' => $user,
                    'token' => $token,
                    'area' => $area,
                    'subarea' => $sub,
                    'attendance' => $todayAttendance,
                    'lastAttendance' => $lastAttendance,
                    'denomination' => $denomination,
                    'banks' => $banks,
                    'purpose' => $purpose,
                    'dealers' => $dealer,
                    'unlisted_dealer' => $unlistedDealer,
                    'topic_fcm' => $topicFCM,
                    'package_permissions' => $packagePermissions,
                ], (int)$code);
            };

            DB::beginTransaction();
            try {
                if (in_array($mo->email, $emails)) {
                    $mo->device = null;
                    $mo->fcm_id = $request->input('fcm_id');
                    $mo->remember_token = $token;
                    $mo->save();
                    DB::commit();
                    return $makeResponse(
                        'success',
                        200,
                        'Welcome',
                        $mo,
                        $token,
                        $area,
                        $sub,
                        $todayAttendance,
                        $lastAttendance,
                        $denomination,
                        $banks,
                        $purpose,
                        $dealer,
                        $unlistedDealer,
                        $topicFCM,
                        $packagePermissions
                    );
                }
                if ($mo->device == null) {
                    $mo->device = $request->device;
                    $mo->fcm_id = $request->fcm_id;
                    $mo->remember_token = $token;
                    $mo->save();
                    DB::commit();
                    return $makeResponse(
                        'success',
                        200,
                        'Welcome',
                        $mo,
                        $token,
                        $area,
                        $sub,
                        $todayAttendance,
                        $lastAttendance,
                        $denomination,
                        $banks,
                        $purpose,
                        $dealer,
                        $unlistedDealer,
                        $topicFCM,
                        $packagePermissions
                    );
                }
                if ($request->input('device') == $mo->device) {
                    $mo->fcm_id = $request->input('fcm_id');
                    $mo->remember_token = $token;
                    $mo->save();
                    DB::commit();
                    return $makeResponse(
                        'success',
                        200,
                        'Welcome',
                        $mo,
                        $token,
                        $area,
                        $sub,
                        $todayAttendance,
                        $lastAttendance,
                        $denomination,
                        $banks,
                        $purpose,
                        $dealer,
                        $unlistedDealer,
                        $topicFCM,
                        $packagePermissions
                    );
                }

                DB::rollBack();
                return $makeResponse(
                    'error',
                    403,
                    'wrong device',
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                );
            } catch (\Exception $e) {
                DB::rollBack();
                return $makeResponse(
                    'error',
                    500,
                    'Internal server error',
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                );
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired' => true], 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid' => true], 500);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], 500);
        }
    }

    private function topicFCM($userRole = null): array
    {
        $allProdTopic = config('app.prod-topic');
        $allDevTopic = config('app.dev-topic');

        return [
            $allProdTopic,
            $allDevTopic
        ];
    }

    private function packagePermissions(Request $request)
    {
        // Get appData set by CheckSubscriptionMiddleware
        $appData = $request->attributes->get('appData');

        return $mobileFeatures = $appData['mobile_features'] ?? null;
        // $maxMobileUsers = $appData['max_mobile_users'] ?? null;

        // return [
        //     // 'appData' => $appData,
        //     'mobile_features' => $mobileFeatures,
        //     'max_mobile_users' => $maxMobileUsers,
        // ];
    }
}

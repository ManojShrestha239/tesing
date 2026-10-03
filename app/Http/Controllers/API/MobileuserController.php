<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Bank;
use App\Models\Dealer;
use App\Models\Denomination;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use App\Models\Subarea;
use App\Models\UnlistedDealer;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class MobileuserController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    public function visitHistory(Request $request, $date)
    {
        $mouser = Auth::user();
        $userid = $mouser->id;

        $visithistory = Session::where(['user_id' => $userid, 'date' => $date])->orderBy('created_at', 'DESC')->get();

        foreach ($visithistory as $visited) {
            $purpose = $visited->purpose_id;


            $visited->dealer_code = ($visited->dealer_code) ? $visited->dealer_code : 'Unlisted Dealer';
            $visited->dealer_name = ($visited->dealer) ? $visited->dealer['name'] : $visited->dealer_name;
            $visited->dealer_address = ($visited->dealer) ? $visited->dealer['address'] : $visited->dealer_address;
            $visited->date = $this->custom_AD_to_BS($visited->date);
            $visited->purpose_id = $purpose;
            $visited->is_physical_visit = (int) ($visited->is_physical_visit ?? 1);
        }
        return response()->json($visithistory);
    }

    public function rangehistory(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'from' => 'required|date_format:Y-m-d',
            'to' => 'required|date_format:Y-m-d|after_or_equal:from'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'success', 'message' => $valid->errors()], 422);
        }
        $mouser = Auth::user();
        $userid = $mouser->id;

        $visithistory = Session::where(['user_id' => $userid])->whereBetween('date', [$request->from, $request->to])->orderBy('created_at', 'DESC')->get();

        foreach ($visithistory as $visited) {
            $purpose = $visited->purpose_id;

            $visited->dealer_code = ($visited->dealer_code) ? $visited->dealer_code : 'Unlisted Dealer';
            $visited->dealer_name = ($visited->dealer) ? $visited->dealer['name'] : $visited->dealer_name;
            $visited->dealer_address = ($visited->dealer) ? $visited->dealer['address'] : $visited->dealer_address;
            $visited->purpose_id = $purpose;
            $visited->date = $this->custom_AD_to_BS($visited->date);
            $visited->is_physical_visit = (int) ($visited->is_physical_visit ?? 1);
        }
        return response()->json($visithistory);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['status' => 'success', 'message' => 'Logout Successfully']);
    }

    public function home()
    {
        $authUser = auth()->user();
        $data['denomination'] = Denomination::orderBy('id', 'desc')->get();
        $data['banks'] = Bank::get();
        $data['purpose'] = Purpose::get();
        $area_code = explode(' :: ', $authUser->areas);
        $subarea = explode(' :: ', $authUser->sub_areas);
        $data['area'] = Area::with(['subarea' => function ($query) use ($subarea) {
            $query->whereIn('subarea', $subarea);
        }])->whereIn('areacode', $area_code)->get();
        $data['subarea'] = Subarea::whereIn('subarea', $subarea)->get();
        $data['dealers'] = Dealer::where('active_status', 'Active')->formaindealer()
            ->whereIn('area_code', $area_code)
            ->whereIn('subarea', $subarea)
            ->get();
        $data['attendance'] = $authUser->todayLatestAttendance ? $authUser->todayLatestAttendance->status : null;
        $data['lastAttendance'] = $authUser->latestAttendance ? $authUser->latestAttendance->status : null;
        $data['topic_fcm'] = $this->topicFCM();
        $data['package_permissions'] = $this->packagePermissions(request());
        $data['user'] = $authUser;
        $data['unlisted_dealer'] = UnlistedDealer::whereIn('area_code', $area_code)
            ->whereIn('subarea', $subarea)
            ->active()
            ->get();

        return response()->json([
            'status' => 'success',
            'error_code' => '200',
            'data' => $data
        ]);
    }

    private function topicFCM()
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

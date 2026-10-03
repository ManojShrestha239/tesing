<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purpose;
use App\Models\Dealer;
use App\Models\Session;
use App\Models\Schedulevisit;
use App\Models\Subarea;
use App\Models\Repeatedschedule;
use App\Models\SchedulePurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Illuminate\Support\Facades\Auth;

class MobileuserController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function visitSchedule(Request $request)
    {
        $mobileuser = Auth::user();
        $id = $mobileuser->id;

        $aWeekAgo = date('Y-m-d', strtotime('-7 days'));

        $schedulevisits = Schedulevisit::where('user_id', $id)->where('is_visit', 0)
            ->whereDate('date', '>=', $aWeekAgo)
            ->orderBy('date', 'desc')
            ->get();
        foreach ($schedulevisits as $sess) {
            $created_at = explode(" ", (string) $sess->created_at);
            $created_at = $this->custom_AD_to_BS($created_at[0]) . ' ' . $created_at[1];
            $sess->date = $this->custom_AD_to_BS($sess->date);
            $schedulePurpose = SchedulePurpose::where(['visitschedule_id' => $sess->id, 'status' => 'pending'])->pluck('purpose_id');
            $purposes = Purpose::whereIn('id', $schedulePurpose)->pluck('purpose');
            $purposes = implode(',', $purposes->toArray());
            $sess->purpose = $purposes;
            $sess->dealer_name = $sess->dealer['name'];
            $sess->created_at = $created_at;
            $sess->updated_at = (string) $sess->updated_at;
        }
        $day = date('l');
        $schedule = Repeatedschedule::select(
            'user_id',
            'day',
            'areacode',
            'subarea',
            'visited_dealer as dealer_code',
            'created_at',
            'updated_at'
        )->where('user_id', $id)
            ->where('day', $day)->get();

        foreach ($schedule as $s) {
            $created_at = explode(" ", (string) $s->created_at);
            $created_at = $this->custom_AD_to_BS($created_at[0]) . ' ' . $created_at[1];
            $s->subarea = Subarea::whereIn('subarea', explode(" :: ", $s->subarea))->get();

            $visiting_dealer = explode(" :: ", $s->dealer_code);
            $visited_dealer = Session::where(['date' => date('Y-m-d'), ['status', '!=', 'Unlisted'], 'user_id' => $id])->pluck('dealer_code');

            $remaining_dealer = array_diff($visiting_dealer, $visited_dealer->toArray());
            $s->dealer = Dealer::whereIn('code', $remaining_dealer)->get();
            $s->created_at = $created_at;
            $s->updated_at = (string) $s->updated_at;
        }

        return response()->json(['status' => 'success', 'schedule' => $schedulevisits, 'repeated' => $schedule]);
    }
    public function profile()
    {
        return response()->json(Auth::user());
    }
}

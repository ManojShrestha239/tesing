<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use Illuminate\Http\Request;
use App\Models\Schedulevisit;
use App\Models\Mobileuser;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use App\Models\Dealer;
use App\Models\Area;
use App\Models\SchedulePurpose;
use App\Traits\FCM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ScheduleVisitController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter, FCM;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = Schedulevisit::orderBy('created_at', 'DESC')->get();
        foreach ($schedules as $schedule) {
            $roles = explode('::', $schedule->purpose);
            $role = Purpose::where('id', $roles)->pluck('purpose')->toArray();
            $schedule->purpose = $role;
        }
        $data['schedules'] = $schedules;
        $data['users'] = Mobileuser::all();
        $data['mr'] = "";
        $data['date'] = "";

        return view('dashboards.schedules.schedules', $data);
    }

    public function create()
    {
        $data['mobiles'] = Mobileuser::all();
        $data['purpose'] = Purpose::all();
        $data['dealers'] = Dealer::all();
        return view('dashboards.schedules.bulkschedule', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'dealer' => 'required|array',
            'dealer.*' => 'required|exists:dealers,code',
            'user_id' => 'required',
            'purpose_id' => 'required|array',
            'purpose_id.*' => 'required|exists:purposes,id',
            'expiry_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required'],
            'remarks' => 'required'
        ]);
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($request['expiry_date'] < $today) {
            return redirect('/schedule/create')->with('status', 'The expiry date must be a date after or equal to today');
        }
        $b = collect($request['purpose_id']);
        $a = $b->implode('::');
        $dates = $this->custom_BS_to_AD($request->expiry_date);
        DB::beginTransaction();

        $dealerNames = [];

        try {
            foreach ($request->dealer as $dell) {
                $del = Dealer::findorFail($dell);
                $schedule = new Schedulevisit;
                $schedule->user_id = $request['user_id'];
                $schedule->dealer_code = $dell;
                $schedule->purpose_id = $a;
                $schedule->purpose = $a;
                $schedule->date = $dates;
                $schedule->remarks = $request['remarks'];
                $schedule->is_visit = 0;
                $schedule->save();
                foreach ($request->purpose_id as $purpose) {
                    $sch = new SchedulePurpose();

                    $sch->visitschedule_id = $schedule->id;
                    $sch->purpose_id  = $purpose;
                    $sch->status = "pending";

                    $sch->save();
                }

                $dealerNames[] = [$del->name];
            }

            $delNames = implode(", ", array_map(function ($item) {
                return $item[0];
            }, $dealerNames));

            $delNames = preg_replace('/,(?!.*,)/', ' and', $delNames);

            $user = Mobileuser::findorFail($request['user_id']);

            if ($user && $user->fcm_id) {
                $title = "Schedule to visit " . $delNames;
                $body  = "You are scheduled to visit " . $delNames . " on " . $this->custom_AD_to_BS($dates);

                $data = [
                    'route_channel' => 'schedule?schedule=' . $schedule->load('dealer'),
                ];

                $this->runFirebaseNotification($user->fcm_id, null, $title, $body, $data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('Status', 'Cannot add Visit Schedule Please Try again later' . $e->getMessage());
            return back()->withInput();
        }
        return redirect('/schedule')->with('Status', 'Schedule Visit Created.');
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
    public function edit($code)
    {
        $data['mobiles'] = Mobileuser::all();
        $data['purposes'] = Purpose::all();
        $schedule = Schedulevisit::with('schedulepurpose')->findorFail($code);
        $data['dealers'] = Dealer::whereIn('area_code', explode(" :: ", $schedule->user['areas']))->get();
        $data['schedules'] = $schedule;
        return view('dashboards.schedules.schedule-edit', $data);
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
        $this->validate($request, [
            'dealer_code' => 'required|exists:dealers,code',
            'user_id' => 'required|exists:mobileusers,id',
            'purpose_id' => 'required|array',
            'purpose_id.*' => 'required|exists:purposes,id',
            'expiry_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'required'],
        ]);


        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($request['expiry-date'] > $today) {

            return back()->with('status', 'The expiry-date must be a date after or equal to today');
        }
        $b = collect($request['purpose_id']);
        $a = $b->implode(' :: ');
        $dates = $this->custom_BS_to_AD($request['expiry_date']);
        $del = Dealer::findorFail($request['dealer_code']);

        $schedule = Schedulevisit::findorFail($id);
        $schedule->dealer_code = $request->input('dealer_code');
        $schedule->user_id = $request->input('user_id');
        $schedule->purpose_id = $a;
        $schedule->purpose = $a;
        $schedule->date = $dates;
        $schedule->remarks = $request->input('remarks');

        $schedule->save();
        SchedulePurpose::where('visitschedule_id', $schedule->id)->delete();
        foreach ($request->purpose_id as $purpose) {
            $sch = new SchedulePurpose();

            $sch->visitschedule_id = $schedule->id;
            $sch->purpose_id  = $purpose;
            $sch->status = "pending";

            $sch->save();
        }

        return redirect('/schedule')->with('Status', 'Schedule Visit Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule = Schedulevisit::findorFail($id);
        SchedulePurpose::where('visitschedule_id', $schedule->id)->delete();

        $schedule->delete();
        Session::flash('Status', 'Successfully Deleted');
        return back();
    }

    public function dealerValue($id)
    {
        $user =  Mobileuser::findorFail($id);
        $areacode = explode(' :: ', $user->areas);
        $subAreas = explode(' :: ', $user->sub_areas);
        $dealers = Dealer::select(['name', 'code'])->formaindealer()
            ->whereIn('area_code', $areacode)
            ->whereIn('subarea', $subAreas)
            ->get();
        return json_encode($dealers);
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/', 'nullable'],
        ]);
        $dd = $request['date'];
        $code = $request['user'];
        if (!empty($request['date'])) {
            $date = $this->custom_BS_to_AD($request->date);
        }
        $today = date('Y-m-d');
        if (!empty($request['date']) && !empty($request['user'])) {
            $schedules = Schedulevisit::where(['date' => $date, 'user_id' => $request->user])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif (empty($request['date']) && !empty($request['user'])) {
            $schedules = Schedulevisit::where(['user_id' => $request->user])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif (!empty($request['date']) && empty($request['user'])) {
            $schedules = Schedulevisit::where(['date' => $date])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $schedules = Schedulevisit::where(['date' => $today])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        foreach ($schedules as $schedule) {
            $roles = explode('::', $schedule->purpose);
            $role = Purpose::where('id', $roles)->pluck('purpose')->toArray();
            $schedule->purpose = $role;
        }
        if (!empty($code)) {
            $mrk_user = Mobileuser::where('id', $code)->first();
            $mrk = explode('::', $mrk_user->name);
            $mr = implode('', $mrk);
        } else {
            $mr = '';
        }
        $users = Mobileuser::all();
        $data['schedules'] = $schedules;
        $data['users'] = $users;
        $data['mr'] = $mr;
        $data['date'] = $dd;
        $data['user_id'] = $request->user;

        return view('dashboards.schedules.schedules', $data);
    }

    public function getarea($id)
    {
        $user = Mobileuser::findorFail($id);
        $areas = explode(' :: ', $user->areas);
        $area =  Area::whereIn('areacode', $areas)->get();

        return response()->json($area);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Subarea;
use App\Models\Dealer;
use App\Models\Repeatedschedule;
use App\Models\Mobileuser;
use App\Models\Purpose;
use App\Models\RepeatedDealerVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RepeatescheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedule = Repeatedschedule::orderBy('created_at', 'DESC')->get();

        foreach ($schedule as $sch) {
            $mobile = Mobileuser::where('id', $sch->user_id)->select('name')->first();
            $area = explode(' :: ', $sch->areacode);
            $area = Area::whereIn('areacode', $area)->get();
            $subarea = Subarea::whereIn('subarea', explode(' :: ', $sch->subarea))->get();
            $dealer = Dealer::whereIn('code', explode(' :: ', $sch->dealer_code))->get();
            // dd($dealer);
            $sch->user = $mobile;
            $sch->area = $area;
            $sch->subarea = $subarea;
            $sch->dealer = $dealer;
        }
        $data['schedules'] = $schedule;
        $data['users'] = Mobileuser::all();
        return view('dashboards.repeated-schedules.repeatedschedule', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['users'] = Mobileuser::all();
        $data['purpose'] = Purpose::all();

        return view('dashboards.repeated-schedules.repeated', $data);
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
            'user_id' => 'required|exists:mobileusers,id',
            'day' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'area' => 'required|array',
            'subarea' => 'required|array',
            'area.*' => 'required|exists:areas,areacode',
            'subarea.*' => 'required|exists:subareas,subarea',
            'dealer' => 'required|array',
            'dealer.*' => 'required|exists:dealers,code',
            'remarks' => 'required'
        ]);
        $area = collect($request->area);
        $area = $area->implode(" :: ");

        $subarea = collect($request->subarea);
        $subarea = $subarea->implode(" :: ");

        $dealer = collect($request->dealer);
        $dealer = $dealer->implode(" :: ");

        $schedule = new Repeatedschedule();

        $schedule->user_id = $request->user_id;
        $schedule->day = $request->day;
        $schedule->areacode = $area;
        $schedule->subarea = $subarea;
        $schedule->dealer_code = $dealer;
        $schedule->visited_dealer = $dealer;
        $schedule->remarks = $request->remarks;

        $schedule->save();

        // foreach($request->dealer as $dealer){
        //     $dailyvisit = new RepeatedDealerVisit();

        //     $dailyvisit->repeateschedule_id  = $schedule->id;
        //     $dailyvisit->code = $dealer;

        //     $dailyvisit->save();
        // }


        return back()->with('Status', 'Repeated Schedule Created');
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
        $schedule = Repeatedschedule::findorFail($id);
        $areacode = explode(' :: ', $schedule->areacode);
        $sub_area = explode(' :: ', $schedule->subarea);
        $mobile = Mobileuser::where('id', $schedule->user_id)->select('name', 'areas', 'sub_areas')->first();
        $area = Area::whereIn('areacode', explode(' :: ', $schedule->area))->get();
        $subarea = Subarea::whereIn('subarea', explode(' :: ', $schedule->subarea))->get();
        $dealer = Dealer::whereIn('code', explode(' :: ', $schedule->dealer_code))->get();
        $allarea = Area::whereIn('areacode', explode(' :: ', $mobile->areas))->get();

        $schedule->mobile = $mobile;
        $schedule->area = $area;
        $schedule->subarea = $subarea;
        $schedule->dealer = $dealer;

        // dd($schedule);
        $assignareas = DB::table('assignareas')->whereIn('areacode', $areacode)->groupBy('subarea_id')->get();

        foreach ($assignareas as $d) {
            $sub = Subarea::findorFail($d->subarea_id);
            $d->subarea = $sub->subarea;
        }

        $data['dealers'] = Dealer::whereIn('subarea', $sub_area)->get();
        $data['allareas'] = $allarea;
        $data['data'] = $assignareas;
        $data['schedule'] = $schedule;

        return view('dashboards.repeated-schedules.repeated-edit', $data);
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
            // 'user_id'=>'required|exists:mobileusers,id',
            'day' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'area' => 'required|array',
            'subarea' => 'required|array',
            'area.*' => 'required|exists:areas,areacode',
            'subarea.*' => 'required|exists:subareas,subarea',
            'dealer' => 'required|array',
            'dealer.*' => 'required|exists:dealers,code',
            'remarks' => 'required'
        ]);
        $area = collect($request->area);
        $area = $area->implode(" :: ");

        $subarea = collect($request->subarea);
        $subarea = $subarea->implode(" :: ");

        $dealer = collect($request->dealer);
        $dealer = $dealer->implode(" :: ");

        $schedule = Repeatedschedule::findorFail($id);

        // $schedule->user_id = $request->user_id;
        $schedule->day = $request->day;
        $schedule->areacode = $area;
        $schedule->subarea = $subarea;
        $schedule->dealer_code = $dealer;
        $schedule->remarks = $request->remarks;
        $schedule->visited_dealer = $dealer;
        // $schedule->remarks = $request->remarks;

        $schedule->save();
        // RepeatedDealerVisit::where('repeateschedule_id',$schedule->id)->delete();
        // foreach($request->dealer as $dealer){
        //     $dailyvisit = new RepeatedDealerVisit();

        //     $dailyvisit->repeateschedule_id  = $schedule->id;
        //     $dailyvisit->code = $dealer;

        //     $dailyvisit->save();
        // }

        return redirect('/repeated')->with('Status', 'Repeated Schedule Updated.!!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $repeated = Repeatedschedule::findorFail($id);

        RepeatedDealerVisit::where('repeateschedule_id', $id)->delete();

        $repeated->delete();

        Session::flash('Status', 'Repeated Shedule Successfully Deleted');

        return back();
    }
    public function search(Request $request)
    {
        $this->validate($request, [
            'user' => 'nullable|exists:mobileusers,id',
            'day' => 'nullable|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'
        ]);
        if ($request->user && $request->day) {
            $schedule = Repeatedschedule::where('user_id', $request->user)->where('day', $request->day)->orderBy('created_at', 'DESC')->get();
        } else if (!$request->user && $request->day) {
            $schedule = Repeatedschedule::where('day', $request->day)->orderBy('created_at', 'DESC')->get();
        } else if ($request->user && !$request->day) {
            $schedule = Repeatedschedule::where('user_id', $request->user)->orderBy('created_at', 'DESC')->get();
        } else {
            $schedule = Repeatedschedule::orderBy('created_at', 'DESC')->get();
        }
        foreach ($schedule as $sch) {
            $mobile = Mobileuser::where('id', $sch->user_id)->select('name')->first();
            $area = explode(' :: ', $sch->area);
            $area = Area::whereIn('areacode', $area)->get();
            $subarea = Subarea::whereIn('subarea', explode(' :: ', $sch->subarea))->get();
            $dealer = Dealer::whereIn('code', explode(' :: ', $sch->dealer_code))->get();
            // dd($dealer);
            $sch->user = $mobile;
            $sch->area = $area;
            $sch->subarea = $subarea;
            $sch->dealer = $dealer;
        }
        // dd($schedule);
        // return $schedule;
        $users = Mobileuser::all();
        $data['users'] = $users;
        $data['schedules'] = $schedule;
        $data['user_id'] = $request->user;
        $data['day'] = $request->day;
        return view('dashboards.repeated-schedules.repeatedschedule', $data);
    }
}

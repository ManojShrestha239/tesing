<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Area;
use App\Models\AreaMobileuser;
use App\Models\Mobileuser;
use App\Models\Subarea;
use App\Models\Role;

class MobileUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mobileusers = Mobileuser::orderBy('id', 'ASC')->get();

        foreach ($mobileusers as $sess) {
            $areas = explode(' :: ', $sess->areas);
            $subarea = explode(' :: ', $sess->sub_areas);
            $subarea = implode(',', $subarea);
            $area = Area::whereIn('areacode', $areas)->pluck('area')->toArray();
            $sess->areas = $area;
            $sess->sub_areas = $subarea;
            $area = null;
        }
        $data['mobileusers'] = $mobileusers;
        return view('dashboards.mobileusers.user-index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['areas'] = Area::where('status', 'Published')->get();
        $data['mobileusers'] = Mobileuser::orderBy('name')->get();
        $data['roles'] = Role::orderBy('role')->get();
        return view('dashboards.mobileusers.user-register', $data);
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
            'name' => 'required|max:255',
            'email' => 'required|email|unique:mobileusers|max:255',
            'phone' => 'required|numeric|unique:mobileusers|digits_between:9,10',
            'areas' => 'required|array',
            'areas.*' => 'required|max:255|exists:areas,areacode',
            'password' => 'required|confirmed|min:6|max:255',
            'subarea' => 'required|array',
            'subarea.*' => 'required|exists:subareas,subarea',
            'is_tracking_enabled' => 'nullable|boolean',
            'role_id' => 'required|exists:roles,id',
            'is_senior' => 'required|in:0,1,2',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:mobileusers,id',
        ]);

        DB::beginTransaction();

        try {

            $b = collect($request->areas);
            $a = $b->implode(' :: ');

            $sub = collect($request->subarea);
            $su = $sub->implode(' :: ');
            $user = Mobileuser::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'areas' => $a,
                'sub_areas' => $su,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'is_tracking_enabled' => $request->is_tracking_enabled ?? false,
                'is_senior' => $request->is_senior === '1' ? true : false,
                'is_super_senior' => $request->is_senior === '2' ? true : false,
                'status' => 'Active'
            ]);

            $user->area()->attach($request->areas);

            // Attach team members if senior (manual selection)
            if ($request->is_senior === '1' && $request->has('team_members') && !empty($request->team_members)) {
                $user->teamMembers()->attach($request->team_members);
            }

            // If super senior, attach all existing mobileusers as team members
            if ($request->is_senior === '2') {
                $allUsers = Mobileuser::where('id', '!=', $user->id)->pluck('id')->toArray();
                $user->teamMembers()->attach($allUsers);
            }

            // Auto-attach this new user to ALL existing super seniors' teams
            $superSeniors = Mobileuser::where('is_super_senior', true)
                ->where('id', '!=', $user->id)
                ->pluck('id')
                ->toArray();
            foreach ($superSeniors as $superSeniorId) {
                DB::table('mobileuser_teams')->insertOrIgnore([
                    'senior_id' => $superSeniorId,
                    'employee_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('Status', 'Cannot create User please try again later');
            return back();
        }

        return redirect('/user')->with('Status', 'New User Registered.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mobileuser = Mobileuser::findorFail($id);
        $mobileuser->device = '';
        $mobileuser->save();
        return redirect('/user')->with('Status', 'Device Reset.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mobileusers = Mobileuser::findorFail($id);
        $areas = Area::where('status', 'Published')->get();
        $sarea = Area::whereIn('areacode', explode(" :: ", $mobileusers->areas))->get();

        $subarea = Subarea::whereIn('subarea', explode(" :: ", $mobileusers->sub_areas))->get();
        $sub = DB::table('assignareas')->whereIn('areacode', explode(" :: ", $mobileusers->areas))->groupBy('subarea_id')->orderBy('id', 'Desc')->get();

        foreach ($sub as $d) {
            $su = Subarea::findorFail($d->subarea_id);
            $d->subarea = $su->subarea;
        }

        // Get all mobileusers for team selection (excluding current user)
        $allMobileusers = Mobileuser::where('id', '!=', $id)->orderBy('name')->get();
        $selectedTeamMembers = $mobileusers->teamMembers->pluck('id')->toArray();
        $roles = Role::orderBy('role')->get();

        $data['mobileusers'] = $mobileusers;
        $data['areas'] = $areas;
        $data['roles'] = $roles;
        $data['selectedarea'] = $sarea;
        $data['selectedsub'] = $subarea;
        $data['subarea'] = $sub;
        $data['allMobileusers'] = $allMobileusers;
        $data['selectedTeamMembers'] = $selectedTeamMembers;

        return view('dashboards.mobileusers.user-edit', $data);
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|numeric',
            'areas' => 'required|array',
            'areas.*' => 'required|exists:areas,areacode',
            'subarea' => 'required|array',
            'subarea.*' => 'required',
            'role_id' => 'required|exists:roles,id',
            'is_tracking_enabled' => 'nullable|boolean',
            'is_senior' => 'required|in:0,1,2',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:mobileusers,id',
        ]);

        $mobileuser = Mobileuser::findorFail($id);
        if ($mobileuser->phone != $request->phone) {
            $this->validate($request, [
                'phone' => 'required|numeric|unique:mobileusers,id|digits_between:9,10',
            ]);
        }
        if ($mobileuser->email != $request->email) {
            $this->validate($request, [
                'email' => 'required|email|unique:mobileusers,id|max:255',
            ]);
        }

        $area = collect($request['areas'])->implode(' :: ');
        $subarea = collect($request->subarea)->implode(' :: ');

        $mobileuser->name = request('name');
        $mobileuser->email = request('email');
        $mobileuser->phone = request('phone');
        $mobileuser->areas = $area;
        $mobileuser->sub_areas = $subarea;
        $mobileuser->role_id = request('role_id');
        $mobileuser->is_tracking_enabled = request('is_tracking_enabled');
        $mobileuser->is_senior = $request->is_senior === '1' ? true : false;
        $mobileuser->is_super_senior = $request->is_senior === '2' ? true : false;

        $mobileuser->save();
        AreaMobileuser::where('mobileuser_id', $mobileuser->id)->delete();
        $mobileuser->area()->attach($request->areas);

        // Update team members based on senior/super senior status
        if ($request->is_senior === '2') {
            // Super senior gets all other mobileusers automatically
            $allOtherUsers = Mobileuser::where('id', '!=', $id)->pluck('id')->toArray();
            $mobileuser->teamMembers()->sync($allOtherUsers);
        } elseif ($request->is_senior === '1' && $request->has('team_members')) {
            // Regular senior gets manually selected team members
            $mobileuser->teamMembers()->sync($request->team_members);
        } else {
            $mobileuser->teamMembers()->detach();
        }

        // Auto-attach this user to ALL existing super seniors' teams (if not already)
        $superSeniors = Mobileuser::where('is_super_senior', true)
            ->where('id', '!=', $id)
            ->pluck('id')
            ->toArray();
        foreach ($superSeniors as $superSeniorId) {
            DB::table('mobileuser_teams')->updateOrInsert(
                ['senior_id' => $superSeniorId, 'employee_id' => $id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return redirect('/user')->with('Status', 'User Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Mobileuser::findorFail($id);
        if ($user->status == "Active") {
            $user->status = "Inactive";
            Session::flash('Status', 'USer Disabled Successfully');
        } else {
            $user->status = "Active";
            Session::flash('Status', 'USer Enabled Successfully');
        }
        $user->save();
        return back();
    }

    public function reset($id)
    {
        $mobileuser = Mobileuser::findorFail($id);
        $mobileuser->device = '';
        $mobileuser->save();
        return redirect('/user')->with('Status', 'Device Reset.');
    }
    public function passwordView($id)
    {
        return view('dashboards.mobileusers.changePassword', ['id' => $id]);
    }

    public function changePassword(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'required|min:6|max:255|same:con_password'
        ]);
        $mobileuser = Mobileuser::findorFail($id);
        $mobileuser->password = Hash::make($request->password);

        $mobileuser->save();
        return redirect('/user')->with('Status', 'Password Updated.');
    }
}

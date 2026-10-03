<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Location;
use App\Models\Mobileuser;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['roles'] = Role::all();
        return view('dashboards.roles.show',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'role' => 'required|unique:roles|max:191',
            'status'=>'required|in:Active,Inactive'
        ]);
        Role::create([
            'role' => $request->role,
            'status'=>$request->status
        ]);

        return redirect('/role')->with('Status','New Role Created.');
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
        $data['role'] = Role::findorFail($id);
        return view('dashboards.roles.edit',$data);
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
        $this->validate($request,[
            'role' => 'required|unique:roles|max:191'
        ]);
        $role = Role::findorFail($id);
        $role->role = $request->role;
        $role->save();

        return redirect('/role')->with('Status','Role Updated Successfully!!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findorFail($id);
        if($role->status=='Active'){
            $role->status = 'Inactive';
            Session::flash('Status','role Inactivate Successfully.');
        }else{
            $role->status = 'Active';
            Session::flash('Status','role Activate Successfully.');
        }
        $role->save();
        return back();
    }
    public function roleassign(){
        $locations = Location::all();

        foreach($locations as $l){
            $user = Mobileuser::where('id',$l->user_id)->first();

            $l->role_id = $user->role_id;

            $l->save();
        }
        return back();
    }
}

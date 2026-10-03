<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminArea;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['users'] = User::where('app', 'SOA')->get();
        if (Auth::user()->hasRole('super') || Auth::user()->hasRole('Super-Admin') ||  Auth::user()->hasRole('marketing') || Auth::user()->hasRole('ASM'))
            return view('dashboards.admins.admin-index', $data);
        else
            abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['areas'] = Area::where('status', 'published')->get();
        // $data['roles'] = Role::all();
        if (Auth::user()->hasRole('Super-Admin')) {
            $data['roles'] = Role::get();
        } else {
            $data['roles'] = Role::where('name', '!=', 'Super-Admin')->get();
        }
        if (Auth::user()->hasRole('super') || Auth::user()->hasRole('Super-Admin') || Auth::user()->hasRole('marketing') || Auth::user()->hasRole('ASM'))
            return view('dashboards.admins.admin-register', $data);
        else
            abort(403);
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
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|numeric|unique:users|digits_between:9,10',
            'password' => 'required|confirmed|min:6|max:255',
            'user_type' => 'required|exists:spatie_roles,id', //ASM
            'area' => 'nullable|array',
            'area.*' => 'nullable|exists:areas,areacode'
        ]);
        DB::beginTransaction();

        try {

            $role = Role::findorfail($request['user_type']);

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'password' => Hash::make($request['password']),
                'user_type' => $role->name,
                'app' => 'SOA'
            ]);
            $user->assignRole($role);
            if ($request->area) {
                if (count($request->area) > 0) {
                    foreach ($request->area as $area) {
                        $user->area()->attach($area);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('Status', 'Something Went Wrong please try again');
            return back()->withInput();
        }
        return redirect('/admin')->with('Status', 'New Admin created.');
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
        if (Auth::user()->hasRole('Super-Admin')) {
            $data['roles'] = Role::get();
        } else {
            $data['roles'] = Role::where('name', '!=', 'Super-Admin')->get();
        }
        $AuthUser = Auth::user();
        $data['user'] = User::with('area')->findorFail($id);
        $data['areas'] = Area::where('status', 'published')->get();
        if ($AuthUser->hasRole('Super-Admin') || $id == $AuthUser->id) {
            if ($AuthUser->hasRole('Super-Admin')) {
                if ($AuthUser->hasRole('Super-Admin') && $id === $AuthUser->id) {
                    return view('dashboards.admins.admin-edit', $data);
                } else {
                    return view('dashboards.admins.admin-edit', $data);
                }
            }
            // return view('dashboards.admins.admin-edit', $data);
        } else {
            abort(403);
        }
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
            'email' => 'required|email',
            'phone' => 'required|numeric|digits_between:9,10',
            'user_type' => 'nullable|exists:spatie_roles,id',
            'area' => 'nullable|array',
            'area.*' => 'nullable|exists:areas,areacode'
        ]);

        $user = User::findorFail($id);
        $role = $request->user_type ? Role::findorfail($request->user_type) : null;
        if (Auth::user()->id == $id || Auth::user()->hasRole('Super-Admin')) {
            if ($request->email != $user->email || $request->phone != $user->phone) {
                $this->validate($request, [
                    'email' => 'required|email|unique:users,phone',
                    'phone' => 'required|numeric|unique:users,phone|digits_between:9,10',
                ]);
            }

            $user->name = request('name');
            $user->email = request('email');
            $user->phone = request('phone');
            if ($request->user_type) {
                $user->user_type = $role->name;
            }

            $user->save();
            if ($request->user_type) {
                $user->syncRoles($role);
            }

            if ($request->area) {
                AdminArea::where('user_id', $user->id)->delete();
                if (count($request->area) > 0) {
                    foreach ($request->area as $area) {
                        $user->area()->attach($area);
                    }
                }
            }
            return back()->with('Status', 'Admin Details Updated.');
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findorFail($id);
        if ($user->id != Auth::user()->id) {
            if ($user->status == "Active") {
                $user->status = "Inactive";
                Session::flash('Status', 'User Disabled Successfully.');
            } else {
                $user->status = "Active";
                Session::flash('Status', 'User Enabled Successfully.');
            }
            $user->save();
        } else {
            Session::flash('Status', 'Cannot Disabled.');
        }

        return back();
    }

    public function change($id)
    {
        //
        $AuthUser = Auth::user();
        $user = User::findorFail($id);
        if ($AuthUser->user_type == 'super' || $id == $AuthUser->id) {
            return view('dashboards.admins.admin-password', compact('user'));
        } else
            abort(403);
    }

    public function changepass(Request $request, $id)
    {
        //
        $user = User::findorFail($id);

        $this->validate($request, [
            'password' => 'required|confirmed|min:6|max:255',
        ]);
        if ($user->id == Auth::user()->id) {
            if (Hash::check($request['old_password'], $user->password)) {

                $pass = Hash::make($request['password']);
                $user->password = $pass;
                $user->save();
                //            $user->update(['password' => $pass]);
                return redirect('/admin')->with('Status', 'Password Changed.');
            } else {
                return back()->withErrors(['old_password' => 'Old password not matched']);
            }
        } else {
            $pass = Hash::make($request['password']);
            $user->password = $pass;
            $user->save();
            //            $user->update(['password' => $pass]);
            return redirect('/admin')->with('Status', 'Password Changed.');
        }
    }
}

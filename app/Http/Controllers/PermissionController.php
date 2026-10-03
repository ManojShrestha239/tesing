<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['role:super']);
    //     // $this->middleware(['role:super', 'permission:publish articles|edit articles']);
    //     // $this->middleware(['role_or_permission:super|edit articles']);
    // }


    function index()
    {
        $data['roles'] = Role::all();
        $data['permissions'] = Permission::all();

        return view('dashboards.spatieroles.store', $data);
    }

    function store(Request $request)
    {
        $this->validate($request, [
            'role' => 'required|unique:spatie_roles,id',
            'user_permission' => 'required|array',
            'user_permission' => 'required|unique:spatie_permissions,name'
        ]);

        $role = Role::create([
            'name' => $request->role
        ]);

        foreach ($request->user_permission as $permission) {
            // dd($permission);
            $Mpermission = Permission::where(['id' => $permission, 'guard_name' => 'web'])->first();
            $role->givePermissionTo([$Mpermission->name]);
        }

        Session::flash('Status', 'Role Added Successfully.');
        return back();
    }

    function edit($id)
    {
        $data['role'] = Role::findorfail($id);
        $data['permissions'] = Permission::all();
        $data['rolePermissions'] = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id')
            ->all();
        return view('dashboards.spatieroles.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:spatie_roles,name,' . $id,
            'user_permission' => 'required|array',
            'user_permission.*' => 'exists:spatie_permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        // Get permission names from IDs
        $permissions = Permission::whereIn('id', $request->user_permission)->pluck('name')->toArray();

        $role->syncPermissions($permissions);

        Session::flash('Status', 'Role updated Successfully.');
        return back();
    }
}

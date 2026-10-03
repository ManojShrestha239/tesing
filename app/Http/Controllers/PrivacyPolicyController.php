<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PrivacyPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['policies'] = PrivacyPolicy::all();

        return view('dashboards.privacy-policies.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.privacy-policies.create');
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
            'title' => 'required|max:255',
            'description' => 'required',
            'status' => 'required|in:Active,Inactive'
        ]);
        if ($request->status == "Active") {
            $p = PrivacyPolicy::where('status', 'Active')->first();

            if ($p) {
                $p->status = 'Inactive';

                $p->save();
            }
        }
        $privacy = new PrivacyPolicy();

        $privacy->title = $request->title;
        $privacy->description = $request->description;
        $privacy->status = $request->status;

        $privacy->save();

        Session::flash('Status', 'Privacy and Policies Added successfully');

        return redirect('/privacy-policy');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PrivacyPolicy  $privacyPolicy
     * @return \Illuminate\Http\Response
     */
    public function show(PrivacyPolicy $privacyPolicy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PrivacyPolicy  $privacyPolicy
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['privacy'] = PrivacyPolicy::findorFail($id);

        return view('dashboards.privacy-policies.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PrivacyPolicy  $privacyPolicy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $privacyPolicy = PrivacyPolicy::findorFail($id);
        $privacyPolicy->title = $request->title;
        $privacyPolicy->description = $request->description;
        $privacyPolicy->status = $request->status;

        $privacyPolicy->save();

        Session::flash('Status', 'Privacy and Policies Update successfully');
        return redirect('/privacy-policy');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PrivacyPolicy  $privacyPolicy
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrivacyPolicy $privacyPolicy)
    {
        $privacyPolicy->delete();

        return back()->with('Status', 'Delete SUccesfully');
    }

    public function status($id)
    {
        $privacyPolicy = PrivacyPolicy::findorFail($id);
        if ($privacyPolicy->status == "Active") {
            $privacyPolicy->status = "Inactive";

            Session::flash('Status', 'Privacy and Policies Deactivate successfully');
        } else {
            $privacyPolicy->status = "Active";
            Session::flash('Status', 'Privacy and Policies Activate successfully');
        }
        $privacyPolicy->save();
        return redirect('/privacy-policy');
    }
    public function privacypolicy(){
        $data['privacy'] = PrivacyPolicy::where('status', 'Active')->first();

        return view('dashboards.privacy-policies.privacy-policy',$data);
    }
}

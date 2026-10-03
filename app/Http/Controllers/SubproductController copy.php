<?php

namespace App\Http\Controllers;

use App\Models\Subproduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SubproductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['subproducts'] = Subproduct::all();

        return view('dashboards.subproducts.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.subproducts.create');
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
            'subproduct' => 'required|unique:subproducts,subproduct',
        ]);

        $sub = new Subproduct();

        $sub->subproduct = $request->subproduct;

        $sub->save();

        Session::flash('Status', 'Sub-Product Added Successfully');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subproduct  $subproduct
     * @return \Illuminate\Http\Response
     */
    public function show(Subproduct $subproduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subproduct  $subproduct
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['subproduct'] = Subproduct::findorFail($id);

        return view('dashboards.subproducts.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subproduct  $subproduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'subproduct' => 'required|unique:subproducts,subproduct|max:191'
        ]);
        if ($valid->fails()) {
            // return response()->json(['status'=>'error','message'=>''])
            // Session::flash('errors',$valid->errors());
            return back()->withErrors($valid->errors());
        }

        $sub = Subproduct::findorfail($id);

        $sub->subproduct = $request->subproduct;

        $sub->save();

        Session::flash('Status', 'Sub-Product Update Successfully');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subproduct  $subproduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subproduct = Subproduct::findorFail($id);

        if ($subproduct->status == 'Active') {
            $subproduct->status = "Inactive";

            $subproduct->save();
            Session::flash('Status', 'Subproduct Inactivated successfully');
        } else {
            $subproduct->status = "Active";
            $subproduct->save();
            Session::flash('Status', 'Subproduct Inactivated successfully');
        }
        return back();
    }
}

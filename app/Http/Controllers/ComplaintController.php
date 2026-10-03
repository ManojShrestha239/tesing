<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['complaints'] = Complaint::with('productcomplaints')->get();

        return view('dashboards.complaints.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Complaint  $Complaint
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['complaint'] = Complaint::findorFail($id);

        return view('dashboards.complaints.view',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Complaint  $Complaint
     * @return \Illuminate\Http\Response
     */
    public function edit (Complaint $Complaint)
    {
        $data['complaint'] = Complaint::with('productcomplaints')->findOrFail($Complaint->id);
        return view('dashboards.complaints.popupcomplaint', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Complaint  $Complaint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Complaint $Complaint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Complaint  $Complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $Complaint)
    {
        //
        $com = Complaint::findorfail($Complaint->id);
        if($com->status == 'Open'){
            $com->status = 'Close';
            $com->save();
        }
        return back()->with('Status', "Complaint has closed.");
    }
}

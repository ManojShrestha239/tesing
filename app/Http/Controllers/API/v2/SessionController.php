<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Purpose;
use App\Models\VisitPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class SessionController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function new(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'address' => 'required|string|max:191',
            'dealer_name' => 'required|string|max:191',
            'user_id' => 'required|exists:mobileusers,id',
            'contact' => 'required',
            'purpose_id' => 'required',
            'date' => 'date|date_format:Y-m-d',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'remarks' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:0,360',
            'longitude' => 'required|numeric|between:0,360'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $purposes = explode(' :: ', $request->purpose_id);

        if (Purpose::whereIn('id', $purposes)->count() == 0) {
            return response()->json(['status' => 'error', 'message' => 'purpose_id invalid'], 422);
        }
        $negotiation = new Session();

        $negotiation->user_id = Auth::user()->id;
        $negotiation->dealer_name = $request->dealer_name;
        $negotiation->dealer_address = $request->address;
        $negotiation->contact = $request->contact;
        $negotiation->purpose_id = $request->purpose_id;
        $negotiation->start_time = $request->start_time;
        $negotiation->end_time = $request->end_time;
        $negotiation->date = $request->date;
        $negotiation->status = "Unlisted";
        $negotiation->remarks = $request->remarks;
        $negotiation->latitude = $request->latitude;
        $negotiation->longitude = $request->longitude;

        $negotiation->save();

        foreach ($purposes as $purpose) {
            $visitpurpose = new VisitPurpose();

            $visitpurpose->user_id = $negotiation->user_id;
            $visitpurpose->visit_id = $negotiation->id;
            $visitpurpose->purpose_id = $purpose;
            $visitpurpose->date = $request->date;
            $visitpurpose->status = "Unlisted";

            $visitpurpose->save();
        }
        return response()->json($negotiation);
    }
}

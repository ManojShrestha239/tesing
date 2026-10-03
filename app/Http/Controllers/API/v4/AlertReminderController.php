<?php

namespace App\Http\Controllers\API\v4;

use App\Http\Controllers\Controller;
use App\Models\AlertReminder;
use App\Models\Dealer;
use App\Models\ReminderPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlertReminderController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        $lists = AlertReminder::with('dealer', 'reminderpurposes.purpose')->where([['date', '>=', date('Y-m-d')], 'mobileuser_id' => $user->id, 'status' => 'Pending'])->get();
        foreach ($lists as $list) {
            if ($list->table_name) {
                $data = DB::table($list->table_name)->where('id', $list->column_id)->first();
                if ($data) {
                    if ($list->table_name == 'visits') {
                        $dealerinfo = ($list->dealer_code) ? $list->dealer->code . ' ' . $list->dealer->name . ' ' . $list->dealer->mobile : $list->dealer_name . "  " . $list->dealer_address . ' ' . $list->contact;
                        $list->dealerinfo = $dealerinfo;
                        $list->visited_date = $data->date;
                    } else if ($list->table_name == 'orders') {
                        $dealerinfo = $list->dealer->code . ' ' . $list->dealer->name . ' ' . $list->dealer->mobile;
                        $list->dealerinfo = $dealerinfo;
                        $list->visited_date = $data->date;
                    } else {
                        $dealerinfo = $list->dealer->code . ' ' . $list->dealer->name . ' ' . $list->dealer->mobile;
                        $list->dealerinfo = $dealerinfo;
                        $list->visited_date = $data->collection_date;
                    }
                } else {
                    $list->dealerinfo = "";
                    $list->visited_date = "";
                }
            }
        }
        return response()->json(['status' => 'success', 'lists' => $lists]);
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
        $valid = Validator::make($request->all(), [
            'dealer_code' => 'required|exists:dealers,code',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'remarks' => 'required',
            'purpose' => 'required|array',
            'purpose.*' => 'required|exists:purposes,id',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'messages' => $valid->errors()], 422);
        }

        $authUser = auth()->user();
        $areaCodes = explode(' :: ', $authUser->areas);
        $subAreas = explode(' :: ', $authUser->sub_areas);

        $dealer = Dealer::where('code', $request->dealer_code)->first();

        if (!$dealer) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Dealer does not exist'
            ], 404);
        }

        if (
            !in_array($dealer->area_code, $areaCodes) ||
            !in_array($dealer->subarea, $subAreas)
        ) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Your are not authorized to add reminder and alert for this dealer',
            ], 400);
        }

        $alert = new AlertReminder();

        $alert->dealer_code = $request->dealer_code;
        $alert->date = $request->date;
        $alert->remarks = $request->remarks;
        $alert->status = "Pending";
        $alert->flag = "Reminder";
        $alert->mobileuser_id = auth('api')->user()->id;

        $alert->save();
        foreach ($request->purpose as $pur) {
            $purpose = new ReminderPurpose();

            $purpose->purpose_id = $pur;
            $purpose->alertreminder_id = $alert->id;

            $purpose->save();
        }

        return response()->json(['status' => 'success', 'message' => 'created successfully']);
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
        $valid = Validator::make($request->all(), [
            'status' => 'required|in:Done,Cancelled',
            'remarks' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'messages' => $valid->errors()], 422);
        }
        $alert = AlertReminder::findorFail($id);

        $alert->status = $request->status;
        $alert->remarks = $request->remarks;

        $alert->save();

        return response()->json(['status' => 'success', 'message' => 'Reminders Updated']);
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
}

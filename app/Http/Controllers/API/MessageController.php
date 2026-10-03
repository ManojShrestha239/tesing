<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Traits\Timestamp;
use Pusher\Pusher;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use carbon\carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user();
        $my_id = $id->id;
        $admin_id = 0;

        Message::where(['from' => $my_id, 'to' => $admin_id,'is_read'=>0])->update(['is_read' => 1]);
        $messages = Message::where(function ($query) use ($admin_id, $my_id) {
            $query->where('from', $my_id)->where('to', $admin_id);
        })->orWhere(function ($query) use ($admin_id, $my_id) {
            $query->where('from', $admin_id)->where('to', $my_id);
        })->get();
        return response()->json(['message'=>$messages]);
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
        $valid = Validator::make($request->all(),[
            'message'=>'required',
        ]);
        if($valid->fails()){
            return response()->json(['status'=>'success','message'=>$valid->errors()],422);
        }
        $to = 0;
        $id = Auth::user();
        $from = $id->id;
        
        $data = new Message();
        
        $data->from = $from;
        $data->to = $to;
        $data->message = $request->input('message');
        $data->is_read = 0;
        // $data->created_at = carbon::now();
        $data->save();
        // return $data;

        $options = array(
            'cluster' => 'ap2',
            'useTLS' => true
        );

        $pusher = new Pusher(
            config('app.PUSHER_APP_KEY'),
            config('app.PUSHER_APP_SECRET'),
            config('app.PUSHER_APP_ID'),
            $options
        );

        $data = ['from' => $from, 'to' => $to]; // sending from and to user id when pressed enter
        $pusher->trigger('my-channel', 'my-event', $data);
        // return $data;



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
}

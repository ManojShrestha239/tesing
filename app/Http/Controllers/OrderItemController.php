<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Orderitem;
use App\Models\Challan;

class OrderItemController extends Controller
{
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
        $data['order'] = Orderitem::findorFail($id);

        return view('dashboards.orders.edit', $data);
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
            'quantity'=>'required|numeric|min:1'
        ]);
        $order = Orderitem::findorFail($id);
        $challan = Challan::where(['order_id'=> $order->order_id, 'items_id' =>$order->items_id])->first();
        if($challan){
            return back()->with('Status','you cannot update this order because of already been created challan');
        }
        $ord = Order::findorFail($order->order_id);
        $date = date('Y-m-d');

        $order->quantity = $request->quantity;
        $order->quantity_updated = $request->quantity;
        $order->total_price = $request->quantity*$order->product_rate;

        $order->save();

        $ord->total_price = Orderitem::where('order_id',$order->id)->sum('total_price');

        $ord->save();

        return back()->with('Status', 'Order Edited Successfully.');
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

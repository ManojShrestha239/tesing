<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Challan;
use App\Models\Dealer;
use App\Exports\ChallanExport;
use App\LogDetail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\Subproduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChallanReportController extends Controller
{
    public function challanreport($id, Request $request)
    {
        $custom_size = Size::where('size', 'CUSTOM')->first();
        $data['sizes'] = $sizes = Size::where('size', '!=', 'CUSTOM')->whereNull('size_type')->get();
        $data['d_sizes'] = $d_sizes = Size::where([['size', '!=', 'CUSTOM'],'size_type'=>'door'])->get();
        if(count($sizes)>count($d_sizes)){
            for($i=0;$i<(count($sizes)-count($d_sizes));$i++){
                $obj = new \stdClass();

                $obj->id = '-';
                $obj->size = '-';
                $obj->status = '-';
                $obj->created_at = '-';
                $obj->updated_at = '-';
                $obj->size_type = 'door';

                $d_sizes->push($obj);
            }
        }else if(count($sizes)<count($d_sizes)){
            for($i=0;$i<(count($d_sizes)-count($sizes));$i++){
                $obj = new \stdClass();

                $obj->id = '-';
                $obj->size = '-';
                $obj->status = '-';
                $obj->created_at = '-';
                $obj->updated_at = '-';
                $obj->size_type = null;

                $sizes->push($obj);
            }
        }
        $challans = Challan::with('relsize')->groupBy(['category_id', 'product_id'])
            ->where(['order_id' => $id, ['size_id', '!=', $custom_size->id]])
            ->get();
        // $ch = Challan::where('order_id',$id)->first();
        $custom_size = Size::where('size', 'CUSTOM')->first();
        if (!$custom_size) {
            return back()->with('Status', 'Add CUSTOM Size.');
        }
        foreach ($challans as $challan) {
            $ch_subproducts = Challan::where(['order_id' => $id, 'category_id' => $challan->category_id, 'product_id' => $challan->product_id])->groupBy('subproduct_id')->get();
            foreach ($ch_subproducts as $c_i) {
                $challan_items = Challan::with('size', 'items')->where(['order_id' => $id, 'category_id' => $challan->category_id, 'product_id' => $challan->product_id, 'subproduct_id' => $c_i->subproduct_id, ['size_id', '!=', $custom_size->id]])->get();
                $c_i->items = $challan_items;
            }
            $challan->ch_subproducts = $ch_subproducts;
        }
        $cu_challan = Challan::where(['order_id' => $id, 'size_id' => $custom_size->id])->get();
        $ac_challans = Challan::where(['order_id' => $id, ['size_id', '!=', $custom_size->id]])->get();
        $cu_quantity = $cu_challan->sum('quantity');
        $cu_sqft = $cu_challan->sum('area');
        $cu_wt = $cu_challan->sum('weight');

        $ac_quantity = $ac_challans->sum('quantity');
        $ac_sqft = $ac_challans->sum('area');
        $ac_wt = $ac_challans->sum('weight');

        $ch = LogDetail::where(['ordercoll_id'=>$id,'flag'=>'order','status'=>'Dispatched'])->first();
        $data['total_SQFT'] =$cu_sqft+$ac_sqft;
        $data['total_quantity'] =$cu_quantity+$ac_quantity;
        $data['total_weight'] =$cu_wt+$ac_wt;

        $order = Order::findorFail($id);
        $data['custom_challans'] = $cu_challan;
        $data['challans'] = $challans;
        $data['subproducts'] = Subproduct::all();
        $data['order'] = $order;
        $data['ch'] = $ch;
        $data['loopcount'] = (count($sizes)>=count($d_sizes))?count($sizes):count($d_sizes);
        if($request->type=="report"){
            return Excel::download(new ChallanExport($data),"challan_report_of_".$order->dealer->code.".xlsx");
        }
        return view('dashboards.challans.challan-report', $data);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Carbon\Carbon;
use App\Models\Mobileuser;
use App\Models\LogDetail;
use App\Models\Orderitem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Denominationpoint;
use App\Models\Bank;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class NoticeController extends Controller
{
    use Nepali_Calendar,CustomNepaliCalendarConverter;
    //
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $notice = Notice::where('user_id', null)->where('expiry_date', '>=', $today)->get();
        return response()->json($notice);
    }

    public function individual(Request $request, $id)
    {
        $al = array();
        $all = array();
        $all1 = array();
        $arr = [];
        $res = [];
        $logs = Logdetail::where('user_id', Auth::user()->id)->get();
        foreach ($logs as $log) {
            if (($log->flag) == 'collection') {
                $info = DB::table('logdetails')
                    ->join('payments', 'logdetails.ordercoll_id', 'payments.id')
                    ->join('dealers', 'payments.dealer_code', 'dealers.code')
                    ->leftJoin('banks', 'banks.id', 'payments.bank_id')
                    ->select('logdetails.id', 'logdetails.ordercoll_id', 'payments.payment_date', 'logdetails.flag', 'logdetails.cheque_no', 'logdetails.date', 'logdetails.status', 'logdetails.previous_status', 'logdetails.remarks', 'logdetails.created_at', 'dealers.name', 'dealers.code', 'dealers.address', 'banks.name as bankname', 'logdetails.updated_at')
                    ->where('logdetails.id', $log->id)
                    ->get();
                foreach ($info as $inf) {
                    if ($inf->bankname == 'cash') {
                        $inf->bankname = null;
                    }
                    $logs = array(
                        "check" => (string)$inf->created_at,
                        "flag" => $inf->flag,
                        "title" => 'Collection Updated',
                        "notice" => 'Collection status updated from ' . $inf->previous_status . ' to ' . $inf->status,
                        "expiry_date" => null,
                        "created_at" => (string)$inf->created_at,
                        "id" => $inf->id,
                        "payment_id" => $inf->ordercoll_id,
                        "cheque_no" => $inf->cheque_no,
                        "date" => $inf->date, "status" => $inf->status,
                        "remarks" => $inf->remarks,
                        'dealer_name' => $inf->name,
                        'dealer_code' => $inf->code,
                        'dealer_address' => $inf->address,
                        'bank_name' => $inf->bankname,
                        'payment_date' => $inf->payment_date
                    );
                    array_push($al, (object) $logs);
                }
            } elseif ($log->flag == 'payment') {

                $info = DB::table('logdetails')
                    ->join('payments', 'logdetails.ordercoll_id', 'payments.id')
                    ->join('dealers', 'payments.dealer_code', 'dealers.code')
                    ->join('banks', 'banks.id', 'payments.bank_id')
                    ->select('logdetails.id', 'logdetails.ordercoll_id', 'payments.payment_date', 'logdetails.flag', 'logdetails.cheque_no', 'logdetails.date', 'logdetails.status', 'logdetails.previous_status', 'logdetails.remarks', 'logdetails.created_at', 'dealers.name', 'banks.name as bankname', 'dealers.code', 'dealers.address', 'logdetails.updated_at')
                    ->where('logdetails.id', $log->id)
                    ->get();
                foreach ($info as $inf) {
                    if ($inf->bankname == 'cash') {
                        $inf->bankname = null;
                    }
                    $logs = array(
                        "check" => (string)$inf->created_at,
                        "flag" => $inf->flag,
                        "title" => 'Payment Date',
                        "notice" => 'Payment Date',
                        "expiry_date" => null,
                        "created_at" => (string)$inf->created_at,
                        "id" => $inf->id,
                        "payment_id" => $inf->ordercoll_id,
                        "cheque_no" => $inf->cheque_no,
                        "date" => $inf->date, "status" => $inf->status,
                        "remarks" => $inf->remarks,
                        'dealer_name' => $inf->name,
                        'dealer_code' => $inf->code,
                        'dealer_address' => $inf->address,
                        'bank_name' => $inf->bankname,
                        'payment_date' => $inf->payment_date
                    );
                    array_push($al, (object) $logs);
                }
            } else {

                $info = DB::table('logdetails')
                    ->join('orders', 'logdetails.ordercoll_id', 'orders.id')
                    ->join('dealers', 'orders.dealer_code', 'dealers.code')
                    ->select('logdetails.id', 'logdetails.ordercoll_id', 'logdetails.flag', 'logdetails.cheque_no', 'logdetails.date', 'logdetails.status', 'logdetails.previous_status', 'logdetails.remarks', 'dealers.code', 'dealers.address', 'logdetails.created_at', 'dealers.name', 'logdetails.updated_at')
                    ->where('logdetails.id', $log->id)
                    ->get();
                // return $info;
                foreach ($info as $inf) {
                    $logs = array(
                        "check" => (string)$inf->created_at,
                        "flag" => $inf->flag,
                        "title" => 'Order Updated',
                        "notice" => 'Order Status updated from ' . $inf->previous_status . ' to ' . $inf->status,
                        "expiry_date" => null,
                        "created_at" => (string)$inf->created_at,
                        "id" => $inf->id,
                        "order_id" => $inf->ordercoll_id,
                        "cheque_no" => $inf->cheque_no,
                        "date" => $inf->date,
                        "status" => $inf->status,
                        "remarks" => $inf->remarks,
                        'dealer_name' => $inf->name,
                        'dealer_code' => $inf->code,
                        'dealer_address' => $inf->address,
                        'bank_name' => null,
                        'payment_date' => null
                    );

                    array_push($all, (object) $logs);
                }
                // array_push($all, (object) $logs);
            }
            $new = array_merge($al, $all);
        }
        if (empty($new)) {
            $new = array();
        }

        // return $new;
        $notice = Notice::where('publish', '1')->where('user_id', Auth::user()->id)->orwhere('user_id', null)->where('publish', '1')->get();
        foreach ($notice as $notices) {
            $not = array(
                "check" => (string)$notices->created_at,
                "flag" => 'notice',
                "title" => $notices->title,
                "notice" => $notices->notice,
                "expiry_date" => $notices->expiry_date,
                "created_at" => (string)$notices->created_at,
                "id" => '',
                "payment_id" => '',
                "cheque_no" => null,
                "date" => null,
                "status" => null,
                "remarks" => null,
                'dealer_name' => null,
                'dealer_code' => null,
                'dealer_address' => null,
                'bank_name' => null,
                'payment_date' => null
            );
            array_push($all1, (object) $not);
        }

        $all2 = array_merge($new, $all1);
        $unsortedData = collect($all2);

        $sortedData = $unsortedData->sortByDesc('check');
        $data = [];
        foreach ($sortedData as $sort => $value) {
            $res[] = $value;
        }
        $data = $this->paginate($res);
        return response()->json($data);
    }
    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);
    }
    public function all()
    {
        $all = DB::table('notices')
            ->join('logdetails', 'notices.user_id', 'logdetails.user_id')
            ->simplePaginate(10);
        return response()->json($all);
    }
    public function orderDetails(Request $request, $id)
    {
        $order = Order::findorFail($id);
        $orderitems = Orderitem::where('order_id', $id)->get();
        foreach ($orderitems as $o) {
            $test = DB::table('orderitems')
                ->join('product_size', 'orderitems.items_id', 'product_size.id')
                ->join('products', 'product_size.product_id', 'products.id')
                ->join('sizes', 'product_size.size_id', 'sizes.id')
                ->select('orderitems.id', 'orderitems.order_id', 'orderitems.items_id', 'products.name', 'orderitems.quantity', DB::raw('CAST(product_size.price AS int) AS price'), 'product_size.size_id', 'product_size.product_id', 'sizes.size')
                ->where('orderitems.order_id', $order->id)
                ->get();
            $test = array(
                "id" => $order->id,
                "dealerCode" => $order->dealer_code,
                "date" => $order->date,
                "status" => $order->status,
                "remarks" => $order->remarks,
                "orders" => $test
            );
        }
        return response()->json($test);
    }
    public function collectionDetails(Request $request, $id)
    {
        $data = Payment::findorFail($id);
        $bank_id = $data->bank_id;
        if ($data->payment_type == 'token') {
            $Denominationpoint = Denominationpoint::where('collection_id', $id)->get();
        } else {
            $Denominationpoint = [];
        }

        // $bank_id = explode('::',$data->bank_id);
        $bank = Bank::where('id', $data->bank_id)->first();
        $name = $bank ? $bank->name : "";
        $data->collection_date = $this->custom_AD_to_BS($data->collection_date);
        $data->payment_date = $data->payment_date?$this->custom_AD_to_BS($data->payment_date):'';

        $collection = array(
            'id' => $data->id,
            'payment_type' => $data->payment_type,
            'dealer_code' => $data->dealer_code,
            'collection_date' => $data->collection_date,
            'amount' => $data->amount,
            'payment_date' => $data->payment_date,
            'receipt_num' => $data->receipt_num,
            'cheque_num' => $data->cheque_num,
            'bank_name' => $name,
            'TDS' => $data->TDS,
            'discount' => $data->discount,
            'status' => $data->status,
            "remarks" => $data->remarks,
            'mRemaks' => $data->mRemarks,
            'denomination' => $Denominationpoint,
        );
        return response()->json($collection);
    }
}

<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticeImageResource;
use App\Models\Notice;
use App\Models\LogDetail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Bank;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class NoticeController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    private function baseLog($log): array
    {
        return [
            "check" => $log->nepali_created_at,
            "flag" => $log->flag,
            "expiry_date" => null,
            "file" => null,
            "noticeimage" => [],
            "created_at" => $log->nepali_created_at,
            "id" => $log->id,
            "cheque_no" => $log->cheque_no,
            "date" => $log->date ? $this->custom_AD_to_BS($log->date) : null,
            "status" => $log->status,
            "remarks" => $log->remarks,
            "main_dealer_name" => null,
            "main_dealer_code" => null,
            "main_dealer_address" => null,
            "bank_name" => null,
            "payment_date" => null,
        ];
    }

    public function individual($id)
    {
        $logsDetailsArr = [];
        $perPage = 20;

        $logDetails = LogDetail::with([
            'collection:id,payment_date,dealer_code,bank_id',
            'collection.dealer:code,name,address',
            'collection.bank:id,name',
            'order:id,dealer_code',
            'order.dealer:code,name,address',
        ])
            ->where('user_id', $id)
            ->select([
                'id',
                'flag',
                'ordercoll_id',
                'previous_status',
                'status',
                'remarks',
                'cheque_no',
                'date',
                'created_at',
                'user_id'
            ])
            ->orderByDesc('created_at')
            ->get();

        foreach ($logDetails as $log) {
            // For Collection Logs
            if ($log->flag === 'collection') {
                $logsDetailsArr[] = array_merge(
                    $this->baseLog($log),
                    [
                        "title" => 'Collection Updated',
                        "notice" => "Collection status updated from {$log->previous_status} to {$log->status}",
                        "payment_id" => $log->ordercoll_id,
                        "dealer_name" => $log->collection?->dealer?->name,
                        "dealer_code" => $log->collection?->dealer?->code,
                        "dealer_address" => $log->collection?->dealer?->address,
                        "bank_name" => $log->collection?->bank?->name,
                        "payment_date" => $log->collection?->payment_date
                            ? $this->custom_AD_to_BS($log->collection->payment_date)
                            : null,
                    ]
                );
            }

            // For Payment Logs
            if ($log->flag === 'payment') {
                $logsDetailsArr[] = array_merge(
                    $this->baseLog($log),
                    [
                        "title" => 'Payment Date',
                        "notice" => 'Payment Date',
                        "payment_id" => $log->ordercoll_id,
                        "dealer_name" => $log->collection?->dealer?->name,
                        "dealer_code" => $log->collection?->dealer?->code,
                        "dealer_address" => $log->collection?->dealer?->address,
                        "bank_name" => $log->collection?->bank?->name,
                        "payment_date" => $log->collection?->payment_date
                            ? $this->custom_AD_to_BS($log->collection->payment_date)
                            : null,
                    ]
                );
            }

            // For Dealer Order Logs
            if ($log->flag === 'order') {
                $logsDetailsArr[] = array_merge(
                    $this->baseLog($log),
                    [
                        "title" => 'Dealer Order Updated',
                        "notice" => "Dealer Order Status updated from {$log->previous_status} to {$log->status}",
                        "order_id" => $log->ordercoll_id,
                        "dealer_name" => $log->order?->dealer?->name,
                        "dealer_code" => $log->order?->dealer?->code,
                        "dealer_address" => $log->order?->dealer?->address,
                    ]
                );
            }

            // For Retailer Order Logs
            if ($log->flag === 'retailer order') {
                $logsDetailsArr[] = array_merge(
                    $this->baseLog($log),
                    [
                        "title" => 'Retailer Order Updated',
                        "notice" => "Retailer Order Status updated from {$log->previous_status} to {$log->status}",
                        "order_id" => $log->ordercoll_id,
                        "dealer_name" => $log->order?->dealer?->name,
                        "dealer_code" => $log->order?->dealer?->code,
                        "dealer_address" => $log->order?->dealer?->address,
                    ]
                );
            }
        }

        // Cache notices for 10 minutes
        $notices = Cache::remember("notices_user_{$id}", 600, function () use ($id) {
            return Notice::with('noticeimage')
                ->where('publish', '1')
                ->where(function ($q) use ($id) {
                    $q->where('user_id', $id)
                        ->orWhereNull('user_id');
                })
                ->get();
        });

        foreach ($notices as $notice) {
            $created_at = explode(' ', (string)$notice->created_at);
            $logsDetailsArr[] = [
                "check" => $this->custom_AD_to_BS($created_at[0]) . ' ' . $created_at[1],
                "flag" => 'notice',
                "title" => $notice->title,
                "notice" => $notice->notice,
                "expiry_date" => $this->custom_AD_to_BS($notice->expiry_date),
                "created_at" => $this->custom_AD_to_BS($created_at[0]) . ' ' . $created_at[1],
                "noticeimage" => NoticeImageResource::collection($notice->noticeimage),
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
            ];
        }

        if (!empty($logsDetailsArr)) {
            $sortedData = collect($logsDetailsArr)->sortByDesc('created_at')->values()->all();
            return response()->json($this->paginate($sortedData, $perPage));
        }

        return response()->json($this->paginate([], $perPage));
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
        $test = DB::table('orderitems')
            ->join('product_size', 'orderitems.items_id', 'product_size.id')
            ->join('products', 'product_size.product_id', 'products.id')
            ->join('sizes', 'product_size.size_id', 'sizes.id')
            ->select('orderitems.id', 'orderitems.order_id', 'orderitems.items_id', 'products.name', 'orderitems.quantity', DB::raw('CAST(product_size.price AS int) AS price'), 'product_size.size_id', 'product_size.product_id', 'sizes.size')
            ->where('orderitems.order_id', $order->id)
            ->get();
        return response()->json($test);
    }
    public function collectionDetails(Request $request, $id)
    {
        $data = Payment::findorFail($id);

        $bank_id = explode('::', $data->bank_id);
        $bank = Bank::where('id', $bank_id)->first();
        $bank_name = explode('::', $bank->name);
        $name = implode('', $bank_name);
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
            'mRemaks' => $data->mRemarks
        );
        return response()->json($collection);
    }
}

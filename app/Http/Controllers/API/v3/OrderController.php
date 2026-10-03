<?php

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\Models\AlertReminder;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Schedulevisit;
use App\Models\LogDetail;
use App\Models\ProductSize;
use App\Models\Productimage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\SchedulePurpose;
use App\Models\Dealer;
use App\Models\Purpose;
use App\Models\ReminderPurpose;
use App\Models\Session;
use App\Models\VisitPurpose;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Geocodable;
use App\Traits\Nepali_Calendar;
use BcMath\Number;

class OrderController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter, Geocodable;
    public function index(Request $request)
    {
        $all = [];
        $user = Auth::user();
        $user_id = explode('::', $user->id);
        $id = implode('', $user_id);

        $area_code = explode(' :: ', $user->areas);
        $subarea = explode(' :: ', $user->sub_areas);

        $orderHistory = Order::select(
            'id',
            'dealer_code',
            'user_id',
            'date',
            'status',
            'remarks',
            'payment_commitment_date'
        )->where('user_id', '=', $id)
            ->whereHas('dealer', function ($query) use ($area_code, $subarea) {
                $query->whereIn('area_code', $area_code)
                    ->whereIn('subarea', $subarea)
                    ->where('active_status', 'Active');
            })
            ->orderBy('id', 'DESC')
            ->get();

        $amt = 0.0;
        $pending = 0.0;
        $approved = 0.0;
        $ongoing = 0.0;
        $dispatch = 0.0;

        foreach ($orderHistory as $order) {

            $dealer = DB::table('dealers')
                ->select('name', 'address')
                ->where('code', '=', $order->dealer_code)
                ->first();
            if ($order->status == 'Pending') {
                foreach (
                    Orderitem::where('order_id', $order->id)->get()
                    as $oi
                ) {
                    // return $oi->items_id;
                    $product = ProductSize::where('id', $oi->items_id)
                        ->select('price as productprice')
                        ->first();
                    $pending = round(
                        $pending + $oi->quantity * $product->productprice,
                        2
                    );
                }
            }

            if ($order->status == 'Approved') {
                foreach (
                    Orderitem::where('order_id', $order->id)->get()
                    as $oi
                ) {
                    $product = ProductSize::where('id', $oi->items_id)
                        ->select('price as productprice')
                        ->first();
                    $approved = round(
                        $approved + $oi->quantity * $product->productprice,
                        2
                    );
                }
            }

            if ($order->status == 'Sent to Dispatch') {
                foreach (
                    Orderitem::where('order_id', $order->id)->get()
                    as $oi
                ) {
                    $product = ProductSize::where('id', $oi->items_id)
                        ->select('price as productprice')
                        ->first();
                    $ongoing = round(
                        $ongoing + $oi->quantity * $product->productprice,
                        2
                    );
                }
            }

            if ($order->status == 'Dispatched') {
                foreach (
                    Orderitem::where('order_id', $order->id)->get()
                    as $oi
                ) {
                    $product = ProductSize::where('id', $oi->items_id)
                        ->select('price as productprice')
                        ->first();
                    $dispatch = round(
                        $dispatch + $oi->quantity * $product->productprice,
                        2
                    );
                }
            }
            foreach (Orderitem::where('order_id', $order->id)->get() as $oi) {
                $product = ProductSize::where('id', $oi->items_id)
                    ->select('price as productprice')
                    ->first();
                $amt = round($amt + $oi->quantity * $product->productprice, 2);
            }

            $test = DB::table('orderitems')
                ->join('product_size', 'orderitems.items_id', 'product_size.id')
                ->join('products', 'product_size.product_id', 'products.id')
                ->join('sizes', 'product_size.size_id', 'sizes.id')
                ->select(
                    'orderitems.id',
                    'orderitems.order_id',
                    'orderitems.items_id',
                    'products.name',
                    'orderitems.quantity',
                    'product_size.price',
                    'product_size.product_id',
                    'sizes.size'
                )
                ->where('orderitems.order_id', $order->id)
                ->get();

            $test = [
                'id' => $order->id,
                'dealerCode' => $order->dealer_code,
                'dealerName' => $dealer->name ?? null, // Added dealer name
                'dealerAddress' => $dealer->address ?? null, // Added dealer address
                'date' => $this->custom_AD_to_BS($order->date),
                'payment_commitment_date' => $order->payment_commitment_date
                    ? $this->custom_AD_to_BS($order->payment_commitment_date)
                    : null,
                'status' => $order->status,
                'remarks' => $order->remarks,
                'orders' => $test,
            ];
            array_push($all, (object) $test);
        }
        $data = $this->paginate($all);

        return response()->json([
            'order' => $data,
            'total_order' => $amt,
            'approved' => $approved,
            'sent_to_dispatch' => $ongoing,
            'pending' => $pending,
            'dispatched' => $dispatch,
        ]);
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items =
            $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $isUserCheckIn = $authUser?->latestAttendance?->status ?? null;

        $allowedStatuses = ['checkin', 'breakend'];

        if (!in_array(strtolower($isUserCheckIn), $allowedStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must check-in/break-end before placing an order.'
            ], 403);
        }

        $valid = Validator::make($request->all(), [
            'dealer_code' => 'required|exists:dealers,code',
            'main_dealer_code' => 'nullable|exists:dealers,code',
            'user_id' => 'required|exists:mobileusers,id',
            'date' => 'required|date_format:Y-m-d',
            'orders' => 'required|array',
            'orders.*.items_id' => 'required|exists:product_size,id',
            'orders.*.quantity' => 'required|min:1',
            'orders.*.custom_remarks' => 'nullable|max:191',
            'payment_commitment_date' => 'nullable|date_format:Y-m-d|after_or_equal:date',
            'committed_amount' => 'nullable|numeric|min:0|max:99999999',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:191',
            'status' => 'nullable|string|max:191|in:offline,online',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }

        $areaCodes  = array_map('trim', explode('::', $authUser->areas));
        $subAreas   = array_map('trim', explode('::', $authUser->sub_areas));

        DB::beginTransaction();
        try {
            $schedules = Schedulevisit::where([
                'user_id' => $request->user_id,
                'dealer_code' => $request->dealer_code,
                'date' => $request->date,
                'is_visit' => 0
            ])->get();

            $scheduleIds = $schedules->pluck('id');

            SchedulePurpose::whereIn('visitschedule_id', $scheduleIds)
                ->where('purpose_id', $request->purpose_id)
                ->update(['status' => 'visit']);

            $pendingSchedules = SchedulePurpose::whereIn('visitschedule_id', $scheduleIds)
                ->where('status', 'pending')
                ->pluck('visitschedule_id')
                ->toArray();

            $schedulesToUpdate = array_diff($scheduleIds->toArray(), $pendingSchedules);

            if (!empty($schedulesToUpdate)) {
                Schedulevisit::whereIn('id', $schedulesToUpdate)
                    ->update(['is_visit' => 1]);
            }

            $dealer = Dealer::where('code', $request->dealer_code)
                ->where('active_status', 'Active')
                ->first();

            if (!$dealer) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dealer does not exist'
                ], 404);
            }

            $dealerAreaCode = trim($dealer->area_code);
            $dealerSubAreaCode = trim($dealer->subarea);

            if (
                !in_array($dealerAreaCode, $areaCodes) ||
                !in_array($dealerSubAreaCode, $subAreas)
            ) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your are not authorized to place order for this dealer',
                ], 400);
            }

            $order = Order::create([
                'areacode' => $dealer->area_code,
                'dealer_code' => $request->dealer_code,
                'main_dealer_code' => $request->main_dealer_code,
                'user_id' => $request->user_id,
                'date' => $request->date,
                'remarks' => $request->remarks,
                'status' => 'Pending',
                'payment_commitment_date' => $request->payment_commitment_date,
                'committed_amount' => $request->committed_amount ? $request->committed_amount : 0,
                'lat_lng' => $request->latitude . ',' . $request->longitude,
            ]);

            $purpose = Purpose::whereIn('purpose', ['Order', 'order'])->first();

            if (isset($request->payment_commitment_date)) {
                $alert = AlertReminder::where([
                    'dealer_code' => $request->dealer_code,
                    'date' => $request->payment_commitment_date,
                    'column_id' => $order->id,
                    'table_name' => $order->getTable()
                ])->first();
                if (!$alert) {
                    $alert = new AlertReminder();
                }

                $alert->dealer_code = $request->dealer_code;
                $alert->date = $request->payment_commitment_date;
                $alert->column_id = $order->id;
                $alert->table_name = $order->getTable();
                $alert->remarks = $request->remarks;
                if (!$request->remarks) {
                    $alert->remarks = 'Order';
                }
                $alert->status = 'Pending';
                $alert->flag = 'Alert';
                $alert->mobileuser_id = auth('api')->user()->id;

                $alert->save();

                $purposeAlert = ReminderPurpose::where([
                    'purpose_id' => $purpose->id,
                    'alertreminder_id' => $alert->id,
                ])->first();
                if (!$purposeAlert) {
                    $purposeAlert = new ReminderPurpose();
                }

                $purposeAlert->purpose_id = $purpose->id;
                $purposeAlert->alertreminder_id = $alert->id;

                $purposeAlert->save();
            }

            $itemIds = collect($request->orders)->pluck('items_id');

            $products = ProductSize::whereIn('id', $itemIds)
                ->where('status', 1)
                ->get()
                ->keyBy('id');

            $totalOrder = 0;

            $newOrderItems = collect($request->orders)->map(function ($item) use ($products, $order, &$totalOrder) {
                $product = $products->get($item['items_id']);

                if (!$product) {
                    throw new \Exception('Product not found or inactive: ' . $item['items_id']);
                }

                $totalPrice = $product->price * $item['quantity'];
                $totalOrder += $totalPrice;

                return [
                    'order_id' => $order->id,
                    'items_id' => $item['items_id'],
                    'quantity' => $item['quantity'],
                    'quantity_updated' => $item['quantity'],
                    'product_rate' => $product->price,
                    'total_price' => $totalPrice,
                    'custom_unit_remarks' => $item['custom_remarks'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            if (!empty($newOrderItems)) {
                Orderitem::insert($newOrderItems);
            } else {
                throw new \Exception('No valid order items found');
            }

            $order->update(['total_price' => $totalOrder]);

            $time = now()->format('H:i:s');


            $session = Session::create([
                'dealer_code' => $request->dealer_code,
                'user_id' => $request->user_id,
                'areacode' => $dealer->area_code,
                'purpose_id' => $purpose->id,
                'start_time' => $time,
                'end_time' => $time,
                'date' => $request->date,
                'status' => $request->status ?? 'online',
                'remarks' => $request->remarks,
                'lat_lng' => $request->latitude && $request->longitude ? $request->latitude . ',' . $request->longitude : null,
            ]);

            VisitPurpose::create([
                'user_id' => $session->user_id,
                'visit_id' => $session->id,
                'purpose_id' => $purpose->id,
                'date' => $request->date,
                'status' => $request->status ?? 'online',
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Success',
                'order_id' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::findorFail($id);
        $order->date = $this->custom_AD_to_BS($order->date);
        $order->payment_commitment_date    = ($order->payment_commitment_date) ? $this->custom_AD_to_BS($order->payment_commitment_date) : null;
        $test = DB::table('orderitems')
            ->join('product_size', 'orderitems.items_id', 'product_size.id')
            ->join('products', 'product_size.product_id', 'products.id')
            ->join('sizes', 'product_size.size_id', 'sizes.id')
            ->select('orderitems.id', 'orderitems.order_id', 'orderitems.items_id', 'products.name', 'orderitems.quantity', 'product_size.price', 'product_size.size_id', 'product_size.product_id', 'sizes.size')
            ->where('orderitems.order_id', $order->id)
            ->get();
        $order->orders = $test;
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'remarks' => 'required',
        ]);
        $order = Order::findorFail($id);

        if ($order->status == 'Pending') {
            $order->status = 'Declined';
            $order->remarks = $request->remarks;
            $order->save();

            $log = new LogDetail();
            $date = date('Y-m-d');

            $log->ordercoll_id = $id;
            $log->flag = 'order';
            // $log->cheque_no = $payment->cheque_num;
            $log->date = $date;
            $log->mo_user = $order->user_id;
            $log->user_id = $order->user_id;
            $log->status = "Declined";
            $log->remarks = $request->remarks;
            $log->previous_status = $order->status;

            $log->save();

            // }

            $item = DB::table('orderitems')
                ->join('product_size', 'orderitems.items_id', 'product_size.id')
                ->join('products', 'product_size.product_id', 'products.id')
                ->join('sizes', 'product_size.size_id', 'sizes.id')
                ->select('orderitems.id', 'products.id as pid', 'orderitems.order_id', 'orderitems.items_id', 'products.name', 'orderitems.quantity', 'product_size.price', 'product_size.size_id', 'product_size.product_id', 'sizes.size')
                ->where('orderitems.order_id', $order->id)
                ->get();
            foreach ($item as $t) {
                $image = Productimage::where('product_id', $t->pid)->select('image', 'thumbnail')->get();

                $lis[] = array(
                    'id' => $t->id,
                    'order_id' => $t->order_id,
                    'items_id' => $t->items_id,
                    'name' => $t->name,
                    'quantity' => $t->quantity,
                    'price' => $t->price,
                    'size_id' => $t->size_id,
                    'product_id' => $t->product_id,
                    'size' => $t->size,
                    'image' => $image,
                );
            }

            $list = array(
                'order_id' => $order->id,
                'dealer_code' => $order->dealer_code,
                'user_id' => $order->user_id,
                'date' => $order->date,
                'status' => $order->status,
                'remarks' => $order->remarks,
                'items' => $lis
            );
            return response()->json($list);
        } else {

            return response()->json('you cannot cancelled this order because your order has been changed.');
        }
    }

    public function cancelOrderItem($id)
    {
        $item = Orderitem::findorFail($id);

        $order = Order::findorFail($item->order_id);

        if ($order->status == 'Pending') {

            if (count(Orderitem::where('order_id', $item->order_id)->get()) == '1') {
                $order->status = 'Declined';
                // $order->remarks = $request->remarks;
                $order->save();
                $item = DB::table('orderitems')
                    ->join('product_size', 'orderitems.items_id', 'product_size.id')
                    ->join('products', 'product_size.product_id', 'products.id')
                    ->join('sizes', 'product_size.size_id', 'sizes.id')
                    ->select('orderitems.id', 'products.id as pid', 'orderitems.order_id', 'orderitems.items_id', 'products.name', 'orderitems.quantity', 'product_size.price', 'product_size.size_id', 'product_size.product_id', 'sizes.size')
                    ->where('orderitems.order_id', $order->id)
                    ->get();
                foreach ($item as $t) {
                    $image = Productimage::where('product_id', $t->pid)->select('image', 'thumbnail')->get();

                    $lis[] = array(
                        'id' => $t->id,
                        'order_id' => $t->order_id,
                        'items_id' => $t->items_id,
                        'name' => $t->name,
                        'quantity' => $t->quantity,
                        'price' => $t->price,
                        'size_id' => $t->size_id,
                        'product_id' => $t->product_id,
                        'size' => $t->size,
                        'image' => $image,
                    );
                }

                $list = array(
                    'order_id' => $order->id,
                    'dealer_code' => $order->dealer_code,
                    'user_id' => $order->user_id,
                    'date' => $order->date,
                    'status' => $order->status,
                    'remarks' => $order->remarks,
                    'items' => $lis
                );
                // $item->status = '1';
                // $item->save();
                return response()->json($list);
            }

            // $item->status = '1';
            // $item->save();

            $item->delete();

            $items = DB::table('orderitems')
                ->join('product_size', 'orderitems.items_id', 'product_size.id')
                ->join('products', 'product_size.product_id', 'products.id')
                ->join('sizes', 'product_size.size_id', 'sizes.id')
                ->select('orderitems.id', 'products.id as pid', 'orderitems.order_id', 'orderitems.status', 'orderitems.items_id', 'products.name', 'orderitems.quantity', 'product_size.price', 'product_size.size_id', 'product_size.product_id', 'sizes.size')
                ->where('orderitems.order_id', $order->id)
                ->get();
            foreach ($items as $t) {
                $image = Productimage::where('product_id', $t->pid)->select('image', 'thumbnail')->get();

                $lis[] = array(
                    'id' => $t->id,
                    'order_id' => $t->order_id,
                    'items_id' => $t->items_id,
                    'name' => $t->name,
                    'quantity' => $t->quantity,
                    'price' => $t->price,
                    'size_id' => $t->size_id,
                    'status' => $t->status,
                    'product_id' => $t->product_id,
                    'size' => $t->size,
                    'image' => $image,
                );
            }

            $list = array(
                'order_id' => $order->id,
                'dealer_code' => $order->dealer_code,
                'user_id' => $order->user_id,
                'date' => $order->date,
                'status' => $order->status,
                'remarks' => $order->remarks,
                'items' => $lis
            );
            return response()->json($list);
        } else {
            return response()->json(['status' => 'error', 'message' => 'This Order has already been  Approved or Cancelled.']);
        }
    }

    public function filter(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'dealer' => 'nullable|string|max:191',
            'status' => 'nullable|string|max:191',
            'from' => 'nullable|required_with:to|date|date_format:Y-m-d',
            'to' => 'nullable|required_with:from|date||date_format:Y-m-d',
            'subarea' => 'nullable|string|max:191',
            'sorting' => 'nullable|string|max:191|in:ASC,DESC'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $sort = $request->sorting;
        if (!$sort) {
            $sort = 'DESC';
        }
        $amt = 0.0;
        $pending = 0.0;
        $approved = 0.0;
        $ongoing = 0.0;
        $dispatch = 0.0;
        $status = explode(',', $request->status);

        $user = Auth::user();


        if ($request->dealer && $request->from) {
            $orders = DB::table('orders')
                ->join('dealers', 'dealers.code', 'orders.dealer_code')
                ->select('id', 'dealer_code as dealerCode', 'user_id', 'date', 'dealers.name', 'orders.status', 'remarks', 'payment_commitment_date', 'committed_amount')
                ->where('user_id', '=', $user->id)
                ->where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->wherebetween('orders.date', [$request->from, $request->to])
                ->orderBy('orders.created_at', $sort)
                ->paginate(10);

            $amt = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->wherebetween('orders.date', [$request->from, $request->to])->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $pending = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Pending')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $approved = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Approved')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $ongoing = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Sent to Dispatch')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $dispatch = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Dispatched')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
        } else if ($request->dealer && !$request->from) {
            $orders = DB::table('orders')
                ->join('dealers', 'dealers.code', 'orders.dealer_code')
                ->select('id', 'dealer_code as dealerCode', 'user_id', 'date', 'dealers.name', 'orders.status', 'remarks', 'payment_commitment_date', 'committed_amount')
                ->where('user_id', '=', $user->id)
                ->where('dealers.name', 'like', '%' . $request->dealer . '%')
                ->orderBy('orders.created_at', $sort)
                ->paginate(10);

            $amt = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $pending = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->where('orders.status', 'Pending')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $approved = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->where('orders.status', 'Approved')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $ongoing = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->where('orders.status', 'Sent to Dispatch')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
            $dispatch = number_format(Order::join('dealers', 'dealers.code', 'orders.dealer_code')->where('orders.status', 'Dispatched')->where('dealers.name', 'like', '%' . $request->dealer . '%')->sum('orders.total_price'), 2, '.', '');
        } else if (!$request->dealer && $request->from) {
            $orders = DB::table('orders')
                ->join('dealers', 'dealers.code', 'orders.dealer_code')
                ->select('id', 'dealer_code as dealerCode', 'user_id', 'date', 'dealers.name', 'orders.status', 'remarks', 'payment_commitment_date', 'committed_amount')
                ->where('user_id', '=', $user->id)
                ->wherebetween('orders.date', [$request->from, $request->to])
                ->orderBy('orders.created_at', $sort)
                ->paginate(10);

            $amt = number_format(Order::wherebetween('orders.date', [$request->from, $request->to])->sum('orders.total_price'), 2, '.', '');
            $pending = number_format(Order::wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Pending')->sum('orders.total_price'), 2, '.', '');
            $approved = number_format(Order::wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Approved')->sum('orders.total_price'), 2, '.', '');
            $ongoing = number_format(Order::wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Sent to Dispatch')->sum('orders.total_price'), 2, '.', '');
            $dispatch = number_format(Order::wherebetween('orders.date', [$request->from, $request->to])->where('orders.status', 'Dispatched')->sum('orders.total_price'), 2, '.', '');
        } else {
            $orders = DB::table('orders')
                ->join('dealers', 'dealers.code', 'orders.dealer_code')
                ->select('id', 'dealer_code as dealerCode', 'user_id', 'date', 'dealers.name', 'orders.status', 'remarks', 'payment_commitment_date', 'committed_amount')
                ->where('user_id', '=', $user->id)
                ->orderBy('orders.created_at', $sort)
                ->paginate(10);

            $amt = number_format(Order::sum('orders.total_price'), 2, '.', '');
            $pending = number_format(Order::where('orders.status', 'Pending')->sum('orders.total_price'), 2, '.', '');
            $approved = number_format(Order::where('orders.status', 'Approved')->sum('orders.total_price'), 2, '.', '');
            $ongoing = number_format(Order::where('orders.status', 'Sent to Dispatch')->sum('orders.total_price'), 2, '.', '');
            $dispatch = number_format(Order::where('orders.status', 'Dispatched')->sum('orders.total_price'), 2, '.', '');
        }
        foreach ($orders as $order) {
            $order->date = $this->custom_AD_to_BS($order->date);
        }
        return response()->json(['order' => $orders, 'total_order' => $amt, 'approved' => $approved, 'sent_to_dispatch' => $ongoing, 'pending' => $pending, 'dispatched' => $dispatch]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LogDetail;
use App\Models\Orderitem;
use App\Models\Mobileuser;
use App\Http\Controllers\NotificationController;
use App\Models\DealerUser;
use App\Models\ProductSize;
use App\Models\Category;
use App\Models\Size;
use App\Models\DealerInfo;
use App\Models\Challan;
use App\Traits\FCM;
use App\Traits\Nepali_Calendar;

class ChallanController extends Controller
{
    use FCM;
    use Nepali_Calendar;

    public function listChallan()
    {
        $dchln = [];
        $chln = [];
        $challans = DB::table('challans')
            ->join('orders', 'challans.order_id', 'orders.id')
            ->select('challans.id', 'challans.order_id', 'group_id')
            ->groupBy('challans.group_id')
            ->get();

        foreach ($challans as $challan) {
            $order = Order::findorFail($challan->group_id);
            if ($order->dealer_code) {
                $dchln = DB::table('challans')
                    ->join('orders', 'challans.order_id', 'orders.id')
                    ->join('dealers', 'orders.dealer_code', 'dealers.code')
                    ->join('mobileusers', 'orders.user_id', 'mobileusers.id')
                    ->select('challans.id', 'challans.order_id', 'challans.group_id', 'mobileusers.name as user_name', 'dealers.name as dealer_name', 'dealers.code as dealerid')
                    ->groupBy('challans.group_id')
                    ->get()->toArray();
            }
            if ($order->tint_user) {
                $chln = DB::table('challans')
                    ->join('orders', 'challans.order_id', 'orders.id')
                    ->join('dealers', 'orders.dealer_code', 'dealers.code')
                    ->join('dealer_user_info', 'orders.tint_user', 'dealer_user_info.id')
                    ->select('challans.id', 'challans.order_id', 'challans.group_id', 'dealer_user_info.full_name as user_name', 'dealers.name as dealer_name', 'dealers.code as dealerid')
                    ->groupBy('challans.group_id')
                    ->get()->toArray();
            }
        }
        $all = array_merge($dchln, $chln);
        $s = Dealer::get()->toArray();
        $t = DealerUser::where('user_role', 1)->get()->toArray();
        $dealers = array_merge($s, $t);
        return view('dashboards.orders.challanList', ['challans' => $all, 'dealers' => $dealers]);
    }

    function challan(Request $request, $id)
    {
        $mo = "";
        $o = Order::findOrFail($id);

        if ($o->user_id) {
            $mobileuser = Mobileuser::find($o->user_id);
            $mo = $mobileuser?->name ?? '';
        }

        $dealer = Dealer::where('code', $o->dealer_code)->first();
        $categories = Category::where('status', 'Active')->orderBy('name')->get(['id', 'name']);

        $sizes = Size::select('id', 'size', 'flag')
            ->where('flag', 'challan')
            ->orderBy('sizes.order_index', 'asc') // order by index column
            ->pluck('sizes.size')
            ->toArray();

        // Colorants
        $allOrderVariants = Orderitem::where('order_id', $id)
            ->join('product_size', 'product_size.id', '=', 'orderitems.items_id')
            ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
            ->select('sizes.id', 'sizes.size')
            ->distinct()
            ->pluck('sizes.size')
            ->toArray();

        // Remove duplicates from $allOrderVariants that are already in $sizes
        $colorants = array_diff($allOrderVariants, $sizes);



        // Grouped order items
        $order = Orderitem::where('order_id', $id)
            ->with(['items.product', 'items.size'])
            ->get()
            ->groupBy(fn($item) => $item->items->product_id);

        $finalData = [];

        foreach ($order as $productItems) {
            $firstItem = $productItems->first();
            $product = $firstItem->items->product;

            $sizeData = [];
            foreach ($productItems as $item) {
                $size = $item->items->size->size;
                $qty = $item->quantity;
                $amt = $qty * $item->items->price;

                if (!isset($sizeData[$size])) {
                    $sizeData[$size] = ['qty' => 0, 'amt' => 0];
                }

                $sizeData[$size]['qty'] += $qty;
                $sizeData[$size]['amt'] += $amt;
            }

            $finalData[] = [
                'product' => $product->name,
                'sizes' => $sizeData
            ];
        }

        return view('dashboards.orders.challan', [
            'sizes' => $sizes,
            'orders' => $finalData,
            'dealer' => $dealer,
            'mo' => $mo,
            'colorants' => $colorants,
            'categories' => $categories,
            'orderId' => $o->id,
        ]);
    }

    public function addProductsToOrder(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|exists:categories,id',
            'items.*.product' => 'required|exists:products,id',
            'items.*.size' => 'required|exists:product_size,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $order = Order::findOrFail($id);

        DB::beginTransaction();

        try {
            $addedCount = 0;
            $addedAmount = 0;

            foreach ($validated['items'] as $item) {
                $productSize = ProductSize::with('product')
                    ->where('id', $item['size'])
                    ->where('product_id', $item['product'])
                    ->where('status', 1)
                    ->firstOrFail();

                if ((int) $productSize->product->category_id !== (int) $item['category']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid category/product combination selected.'
                    ], 422);
                }

                $qty = (int) $item['qty'];
                $lineAmount = (float) $productSize->price * $qty;

                $existingOrderItem = Orderitem::where('order_id', $order->id)
                    ->where('items_id', $productSize->id)
                    ->where('status', 0)
                    ->first();

                if ($existingOrderItem) {
                    $unitRate = (float) ($existingOrderItem->product_rate ?: $productSize->price);
                    $existingOrderItem->quantity += $qty;
                    $existingOrderItem->quantity_update = $existingOrderItem->quantity;
                    $existingOrderItem->total_price += $qty * $unitRate;
                    $existingOrderItem->save();

                    $lineAmount = $qty * $unitRate;
                } else {
                    $orderItem = new Orderitem();
                    $orderItem->order_id = $order->id;
                    $orderItem->items_id = $productSize->id;
                    $orderItem->quantity = $qty;
                    $orderItem->quantity_update = $qty;
                    $orderItem->product_rate = $productSize->price;
                    $orderItem->total_price = $lineAmount;
                    $orderItem->discount = 0;
                    $orderItem->vat = 0;
                    $orderItem->save();
                }

                $addedCount++;
                $addedAmount += $lineAmount;
            }

            $order->total_price = (float) $order->total_price + $addedAmount;
            $order->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Products added to order successfully.',
                'added_count' => $addedCount,
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to add products at the moment. Please try again.',
            ], 500);
        }
    }


    public function singleChallan($id)
    {

        $o = Order::findorFail($id);

        $suser = $o->user_id;
        $dealer = $o->dealer_code;

        $tuser = $o->tint_user;

        if ($suser) {

            $order = DB::table('orders')
                ->join('challans', 'challans.order_id', '=', 'orders.id')
                ->join('product_size', 'product_size.id', '=', 'challans.items_id')
                ->join('products', 'products.id', '=', 'product_size.product_id')
                ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
                ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
                ->join('mobileusers', 'mobileusers.id', '=', 'orders.user_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->select('orders.id as order_id', 'challans.quantity as quantity', 'product_size.price as price', 'challans.id as sizeid', 'products.name as product_name', 'sizes.size as size', 'dealers.name as dealer_name', 'orders.date as order_date', 'orders.status as order_status', 'mobileusers.name as user_name', 'categories.name as category_name', 'orders.remarks')
                ->where('orders.dealer_code', $dealer)
                ->where('orders.user_id', $suser)
                ->where('challans.group_id', $id)
                ->get();
        }
        if ($tuser) {
            $order = DB::table('orders')
                ->join('challans', 'challans.order_id', '=', 'orders.id')
                ->join('product_size', 'product_size.id', '=', 'challans.items_id')
                ->join('products', 'products.id', '=', 'product_size.product_id')
                ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
                ->join('dealers', 'dealers.code', '=', 'orders.dealer_code')
                ->join('dealer_user_info', 'dealer_user_info.id', '=', 'orders.tint_user')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->select('orders.id as order_id', 'challans.quantity as quantity', 'product_size.price as price', 'challans.id as sizeid', 'products.name as product_name', 'sizes.size as size', 'dealers.name as dealer_name', 'orders.date as order_date', 'orders.status as order_status', 'dealer_user_info.full_name as user_name', 'categories.name as category_name', 'orders.remarks')
                ->where('orders.dealer_code', $dealer)
                ->where('orders.tint_user', $tuser)
                ->where('challans.group_id', $id)
                ->get();
        }
        //    $order = array_merge($sorder->toArray(),$torder->toArray());
        // return $order;

        foreach ($order as $n) {
            $explode = explode('::', $n->order_date);
            $implode = implode('', $explode);
            $date = explode('-', $implode);
            $currentYear = $date[0];
            $currentMonth = $date[1];
            $currentDay = $date[2];
            $currentNepaliDay = $this->AD_to_BS($currentYear, $currentMonth, $currentDay);
            $nepaliDay = $currentNepaliDay['date'];
            $nepaliMth = $currentNepaliDay['month'];
            $d = strlen($nepaliMth);
            if ($d == 1) {
                $nepaliMth = '0' . $nepaliMth;
            }
            $nepaliYr = $currentNepaliDay['year'];
            $order_date = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
            $dName = $n->dealer_name;
            $mo = $n->user_name;
            $orderid = $n->order_id;
            $list[] = array(
                'order_id' => $n->order_id,
                'dealer_name' => $n->dealer_name,
                'order_status' => $n->order_status,
                'quantity' => $n->quantity,
                'price' => $n->price,
                'product_name' => $n->product_name,
                'size' => $n->size,
                'category_name' => $n->category_name,
                'order_date' => $order_date,
                'remarks' => $n->remarks,
                'user_name' => $n->user_name,
                'sizeid' => $n->sizeid,

            );
        }
        if (empty($list)) {
            return view('dashboards.orders.challan', ['order' => '', 'dealer' => '', 'mo' => '']);
        } else {
            return view('dashboards.orders.challan', ['order' => $list, 'dealer' => $dName, 'mo' => $mo]);
        }
    }
    public function challanEditView($id)
    {
        Orderitem::findorFail($id);
        $order = DB::table('orderitems')
            ->join('product_size', 'orderitems.items_id', 'product_size.id')
            ->join('products', 'product_size.product_id', 'products.id')
            ->join('sizes', 'product_size.size_id', 'sizes.id')
            ->select('orderitems.id', 'orderitems.order_id as order_id', 'products.name', 'product_size.id as productsize_id', 'sizes.size', 'product_size.price', 'orderitems.quantity')
            ->where('orderitems.id', $id)
            ->get();

        return view('dashboards.orders.edit', ['orders' => $order]);
    }

    public function challanAdd($id, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $orderItem = Orderitem::findOrFail($id);
        $order     = $orderItem->order;

        if ($request->quantity == $orderItem->quantity) {
            return response()->json([
                'status' => 'info',
                'message' => 'Previous Quantity is equal to your edited Quantity.'
            ]);
        }

        $price = $orderItem->items->price;

        $order->total_price -= $price * $orderItem->quantity;
        $order->total_price += $price * $request->quantity;
        $order->save();

        $orderItem->update([
            'quantity' => $request->quantity,
            'total_price' => $price * $request->quantity
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order has been updated.',
            'order_total_price' => $order->total_price,
            'order_item_total_price' => $orderItem->total_price
        ]);
    }

    // public function availability($id)
    // {
    //     $order = Orderitem::findorFail($id);
    //     $ord = Order::where('id', $order->order_id)->first();

    //     $isUnavailable = false;
    //     $message = '';

    //     if (count(Orderitem::where('order_id', $ord->id)->get()) == 1) {
    //         if ($order->status == 0) {
    //             $order->status = '1';
    //             $order->save();
    //             $isUnavailable = true;
    //             $message = 'Order Unavailable.';
    //         } else {
    //             $order->status = '0';
    //             $order->save();
    //             $isUnavailable = false;
    //             $message = 'Order Available';
    //         }
    //     } else {
    //         if ($order->status == 0) {
    //             $o = new Order();
    //             $o->dealer_code = $ord->dealer_code;
    //             $o->user_id = $ord->user_id;
    //             $o->tint_user = $ord->tint_user;
    //             $o->date = $ord->date;
    //             $o->status = $ord->status;
    //             $o->remarks = $ord->remarks;
    //             // $o->app = $ord->app;
    //             $o->save();

    //             $newOrder = Order::where('dealer_code', $ord->dealer_code)
    //                 ->where('user_id', $ord->user_id)
    //                 ->where('tint_user', $ord->tint_user)
    //                 ->where('date', $ord->date)
    //                 ->orderBy('id', 'DESC')->first();

    //             $item = new Orderitem();
    //             $item->order_id = $newOrder->id;
    //             $item->items_id =  $order->items_id;
    //             $item->quantity = $order->quantity;
    //             $item->quantity_update = $order->quantity;
    //             $item->status = 0;
    //             $item->save();

    //             $order->status = '1';
    //             $order->save();
    //             $isUnavailable = true;
    //             $message = 'Order Unavailable.';
    //         } else {
    //             $order->status = '0';
    //             $order->save();
    //             $isUnavailable = false;
    //             $message = 'Order Available';
    //         }
    //     }

    //     if (request()->ajax()) {
    //         return response()->json([
    //             'success' => true,
    //             'status' => $isUnavailable ? 'unavailable' : 'available',
    //             'message' => $message,
    //             'orderitems_id' => $order->id,
    //             'order_status' => $order->status
    //         ]);
    //     } else {
    //         return redirect('/order')->with('Status', $message);
    //     }
    // }

    public function availability($id)
    {
        $orderItem = Orderitem::findOrFail($id);
        $originalOrder = Order::where('id', $orderItem->order_id)->firstOrFail();

        $isUnavailable = false;
        $message = '';

        // Count how many items belong to the original order
        $itemCount = Orderitem::where('order_id', $originalOrder->id)->count();

        if ($itemCount == 1) {
            // Only one item in this order – toggle its status directly
            if ($orderItem->status == 0) {
                $orderItem->status = '1';
                $orderItem->save();
                $isUnavailable = true;
                $message = 'Order Unavailable.';
            } else {
                $orderItem->status = '0';
                $orderItem->save();
                $isUnavailable = false;
                $message = 'Order Available';
            }
        } else {
            // More than one item – we need to split the order
            if ($orderItem->status == 0) {
                // Clone the entire order (copies all columns except the primary key)
                $newOrder = $originalOrder->replicate();
                // Let Laravel set fresh timestamps
                $newOrder->created_at = null;
                $newOrder->updated_at = null;
                $newOrder->save();

                // Attach the current item to the new order
                $newItem = new Orderitem();
                $newItem->order_id = $newOrder->id;
                $newItem->items_id = $orderItem->items_id;
                $newItem->quantity = $orderItem->quantity;
                $newItem->quantity_update = $orderItem->quantity;
                $newItem->status = 0;          // available
                $newItem->save();

                // Mark the original item as unavailable
                $orderItem->status = '1';
                $orderItem->save();

                $isUnavailable = true;
                $message = 'Order Unavailable.';
            } else {
                // Item is currently unavailable – toggle back to available
                $orderItem->status = '0';
                $orderItem->save();
                $isUnavailable = false;
                $message = 'Order Available';
            }
        }

        // Response handling
        if (request()->ajax()) {
            return response()->json([
                'success'       => true,
                'status'        => $isUnavailable ? 'unavailable' : 'available',
                'message'       => $message,
                'orderitems_id' => $orderItem->id,
                'order_status'  => $orderItem->status,
            ]);
        }

        return redirect('/order')->with('Status', $message);
    }


    public function singleChallanAdd($id)
    {
        $odr = Order::findorFail($id);
        $order = Orderitem::where('order_id', $id)->where('status', '0')->get();
        if (count($order) == 0) {
            return redirect('/order')->with('Status', 'Product unavailable for this Order.');
        } else {
            foreach ($order as $or) {
                $challan = new Challan();
                $challan->order_id = $or->order_id;
                $challan->items_id = $or->items_id;
                $challan->quantity = $or->quantity;
                $challan->group_id = $id;
                $challan->remarks = $or->custom_unit_remarks;
                $challan->save();
            }
            $log = new LogDetail();
            $log->flag = 'order';
            $log->ordercoll_id = $odr->id;
            if ($odr->tint_user == null) {
                $log->user_id = $odr->user_id;
            } else {
                $log->tint_user = $odr->tint_user;
            }
            // $log->cheque_no = $odr->cheque_num;
            $log->date = date('Y-m-d');
            $log->admin = Auth::user()->name;
            // $log->user_id = $odr->user_id;
            $log->status = 'Sent to Dispatch';
            $log->remarks = '';
            $log->previous_status = $odr->status;
            $log->save();

            $odr->status = 'Sent to Dispatch';
            $odr->save();
            $otp = Mobileuser::where(['id' => $odr->user_id])->first();
            if ($otp) {
                $fcmid = $otp->fcm_id;
                $flag = "order";
                $title = "Sales Tracker";
                $detail = $odr->id;
                $app = 0;
                $content = array('Title' => 'Order Status Updated', 'Message' => 'Challan Created', 'order_id' => $detail, 'Status' => 'ongoing');
                if ($fcmid) {
                    $notice = new NotificationController();
                    $notice->individualNotificationSend($fcmid, $title, $flag, $content, $app);
                }
            }
            $otp1 = DealerUser::where(['id' => $odr->tint_user])->first();
            if ($otp1) {
                $fcmid = $otp1->fcm_id;
                $flag = "order";
                $title = "Sales Tracker";
                $detail = $odr->id;
                $app = 1;
                $content = array('Title' => 'Order Status Updated', 'Message' => 'Challan Created', 'order_id' => $detail, 'Status' => 'ongoing');
                if ($fcmid) {
                    $notice = new NotificationController();
                    $notice->individualNotificationSend($fcmid, $title, $flag, $content, $app);
                }
            }
            return redirect('/order/challan/' . $odr->id);
        }
    }


    public function allChallan($id)
    {
        $o = Order::findorFail($id);
        $suser = $o->user_id;
        $dealer = $o->dealer_code;

        $tuser = $o->tint_user;


        if ($dealer) {
            $orders = DB::table('orders')
                ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
                ->select('orderitems.id', 'orderitems.order_id', 'orderitems.quantity', 'orderitems.quantity_update', 'orders.status', 'orders.user_id', 'orders.tint_user', 'orderitems.items_id')
                ->where('orders.dealer_code', $dealer)
                ->where('orders.user_id', $suser)
                ->where('orders.status', $o->status)
                ->where('orderitems.status', '0')
                ->get();
            $logord = Order::where('orders.dealer_code', $dealer)
                ->where('orders.user_id', $suser)
                ->where('orders.status', $o->status)
                ->get();
        }
        if ($dealer) {
            $orders = DB::table('orders')
                ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
                ->select('orderitems.id', 'orderitems.order_id', 'orderitems.quantity', 'orderitems.quantity_update', 'orders.status', 'orders.tint_user', 'orders.user_id', 'orderitems.items_id')
                ->where('orders.dealer_code', $dealer)
                ->where('orders.tint_user', $tuser)
                ->where('orders.status', $o->status)
                ->where('orderitems.status', '0')
                ->get();
            $logord = Order::where('orders.dealer_code', $dealer)
                ->where('orders.user_id', $tuser)
                ->where('orders.status', $o->status)
                ->get();
        }
        // return $logord;
        if (!empty($orders)) {


            foreach ($orders as $order) {
                $challan = new Challan();

                $challan->order_id = $order->order_id;
                $challan->items_id = $order->items_id;
                $challan->quantity = $order->quantity;
                $challan->group_id = $id;
                $challan->save();
            }
            foreach ($logord as $lo) {
                $log = new LogDetail();
                $log->flag = 'order';
                $log->ordercoll_id = $lo->id;
                // $log->cheque_no = '';
                $log->date = date('Y-m-d');
                $log->admin = Auth::user()->id;
                if ($order->user_id) {
                    $log->user_id = $order->user_id;
                }
                if ($order->tint_user) {
                    $log->tint_user = $order->tint_user;
                }
                $log->status = 'ongoing';
                $log->remarks = '';
                $log->previous_status = $order->status;
                $log->save();
                $otp = Mobileuser::where(['id' => $lo->user_id])->first();
                if ($otp) {
                    $fcmid = $otp->fcm_id;
                    $flag = "order";
                    $title = "Sales Tracker";
                    $detail = $lo->id;
                    $app = 0;
                    $content = array('Title' => 'Order Status Updated', 'Message' => 'Challan Created', 'order_id' => $detail, 'Status' => 'ongoing');
                    if ($fcmid) {
                        $notice = new NotificationController();
                        $notice->individualNotificationSend($fcmid, $title, $flag, $content, $app);
                    }
                }
                $otp1 = DealerUser::where(['id' => $lo->tint_user])->first();
                if ($otp1) {
                    $fcmid = $otp1->fcm_id;
                    $flag = "order";
                    $title = "Sales Tracker";
                    $app = 1;
                    // $content = array('Title'=>'Cheque Status Updated', 'Message' => $remark, 'payment_id' =>$payment->id,'Status'=> request('status') );
                    $content = array('Title' => 'Order Status Updated', 'Details' => 'Challan Created');
                    if ($fcmid) {
                        $notice = new NotificationController();
                        $notice->individualNotificationSend($fcmid, $title, $flag, $content, $app);
                    }
                }


                Order::where('id', $lo->id)->update(['status' => 'ongoing']);
            }
            return redirect('/order/challan/' . $id);
        } else {
            return redirect('/order')->with('Status', 'Product unavailable for this Order.');
        }
    }
}

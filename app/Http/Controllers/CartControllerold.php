<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Dealer;
use App\Models\Mobileuser;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\Orderitem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\Subproduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CartController extends Controller
{
    use ValidatesRequests;
    /**
     * Store the product details to cart
     * and response back the product details
     */
    public function storeToCart(Request $request)
    {
        // return $request;
        $error = Validator::make($request->all(), [
            'cartData'=>'required',
            'cartData.*.size' => 'required',
            // 'cartData.*.product' => 'required|exists:product_size,id',
            // 'cartData.*.qty' => 'required|max:10000|min:1',

        ]);
        if ($error->fails()) {
            // return back()->withErrors($error,422);
            return response()->json(['status'=>'error','message'=>$error->errors()],422);
        }
        // return response()->json($request);
        Db::beginTransaction();
        try {
            $allData = [];
            foreach ($request->cartData as $cart) {
                $size = explode('x', strtoLower($cart['size']));
                $cartD = new Cart();
                $cartD->finalproduct_id = $cart['product'];
                $cartD->user_id = $cart['mobUser'];
                $cartD->dealer_code = $cart['dealer'];
                $cartD->quantity = $cart['qty'];
                $cartD->custom_length = $size[0];
                $cartD->custom_width = $size[1];
                $cartD->created_by = Auth::user()->id;
                $cartD->save();
                $cart['id'] = $cartD->id;
                $cart['length'] =  $size[0];
                $cart['width'] = $size[1];
                array_push($allData, $cart);
            }
            // $data['carts'] = Cart::where(['user_id' => $cart['mobUser'], 'dealer_code' => $cart['dealer']])->get();
            Db::commit();
            return response()->json([
                'message' => 'success',
                'data' => $allData
            ], 200);

            return view('dashboards.carts.orderlist');
        } catch (Exception $e) {
            Db::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function addOrderstore(Request $request)
    {
        $this->validate($request, [
            'orderreq' => 'required|exists:order_images,id',
            'user_id' => 'required|exists:mobileusers,id',
            'dealer_code' => 'required|exists:dealers,code',
        ]);

        try {
            DB::beginTransaction();
            $oi = OrderImage::where('id', $request->orderreq)->first();

            if ($oi->status != "pending") {
                return back()->with('status', 'This order request has been expired');
            }

            $carts = Cart::with('productSize', 'dealer')->where(['user_id' => $request->user_id, 'dealer_code' => $request->dealer_code])->get();
            $dealer = Dealer::where('code', $request->dealer_code)->first();
            $order = new Order();
            $order->user_id = $request->user_id;
            $order->areacode = $dealer->area_code;
            $order->dealer_code = $request->dealer_code;
            $order->date = date('Y-m-d');
            $order->status = 'Draft';
            $order->save();


            $totalorder = 0;

            foreach ($carts as $item) {
                $product = ProductSize::where('id', $item->finalproduct_id)->first();

                if ($product->size->size == "CUSTOM") {
                    $price = (($item->height * $item->width) / 144) * $product->price * $item->quantity;
                    $totalorder = $totalorder + $price;
                    $area = (($item->height * $item->width)) * $item->quantity / 144;
                    $weight = ($item->height * $item->width * $item->quantity * $product->weight_per_sq_ft) / 144;
                    $size = $item->height . "x" . $item->width;
                } else {
                    $price = $product->price * $item->quantity;
                    $totalorder = $totalorder + $price;
                    $area = $product->SQFT * $item->quantity;
                    $weight = $product->SQFT * $item->quantity * $product->weight_per_sq_ft;
                    $size = $product->size->size;
                }


                $discount = $price * $dealer->discount / 100;
                $array[] = array(
                    'order_id' => $order->id,
                    'items_id' => $item->finalproduct_id,
                    'quantity' => $item->quantity,
                    'quantity_update' => $item->quantity,
                    'product_rate' => $product->price,
                    'total_price' => $price,
                    'discount' => $discount,
                    'vat' => ($price - $discount) * 13 / 100,
                    'height' => $item->custom_length,
                    'width' => $item->custom_width,
                    'size' => $size,
                    'area' => $area,
                    'weight' => $weight,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
            }

            Orderitem::insert($array);
            $total_discount = $dealer->discount * $totalorder / 100;
            $order->total_price = $totalorder;
            $order->discount = $total_discount;
            $order->vat = ($totalorder - $total_discount) * 13 / 100;

            $order->save();

            $oi->status = "Done";

            $oi->save();

            Cart::with('productSize', 'dealer')->where(['user_id' => $request->user_id, 'dealer_code' => $request->dealer_code])->delete();

            // $carts = Cart::with('productSize', 'dealer')->where('created_by', Auth::user()->id)->get();



            // $dealerMobileCombo = [];

            // foreach ($carts as $item) {
            //     // we can have multiple dealer/mobile user at same cart details
            //     // so a combination of dealer and mobile user is 1 order
            //     $dealerAndMobileUser = $item->user_id . $item->dealer_code;
            //     if (in_array($dealerAndMobileUser, array_keys($dealerMobileCombo))) {
            //         $orderId = $dealerMobileCombo[$dealerAndMobileUser]['order_id'];
            //     } else {
            //         $order = new Order();
            //         $order->user_id = $item->user_id;
            //         $order->areacode = $item->dealer->area_code;
            //         $order->dealer_code = $item->dealer->code;
            //         $order->date = date('Y-m-d');
            //         $order->status = 'pending';
            //         $order->save();

            //         $arr = [
            //             'order_id' => $order->id,
            //             'total' => 0
            //         ];
            //         $orderId = $order->id;

            //         $dealerMobileCombo[$dealerAndMobileUser] = $arr;
            //     }

            //     $total_price = 0;
            //     $total_price = $item->quantity * $item->productSize->price;
            //     $array[] = array(
            //         'order_id' => $orderId,
            //         'items_id' => $item->productSize->id,
            //         'product_rate' => $item->productSize->price,
            //         'quantity' => $item->quantity,
            //         'total_price' => $total_price,
            //         'height' => $item->custom_length,
            //         'width' => $item->custom_width,

            //     );

            //     $dealerMobileCombo[$dealerAndMobileUser]['total'] += $total_price;
            // }

            // Orderitem::insert($array);

            // // Total price update for the orders
            // foreach ($dealerMobileCombo as $value) {
            //     Order::where('id', $value['order_id'])->update([
            //         'total_price' => $value['total']
            //     ]);
            // }


            // // Delete the records from cart table
            // Cart::where('created_by', Auth::user()->id)->delete();

            DB::commit();
            return back()->with('Status', 'Order has been created.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('Status', 'Something went wrong. Please try again later.');
        }
    }

    public function orderRequest()
    {
        $data['mobileOrders'] = OrderImage::with('mobileuser')->where('status', 'pending')->get();
        $data['dealers'] = Dealer::all();
        $data['users'] = Mobileuser::all();
        return view('dashboards.carts.orderrequestlist', $data);
    }


    public function addOrder($id)
    {

        $data['products'] = ProductSize::with('size', 'product.category', 'subproduct')->where('status', 1)->orderBy('id', 'desc')->get();
        $data['category'] = Category::where('status','Active')->get();
        $mobileOrder = OrderImage::with('mobileuser')->findOrFail($id);
        $data['mobileOrder'] = $mobileOrder;
        // dd($mobileOrder->dealer_code);
        $data['carts'] = Cart::with('productSize.product.category', 'productSize.subproduct', 'mobileuser', 'dealer')->where('dealer_code', $mobileOrder->dealer_code)->where('user_id', $mobileOrder->mobileuser_id)->get();
        return view('dashboards.carts.neworder', $data);
    }

    public function getProduct(Request $request)
    {

        if ($request->category) {
            $products = Product::where('category_id', $request->category)->get();
        } else {
            $products = Product::get();
        }
        return response()->json(['status' => 'success', 'dealers' => $products]);
    }

    public function getSubProduct(Request $request)
    {

        if ($request->product) {
            $pid = $request->product;
            $subproducts = Subproduct::whereHas('product_size', function ($query)use($pid) {
                $query->where('product_id',$pid);
            })->get();

        } else {
            $subproducts = Subproduct::all();
        }
        return response()->json(['status' => 'success', 'dealers' => $subproducts]);
    }

    public function getproductsize(Request $request)
    {

        if ($request->subproduct) {
            //
             $sizes = ProductSize::with('size')->where(['subproduct_id'=>$request->subproduct,'product_id'=>$request->product])->where('status', 1)->orderBy('id', 'desc')->get();

            // $sizes = ProductSize::with('size')->where('subproduct_id', $request->subproduct)->where('status', 1)->get();

        } else {
            // $sizes = Size::all();
        }
        return response()->json(['status' => 'success', 'dealers' => $sizes]);
    }



    // Update qty field in cart
    public function update(Request $request, $id)
    {
        try {
            Cart::where('id', $id)->update([
                'quantity' => $request->quantity,
                'custom_length' => $request->length,
                'custom_width' => $request->width
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Quantity updated Sucessfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to update quantity.',
            ], 500);
        }
    }


    // Delete the  records from order list
    public function destroy($id)
    {
        try {
            $cart = Cart::findorFail($id);
            $user = $cart->user_id;
            $dealer = $cart->dealer_code;
            $cart->delete();
            $carts = Cart::with('productSize.product.category', 'productSize.subproduct', 'productSize.size', 'mobileuser', 'dealer',)
                ->where(['user_id' => $user, 'dealer_code' => $dealer])
                ->orderBy('id', 'asc')
                ->get();
            $data = [];

            foreach ($carts as $cart) {
                $total =  number_format(($cart->quantity) * ($cart->productSize->price), 2);
                $cart->productSize->size->size == 'CUSTOM' ? $isCustom = true : $isCustom = false;
                $productName = $cart->productSize->product->category->name . '-' . $cart->productSize->product->name . '-' . $cart->productSize->subproduct->subproduct;
                $arr = [
                    'dealer' => $cart->dealer_code,
                    'mobUserInfo' => $cart->mobileuser->name,
                    'productInfo' => $productName . ($isCustom ? ' CUSTOM' : ''),
                    'id' => $cart->id,
                    'length' => $cart->custom_length,
                    'width' => $cart->custom_width,
                    'price' => $cart->productSize->price,
                    'isCustom' => ($isCustom ? 'true' : 'false'),
                    'qty' => $cart->quantity,
                    'totalPrice' => $total
                ];
                array_push($data, $arr);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order delete successfully.',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                // 'message' => 'Unable to delete the order.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Create Order request

    public function addOrderRequest(Request $request)
    {
        $this->validate($request, [
            'user' => 'required|exists:mobileusers,id',
            'dealer' => 'required|exists:dealers,code',
            'images' => 'nullable|array',
            'images.*' => 'required|mimes:png,jpg,jpeg'
        ]);

        $filename = '';
        $f_filename = '';

        if ($request->images) {

            foreach ($request->images as $image) {
                $content = File::get($image);

                $filename = time() . "_" . $image->getClientOriginalName();

                $f_filename = $f_filename . $filename . " :: ";

                Storage::disk('public')->put("/orderimage/$filename", $content);
            }
        }

        $orderimg = new OrderImage();

        $orderimg->mobileuser_id = $request->user;
        $orderimg->dealer_code = $request->dealer;
        $orderimg->status = 'pending';
        $orderimg->image = $f_filename;

        $orderimg->save();


        return back()->with('Status', 'Order Request hasb been added');
    }
}

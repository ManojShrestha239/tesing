<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Traits\FCM;

class FinalProductController extends Controller
{
    use FCM;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['products'] = ProductSize::with(['size', 'product.category'])->orderBy('id', 'DESC')->get();
        return view('dashboards.final-products.final', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['product'] = Product::with('category')
            ->whereHas('category', function ($query) {
                $query->where('categories.status', '=', 'Active');
            })
            ->where('status', 'Active')
            ->get();
        $data['size'] = Size::where('status', 'Active')->get();

        return view('dashboards.final-products.final_create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_name' => 'required|integer',
            'product_size' => 'required|integer',
            'product_price' => 'required|numeric',
        ]);
        $products = ProductSize::where('product_id', $request['product_name'])->where('size_id', $request['product_size'])->get();
        if (count($products) > 0) {
            return redirect('/final-product')->with('Status', 'This product already has this variant');
        } else {
            $product = new  ProductSize();
            $product->product_id =  $request['product_name'];
            $product->size_id = $request['product_size'];
            $product->price = $request['product_price'];
            $product->status = 1;
            $product->save();

            $title = "{$product->load('product')->product->name} has been Added";
            $body  = "{$product->load('product')->product->name} have been added to the company.";
            $topic = strtolower(config('app.env')) === 'production' ? config('app.prod-topic') : config('app.dev-topic');

            $data = [
                'route_channel' => 'product',
            ];

            $this->runFirebaseNotification($fcmid = null, $topic, $title, $body, $data);


            return redirect('/final-product')->with('Status', 'Complete Product Details Added');
        }
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
        // $data['product'] = Product::get();
        $data['size'] = Size::get();
        $data['productinfo'] = ProductSize::findorFail($id);

        return view('dashboards.final-products.final_edit', $data);
        // return view('dashboards.final-products.final_edit', ['product' => $productinfo, 'size' => $size, 'productinfo' => $productinfo]);

    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'variant' => 'required|exists:sizes,id',
            'price' => 'required|numeric',
        ]);

        $productinfo = ProductSize::findorFail($id);

        $exists = ProductSize::where('product_id', $productinfo->product_id)
            ->where('size_id', $request->variant)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['variant' => 'This product already has this variant.']);
        }

        $productinfo->size_id = $request->input('variant');
        $productinfo->price = $request->input('price');
        $productinfo->save();


        $title = "{$productinfo->product->name} Product has been Edited";
        $body  = "{$productinfo->product->name} have been updated.";
        $topic = strtolower(config('app.env')) === 'production' ? config('app.prod-topic') : config('app.dev-topic');

        $data = [
            'route_channel' => 'product',
        ];

        $this->runFirebaseNotification($fcmid = null, $topic, $title, $body, $data);

        return redirect('/final-product')->with('Status', 'Product Details Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = ProductSize::findorFail($id);
        if ($product->product->category['status'] == 'Active') {
            if ($product->status == 1) {
                $product->status = 0;
                Session::flash('Status', 'product Disabled Successfully.');
            } else {
                $product->status = 1;
                Session::flash('Status', 'product Enabled Successfully.');
            }
            $product->save();

            return back();
        } else {
            Session::flash('Status', "Product Cannot be update cause it's category is Disabled.");
            return back();
        }
    }
}

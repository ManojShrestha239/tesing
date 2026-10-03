<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Product;
use App\Models\ProductDealer;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;

class ProductDealerController extends Controller
{

    use  Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index()
    {

        $productDealers = ProductDealer::with(['dealer', 'size', 'product'])->get();
        return view('dashboards.product-dealer.list', compact('productDealers'));
    }


    public function create()
    {
        $stockProductIds = ProductStock::pluck('product_id')->unique();

        $products = Product::with('size', 'productmanuf')
            ->whereIn('id', $stockProductIds)
            ->get();

        $dealers = Dealer::formaindealer()->get();
        $productStocks = ProductStock::with('size')->get();

        return view('dashboards.product-dealer.create', compact('products', 'dealers', 'productStocks'));
    }

    public function getSize($product_id)
    {
        $size = \App\Models\ProductSize::with('size')
            ->where('product_id', $product_id)->get();


        if ($size && $size->size) {
            return response()->json([
                'size_name' => $size->size->size,
                'variant_id' => $size->size_id  // <-- use size_id here, not id
            ]);
        }

        return response()->json(['message' => 'Size not found'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,code',
            'date' => 'required',
            'invoice_num' => 'required|string',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.size_id' => 'required|exists:sizes,id',
            'products.*.assign_qty' => 'required|integer|min:1',
        ]);

        $errors = [];
        $productsToProcess = [];

        $dates = $this->custom_BS_to_AD($request->date);


        // Validate inventory for each product
        foreach ($request->products as $index => $product) {
            $availableQty = ProductStock::where('product_id', $product['product_id'])
                ->where('size_id', $product['size_id'])
                ->value('available');

            if ($availableQty === null) {
                $errors["products.$index.assign_qty"] = 'Product/size combination not found in inventory';
            } elseif ($product['assign_qty'] > $availableQty) {
                $errors["products.$index.assign_qty"] = 'Not enough inventory available (Available: ' . $availableQty . ')';
            } else {
                $productsToProcess[] = $product;
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Process each valid product
        foreach ($productsToProcess as $product) {
            // Create dispatch record
            ProductDealer::create([
                'dealer_id' => $request->dealer_id,
                'product_id' => $product['product_id'],
                'size_id' => $product['size_id'],
                'assign_qty' => $product['assign_qty'],
                'date' =>  $dates,
                'invoice_num' => $request->invoice_num,

            ]);

            // Update inventory
            $productStock = ProductStock::where('product_id', $product['product_id'])
                ->where('size_id', $product['size_id'])
                ->first();

            if ($productStock) {
                $productStock->assigned += $product['assign_qty'];
                $productStock->available = $productStock->total_qty - $productStock->assigned;
                $productStock->save();
            }
        }

        return redirect('/product-dealer')
            ->with('Status', count($productsToProcess) . ' product(s) assigned to dealer successfully!');
    }
}

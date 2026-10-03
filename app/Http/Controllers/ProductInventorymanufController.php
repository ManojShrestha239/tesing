<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductManufInventory;
use App\Models\ProductSize;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;



class ProductInventorymanufController extends Controller
{

    use  Nepali_Calendar, CustomNepaliCalendarConverter;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productStock = ProductStock::with(['product.size', 'size'])->get();

        $productIds = $productStock->pluck('product_id')->unique();
        $sizeIds = $productStock->pluck('size_id')->unique();

        $productmanufs = ProductManufInventory::with('product')
            ->whereIn('product_id', $productIds)
            ->whereIn('size_id', $sizeIds)
            ->get();

        return view('dashboards.productinventory.list', compact('productmanufs', 'productStock'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('size')->get();

        $today = date('Y-m-d');

        return view('dashboards.productinventory.productmanufac', compact('products'));
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

        // dd($request->all());
        $request->validate([
            'batch_number' => 'required|integer',
            'date' => 'required',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.size_id' => 'required|integer|exists:sizes,id',
            'products.*.total_qty' => 'required|numeric|min:1',
        ]);

        $errors = [];

        // Validate each product combination
        foreach ($request->products as $index => $product) {
            $variantExists = ProductSize::where('size_id', $product['size_id'])
                ->where('product_id', $product['product_id'])
                ->exists();

            if (!$variantExists) {
                $errors["products.$index.size_id"] = 'Selected size does not belong to the selected product.';
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $dates = $this->custom_BS_to_AD($request->date);


        // Process each product
        foreach ($request->products as $product) {
            // Create manufacturing record
            $manufacture = ProductManufInventory::create([
                'batch_number' => $request->batch_number,
                'product_id' => $product['product_id'],
                'size_id' => $product['size_id'],
                'total_qty' => $product['total_qty'],
                'date' => $dates,
                'created_at' => $request->date,
                'updated_at' => now(),
            ]);



            // Update stock
            $productStock = ProductStock::firstOrNew([
                'product_id' => $product['product_id'],
                'size_id' => $product['size_id'],
            ]);

            $productStock->total_qty += $product['total_qty'];
            $productStock->assigned = $productStock->assigned ?? 0;
            $productStock->available = $productStock->total_qty - $productStock->assigned;
            $productStock->save();
        }

        return redirect('/productmanufac')
            ->with('success', count($request->products) . ' product(s) added to inventory successfully.');
    }




    public function show($id)
    {
        $productStock = ProductStock::with(['product', 'size'])->findOrFail($id);

        $productmanufs = ProductManufInventory::with('product')
            ->where('product_id', $productStock->product_id)
            ->where('size_id', $productStock->size_id)
            ->get();

        $totalQty = $productmanufs->sum('total_qty');

        return view('dashboards.productinventory.details', compact('productStock', 'productmanufs', 'totalQty'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

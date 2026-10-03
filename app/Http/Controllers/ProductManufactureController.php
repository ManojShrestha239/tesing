<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductManufacture;
use App\Models\RawInventory;
use App\Models\RawMaterial;
use App\Models\RawProduct;
use App\Models\RawStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

class ProductManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productManufacture = ProductManufacture::with(['rawInventories.rawmaterial.rawSize', 'product'])
            ->orderBy('id', 'asc')
            ->get();

        return view('dashboards.productmanufactures.index', compact('productManufacture'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $products = Product::all();
        $rawMaterialsForProduct = [];

        // session()->forget('Status');


        if ($request->product_id) {
            $rawProduct = RawProduct::where('product_id', $request->product_id)->first();

            // if ($rawProduct && $rawProduct->rawmaterial_id) {
            //     $ids = explode('::', $rawProduct->rawmaterial_id);
            //     $rawMaterialsForProduct = RawMaterial::whereIn('id', $ids)->get();
            // }
            if ($rawProduct && $rawProduct->rawmaterial_id) {
                $ids = explode('::', $rawProduct->rawmaterial_id);
                $rawMaterialsForProduct = RawMaterial::whereIn('id', $ids)->get();

                foreach ($rawMaterialsForProduct as $raw) {
                    // $raw->available_qty = $raw->rawInventories()->orderByDesc('id')->first()?->available ?? 0;
                    $raw->available_qty = $raw->rawStock?->available ?? 0;
                }
            } else {
                return back()->with('Status', 'Raw Materials not assigned for the selected product. <a href="/raw-product/create" >Assign Now </a>');
            }
        }

        return view('dashboards.productmanufactures.create', compact('products', 'rawMaterialsForProduct'));
    }


    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'no_of_products' => 'required|numeric|min:1',
            'rawmaterial_ids' => 'required|array',
            'quantities' => 'required|array',
            'date' => 'required|date|before_or_equal:today',

        ]);

        $rawInventorys = [];

        DB::beginTransaction();
        try {
            // 🔁 First loop: validate all raw materials before inserting
            foreach ($request->rawmaterial_ids as $index => $rawMaterialId) {
                $qtyUsed = $request->quantities[$index];
                $rawsizeId = $request->rawsize_ids[$index];

                $latestInventory = RawInventory::where('r_id', $rawMaterialId)->latest()->first();
                $available = $latestInventory ? $latestInventory->available : 0;

                $rawStock = RawStock::where('r_id', $rawMaterialId)->first();

                if (!$rawStock) {
                    DB::rollBack();
                    return back()->withErrors([
                        'quantities.' . $index => "Raw stock not found for raw material ID: $rawMaterialId.",
                    ])->withInput();
                }

                if ($qtyUsed > $available || $qtyUsed > $rawStock->available) {
                    DB::rollBack();
                    return back()->withErrors([
                        'quantities.' . $index => "Used quantity ($qtyUsed) exceeds available ($available) for raw material ID: $rawMaterialId.",
                    ])->withInput();
                }
            }

            // ✅ All checks passed — now insert ProductManufacture
            $productManufacture = ProductManufacture::create([
                'p_id' => $request->product_id,
                'total_qty' => $request->no_of_products,
                'withdraw_qty' => 0,
                'available_qty' => $request->no_of_products,
                'date' => $request->date,
                'status' => 'yes',
                'flag' => 1,
            ]);

            // 🔁 Second loop: update and insert inventory
            foreach ($request->rawmaterial_ids as $index => $rawMaterialId) {
                $qtyUsed = $request->quantities[$index];
                $rawsizeId = $request->rawsize_ids[$index];

                $latestInventory = RawInventory::where('r_id', $rawMaterialId)->latest()->first();

                $updatedAvailable = $latestInventory->available - $qtyUsed;

                $rawInventorys[] = [
                    'pm_id' => $productManufacture->id,
                    'r_id' => $rawMaterialId,
                    'total_qty' => $latestInventory->available,
                    'rawsize_id' => $rawsizeId,
                    'withheld' => $qtyUsed,
                    'available' => $updatedAvailable,
                    'status' => 'yes',
                    'flag' => 1,
                    'date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $rawStock = RawStock::where('r_id', $rawMaterialId)->first();
                $rawStock->update([
                    'withdraw' => $rawStock->withdraw + $qtyUsed,
                    'available' => $rawStock->available - $qtyUsed,
                ]);
            }

            RawInventory::insert($rawInventorys);
            DB::commit();

            return back()->with('Status', 'Product and Raw Materials recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Server error: ' . $e->getMessage());
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

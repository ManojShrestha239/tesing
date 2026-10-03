<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductManufacture;
use App\Models\RawMaterial;
use App\Models\RawProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RawProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $rawproducts = RawProduct::with(['product', 'rawMaterial'])
            ->whereHas('rawMaterial')
            ->orderby('created_at', 'desc')->get();
        // return  $rawproducts[1]->first()->product;
        return view('dashboards.rawproducts.index', compact('rawproducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $rawMaterials = RawMaterial::all();

        return view('dashboards.rawproducts.create', compact('products', 'rawMaterials'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'raw_material_ids' => 'required|array',
            'raw_material_ids.*' => 'exists:raw_materials,id',
        ]);
        // dd($request->all());

        DB::beginTransaction();

        try {
            $ids = collect($request->raw_material_ids)->implode('::');

            $raw = RawProduct::create([
                'product_id' => $request->product_id,
                'rawmaterial_id' => $ids, // this will store like "1 :: 2 :: 3"
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); // FIXED
            Session::flash('Status', 'Cannot assign Rawmaterials please try again later');
            return back();
        }

        return redirect('/raw-product')->with('Status', 'New Raw Materials Assigned With Products.');
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

    public function edit($id)
    {
        $rawProduct = RawProduct::with('rawMaterial', 'product')->findOrFail($id); // Eager load rawMaterials
        $products = Product::all();
        $rawMaterials = RawMaterial::all();

        $rawMaterialIds = explode('::', $rawProduct->rawmaterial_id);  // Convert to array


        return view('dashboards.rawproducts.edit', compact('rawProduct', 'products', 'rawMaterials', 'rawMaterialIds'));
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'raw_material_ids' => 'required|array',
            'raw_material_ids.*' => 'exists:raw_materials,id',
        ]);

        DB::beginTransaction();

        try {
            // Find the raw product by ID
            $rawProduct = RawProduct::findOrFail($id);
            $oldProductId = $rawProduct->product_id;


            // Update the product_id and rawmaterial_id
            $ids = collect($request->raw_material_ids)->implode('::'); // Concatenate raw material IDs

            $rawProduct->product_id = $request->product_id;
            $rawProduct->rawmaterial_id = $ids;
            $rawProduct->save();  // Save the changes to the database
            ProductManufacture::where('p_id', $oldProductId)->update(['p_id' => $request->product_id]);




            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('Status', 'Cannot assign Raw Materials, please try again later');
            return back();
        }

        return redirect('/raw-product')->with('Status', 'Raw Materials updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

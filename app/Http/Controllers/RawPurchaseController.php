<?php

namespace App\Http\Controllers;

use App\Models\RawInventory;
use App\Models\RawMaterial;
use App\Models\RawSize;
use App\Models\RawStock;
use App\Models\Size;
use Illuminate\Http\Request;

class RawPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rawMaterials = RawMaterial::all();
        $rawSizes = RawSize::all();

        foreach ($rawMaterials as $rawMaterial) {
            $latestInventory = RawInventory::where('r_id', $rawMaterial->id)
                ->latest()
                ->first();

            // Add the available quantity to the raw material object if inventory exists
            $rawMaterial->latest_available = $latestInventory ? $latestInventory->available : 0;
        }
        return view('dashboards.rawpurchase.create', compact('rawMaterials', 'rawSizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'r_id' => 'required|exists:raw_materials,id',
            'total_qty' => 'required|numeric|min:0',
            'date' => 'required|date|before_or_equal:today',
            'batch_number' => 'required|string|max:255',
            // 'rawsize_id' => 'required|exists:raw_sizes,id',


        ]);
        $rawMaterial = Rawmaterial::findOrFail($request->r_id);
        $rawSizeId = $rawMaterial->rawSize ? $rawMaterial->rawSize->id : null;
        if (!$rawSizeId) {
            return redirect()->back()->withErrors(['rawsize_id' => 'The selected raw material does not have a valid raw size.']);
        }
        $existingInventory = RawInventory::where('r_id', $request->r_id)->latest()->first();

        $newAvailable = $existingInventory ? $existingInventory->available + $request->total_qty : $request->total_qty;

        RawInventory::create([
            'pm_id' => null,
            'r_id' => $request->r_id,
            'batch_number' => $request->batch_number,
            'rawsize_id' => $rawSizeId,
            'total_qty' => $request->total_qty,
            'withheld' => 0,
            'available' => $newAvailable,
            'status' => 'yes',
            'flag' => 1,
            'date' => $request->date,
        ]);


        $rawStock = RawStock::where('r_id', $request->r_id)
            ->where('rawsize_id', $rawSizeId)
            // ->where('batch_number', $request->batch_number)
            ->first();

        if ($rawStock) {
            // Update existing RawStock
            $rawStock->total_qty += $request->total_qty;
            $rawStock->available += $request->total_qty;
            $rawStock->save();
        } else {
            RawStock::create([
                'r_id' => $request->r_id,
                'total_qty' => $request->total_qty,
                'rawsize_id' => $rawSizeId,                 // 'batch_number' => $request->batch_number,

                'withdraw' => 0, // Default withdrawal is 0
                'available' => $request->total_qty,
            ]);
        }
        // dd($request->all());

        return redirect()->back()->with('Status', 'Raw Inventory Added Successfully!');
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

<?php

namespace App\Http\Controllers;

use App\Models\ProductDealer;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductInventoryController extends Controller
{
    public function index()
    {
        $stocks = ProductStock::with(['product', 'size'])->get();

        $inventory = $stocks->map(function ($stock) {
            $dealers = ProductDealer::where('product_id', $stock->product_id)
                ->where('size_id', $stock->size_id)
                ->with('dealer')
                ->get();

            $dealerInfo = $dealers->map(function ($d) {
                $dealerName = $d->dealer ? $d->dealer->name : $d->dealer_id;
                return $dealerName . ' (' . $d->assign_qty . ')';
            })->implode(', ');

            return [
                'product_name' => $stock->product->name ?? 'N/A',
                'size' => $stock->size->size ?? 'N/A',
                'total_qty' => $stock->total_qty,
                'assigned' => $stock->assigned ?? 0,
                'available' => $stock->available,
                'dealers' => $dealerInfo ?: 'N/A',
            ];
        });

        return view('dashboards.productinventory.inventory', compact('inventory'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboards.productinventory.productmanufac');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

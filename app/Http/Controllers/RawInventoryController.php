<?php

namespace App\Http\Controllers;

use App\Models\RawInventory;
use Illuminate\Http\Request;

class RawInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rawInventories = RawInventory::with(['rawmaterial', 'productManufactured'])
            ->whereHas('rawmaterial')
            ->orderBy('id', 'asc')
            ->get();

        return view('dashboards.rawinventory.index', compact('rawInventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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

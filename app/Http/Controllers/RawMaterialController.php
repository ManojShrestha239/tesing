<?php

namespace App\Http\Controllers;

use App\Models\RawInventory;
use App\Models\RawMaterial;
use App\Models\RawMaterials;
use App\Models\RawSize;
use App\Models\Size;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $rawMaterials = RawMaterial::with('rawSize', 'rawStock')->get();
        return view('dashboards.rawmaterials.index', compact('rawMaterials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['sizes'] = RawSize::all();
        return view('dashboards.rawmaterials.create', $data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:raw_materials',
            'rawsize_id' => 'required|exists:raw_sizes,id',


        ]);
        $raw = new RawMaterial;


        $raw->name = $request['name'];
        $raw->rawsize_id = $request->input('rawsize_id');


        $raw->save();

        return redirect('/raw')->with('Status', 'New Raw Materials Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rawMaterial = RawMaterial::with('rawInventories')->findOrFail($id);

        // Get the inventories
        $rawInventories = $rawMaterial->rawInventories;

        return view('dashboards.rawmaterials.details', compact('rawInventories', 'rawMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rawmaterial = RawMaterial::findOrFail($id);

        return view('dashboards.rawmaterials.edit', compact('rawmaterial'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rawmaterial = RawMaterial::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
        ]);

        $rawmaterial->update($request->all());

        return redirect('/raw')->with('Status', 'Rawmaterials Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rawmaterial = RawMaterial::findOrFail($id);
        $rawmaterial->delete();

        return redirect('/raw')->with('success', 'Raw Material deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Facades\Session;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['sizes'] = Size::all();
        return view('dashboards.size.list', $data);
        // ->with('sizes', $sizes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.size.create');
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
            'size' => 'required|unique:sizes,size',
        ]);

        Size::create([
            'size' => $request['size'],
        ]);

        return redirect('/size')->with('Status', 'New Size Added.');
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
        $data['size'] = Size::withCount('product')->findorFail($id);
        return view('dashboards.size.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'size' => 'required|unique:sizes,size,' . $id,
        ]);

        $size = Size::findorFail($id);

        $size->size = $request->input('size');

        $size->save();
        return redirect('/size')->with('Status', 'Size Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $size = Size::findorFail($id);

        if ($size->status == "Active") {
            $size->status = "Inactive";
            Session::flash('Status', 'size Disabled Successfully.');
        } else {
            $size->status = "Active";
            Session::flash('Status', 'size Enabled Successfully.');
        }
        $size->save();

        return back();
    }
}

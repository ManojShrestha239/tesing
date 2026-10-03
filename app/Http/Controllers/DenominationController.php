<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Denomination;

class DenominationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['datas'] = Denomination::orderBy('updated_at','DESC')->paginate(15);

        return view('dashboards.denominations.show',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.denominations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'denomination'=>'required|numeric|gt:0|unique:denominations'
        ]);

        Denomination::create([
            'denomination'=>$request->denomination,
        ]);
        return back()->with('Status','New Denomination added');
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
        $data['denomination'] = Denomination::findorFail($id);
        
        return view('dashboards.denominations.edit',$data);
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
        $this->validate($request,[
            'denomination'=>'required|numeric|gt:0|unique:denominations'
        ]);

        $data = Denomination::findorFail($id);

        $data->denomination = $request->denomination;

        $data->save();

        return redirect('/denomination')->with('Status','Denomination Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

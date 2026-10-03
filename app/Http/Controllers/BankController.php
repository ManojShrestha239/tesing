<?php

namespace App\Http\Controllers;

use App\Imports\Bankimport;
use Illuminate\Http\Request;
use App\Models\Bank;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['banks'] = Bank::get();
        return view('dashboards.banks.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.banks.create');
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
            'name' => 'required|max:255|unique:banks',
        ]);

        Bank::create([
            'name' => $request['name'],
        ]);

        return redirect('/bank')->with('Status','New Bank Added.');
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
        $data['bank'] = Bank::findorFail($id);
        return view('dashboards.banks.edit',$data);
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
            'name' => 'required|max:255|unique:banks,id',
        ]);

        $bank = Bank::findorFail($id);

        $bank->name = request('name');

        $bank->save();
        return redirect('/bank')->with('Status','Bank Details Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Bank::findorFail($id)->delete();
        // return redirect('/bank')->with('Status','Bank Deleted.');
    }

    public function bankexcel(Request $request){
        $this->validate($request,[
            'file'=>'required|mimes:xlsx,xls|max:2048'
        ]);

        Excel::import(new Bankimport(),$request->file);

        Session::flash('Success','Bank Uploaded Successfully');

        return redirect('/bank');
    }
}

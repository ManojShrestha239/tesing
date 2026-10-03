<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purpose;

class PurposeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['datas'] =  Purpose::orderBy('updated_at', 'DESC')->paginate(15);

        return view('dashboards.purposes.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboards.purposes.create');
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
            'purpose' => 'required|unique:purposes'
        ]);

        Purpose::create([
            'purpose' => $request->purpose,
        ]);
        return back()->with('Status', 'New purpose added');
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
        $data['purpose'] = Purpose::findorFail($id);
        return view('dashboards.purposes.edit',$data);
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
            'purpose' => 'required'
        ]);
        $request->purpose;


        $data = Purpose::findorFail($id);

        if ($data->purpose != $request->purpose) {
            if (count(Purpose::where('purpose', $request->purpose)->get()) > 0) {
                return redirect('/purpose')->with('Status', 'Duplicate Entry for purpose');
            } else {

                $data->purpose = $request->purpose;

                $data->save();

                return redirect('/purpose')->with('Status', 'purpose Updated');
            }
        }
        return redirect('/purpose')->with('Status', 'Duplicate Entry for purpose');
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

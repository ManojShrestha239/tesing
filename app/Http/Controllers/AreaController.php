<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Subarea;
use App\Models\Assignarea;
use Illuminate\Support\Facades\Session;

class AreaController extends Controller
{
    public function index()
    {
        $data['subareas'] = Subarea::all();
        $data['areas'] = Area::all();
        return view('dashboards.areas.index', $data);
    }

    public function create()
    {
        $data['subareas'] = Subarea::all();
        return view('dashboards.areas.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'areacode' => 'required|unique:areas|string',
            'area' => 'required|unique:areas',
            'subarea' => 'required|array',
            'subarea.*' => 'nullable|string:255'
        ]);

        $area = new Area;
        $area->areacode = $request->areacode;
        $area->area = $request->area;

        $area->save();

        if ($request->subarea) {
            if (count($request->subarea) == 0) {
                $this->validate($request, [
                    'subarea.*' => 'nullable|integer'
                ]);
            }
            foreach ($request->subarea as $sub) {
                $data[] = array(
                    'areacode' => $area->areacode,
                    'subarea_id' => $sub
                );
            }
            Assignarea::insert($data);
        }

        return redirect('/area')->with('Status', 'New Area Added.');
    }

    public function edit($areacode)
    {
        $data['area'] = Area::findorFail($areacode);
        $data['subareas'] = Subarea::all();
        $data['assigns'] = Assignarea::where('areacode', $areacode)->get();
        return view('dashboards.areas.edit', $data);
    }

    public function update(Request $request, $areacode)
    {
        $this->validate($request, [
            'area' => 'required',
            'subarea' => 'required|array',
            'subarea.*' => 'exists:subareas,id'
        ]);
        // return $request->subarea;
        $area = Area::findorFail($areacode);

        $area->area = request('area');

        $area->save();
        Assignarea::where(['areacode' => $areacode])->delete();
        foreach ($request->subarea as $subarea) {
            $assignedarea = new Assignarea();

            $assignedarea->subarea_id = $subarea;
            $assignedarea->areacode = $areacode;

            $assignedarea->save();
        }
        return redirect('/area')->with('Status', 'Area Updated.');
    }

    public function destroy($id)
    {
        $user = Area::findorFail($id);
        if ($user->status == "Published") {
            $user->status = "Deleted";
            Session::flash('Status', 'USer Disabled Successfully');
        } else {
            $user->status = "Published";
            Session::flash('Status', 'USer Enabled Successfully');
        }
        $user->save();
        return back();
    }

    public function getAreaByDealer(Request $request)
    {
        $dealerCode = $request->input('dealer_code');
        $area = Area::whereHas('dealer', function ($query) use ($dealerCode) {
            $query->where('code', $dealerCode);
        })->get();

        return response()->json($area);
    }
}

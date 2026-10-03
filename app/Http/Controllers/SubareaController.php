<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subarea;
use App\Models\Assignarea;
use App\Models\Mobileuser;
use Illuminate\Support\Facades\DB;

class SubareaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'subarea' => 'required',
            'subarea.*' => 'required|string|max:255'
        ]);
        foreach ($request->subarea as $sub) {
            Subarea::create([
                'subarea' => $sub
            ]);
        }
        return back()->with('Status', 'Subarea Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assigns = Assignarea::where('areacode', $id)->get();
        $id = [];
        foreach ($assigns as $assign) {
            $id[] = $assign->subarea_id;
        }
        $subarea = Subarea::whereIn('id', $id)->get();

        return view('dashboards.subareas.list', ['subareas' => $subarea]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subarea = Subarea::findorFail($id);
        return view('dashboards.subareas.edit', ['subarea' => $subarea]);
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
            'subarea' => 'required|string|max:64|unique:subareas,subarea'
        ]);

        $subarea = Subarea::findOrFail($id);

        $old = $subarea->subarea;
        $new = $request->subarea;

        $subarea->subarea = $new;
        $subarea->save();

        Mobileuser::where('sub_areas', 'LIKE', "%{$old}%")
            ->update([
                'sub_areas' => DB::raw("REPLACE(`sub_areas`, " . DB::getPdo()->quote($old) . ", " . DB::getPdo()->quote($new) . ")")
            ]);

        return back()->with('Status', 'Subarea Updated Successfully');
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

    public function getsubarea(Request $request)
    {
        // If specific area codes are provided, use them to return subareas.
        if ($request->has('data') && !empty($request->input('data'))) {
            $areaCodes = $request->input('data', []);

            if (!is_array($areaCodes)) {
                $areaCodes = [$areaCodes];
            }

            $subareaIds = Assignarea::whereIn('areacode', $areaCodes)
                ->pluck('subarea_id');

            $subareas = Subarea::whereIn('id', $subareaIds)->get();
        } elseif ($request->filled('user_id')) {
            // Fallback: if no area codes sent, return subareas assigned to the user
            $user = Mobileuser::find($request->user_id);

            if (!$user || empty($user->sub_areas)) {
                return response()->json([]);
            }

            $subareas = Subarea::whereIn(
                'subarea',
                explode(' :: ', $user->sub_areas)
            )->get();
        } else {
            // No filters provided — return empty set to avoid leaking data
            return response()->json([]);
        }

        return response()->json($subareas);
    }


    function subarea(Request $request)
    {
        $assigns = Assignarea::where('areacode', $request->data)->get();
        $id = [];
        foreach ($assigns as $assign) {
            $id[] = $assign->subarea_id;
        }
        $subarea = Subarea::whereIn('id', $id)->get();

        return response()->json($subarea);
    }
}

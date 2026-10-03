<?php

namespace App\Http\Controllers;

use App\Exports\DealerExport;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\Area;
use Illuminate\Support\Facades\Session;
use App\Imports\DealerImport;
use App\Models\Assignarea;
use App\Models\Subarea;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class DealerController extends Controller
{
    public function index()
    {
        $data['dealers'] = Dealer::with('mainDealer')->orderBy('created_at', 'desc')->get();
        return view('dashboards.dealers.list', $data);
    }

    public function create()
    {
        $data['areas'] = Area::all();
        $data['mainDealers'] = Dealer::Formaindealer()->get();
        return view('dashboards.dealers.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => [
                'required',
                'unique:dealers',
                'regex:/^[A-Za-z0-9]+$/', // only letters and numbers
            ],
            'main_dealer' => 'nullable|exists:dealers,code',
            'name' => 'required|max:64',
            'address' => 'required|max:191',
            'email' => 'nullable|email|unique:dealers,email',
            'mobile' => 'required|digits_between:9,10|unique:dealers,mobile',
            'phone' => 'nullable|digits_between:9,10',
            'area_code' => 'required|exists:areas,areacode',
            'order_limit' => 'nullable|integer',
            'subarea' => 'required|exists:subareas,subarea'
        ]);

        if ($request->email == null) {
            $email = '';
        } else {
            $email = $request->email;
        }
        if ($request->phone == null) {
            $phone = '';
        } else {
            $phone = $request->phone;
        }

        Dealer::create([
            'code' => $request->code,
            'main_dealer_code' => $request->main_dealer,
            'name' => $request->name,
            'address' => $request->address,
            'email' => $email,
            'mobile' => $request->mobile,
            'phone' => $phone,
            'area_code' => $request->area_code,
            'order_limit' => $request->order_limit,
            'status' => 'not-approved',
            'subarea' => $request->subarea,
        ]);

        return redirect('/dealer')->with('Status', 'New Dealer Added.');
    }

    public function edit($code)
    {
        $dealer = Dealer::findorFail($code);
        $data['mainDealers'] = Dealer::Formaindealer()->get();
        $areas = Area::where('Status', 'Published')->get();
        $subarea = Assignarea::where('areacode', $dealer->area_code)->pluck('subarea_id');
        $sub = Subarea::whereIn('id', $subarea)->get();
        $data['subareas'] = $sub;
        $data['dealer'] = $dealer;
        $data['areas'] = $areas;
        return view('dashboards.dealers.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'main_dealer' => 'nullable|exists:dealers,code',
            'name' => 'required|max:64',
            'address' => 'required|max:191',
            'email' => 'nullable|email',
            'phone' => 'nullable|digits_between:9,10',
            'area' => 'required|exists:areas,areacode',
            'order_limit' => 'nullable|integer',
            'status' => 'required',
            'subarea' => 'required|exists:subareas,subarea'
        ]);
        if ($valid->fails()) {
            return back()->withErrors($valid);
        }

        if ($request->input('email') == null) {
            $email = '';
        } else {
            $email = $request->input('email');
        }
        if ($request->input('phone') == null) {
            $phone = '';
        } else {
            $phone = $request->input('phone');
        }


        $dealer = Dealer::findorFail($id);
        if ($dealer->mobile != $request->mobile)
            $this->validate($request, [
                'mobile' => 'required|digits_between:9,10|unique:dealers',
            ]);
        if ($dealer->email != $request->email)
            $this->validate($request, [
                'email' => 'required|unique:dealers',
            ]);
        $dealer->main_dealer_code = $request->main_dealer;
        $dealer->name = $request->name;
        $dealer->address = $request->address;
        $dealer->email = $email;
        $dealer->mobile = $request->mobile;
        $dealer->phone = $phone;
        $dealer->area_code = $request->area;
        $dealer->order_limit = $request->order_limit;
        $dealer->status = $request->status;
        $dealer->subarea = $request->subarea;

        $dealer->save();
        return redirect('/dealer')->with('Status', 'Dealer Details Updated');
    }

    public function destroy($code)
    {
        $dealer = Dealer::findorFail($code);
        if ($dealer->active_status == "Active") {
            $dealer->active_status = "Inactive";
            Session::flash('Status', 'Dealer Disabled Successfully');
        } else {
            $dealer->active_status = "Active";
            Session::flash('Status', 'Dealer Enabled Successfully');
        }
        $dealer->save();
        return back();
    }

    public function excelUpload(Request $request)
    {
        $request->validate(['excel_file' => 'required|file|mimes:xlsx']);

        try {
            $import = new DealerImport;
            Excel::import($import, $request->file('excel_file'));
            return back()->with('success', "Import completed. Inserted: {$import->getInsertedCount()}");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function dealermap()
    {
        $data['dealers'] = Area::where('status', 'Published')->get();

        return view('dashboards.dealers.map', $data);
    }

    public function mapdata()
    {
        $dealer = Dealer::where(['active_status' => 'Active', 'status' => 'approved'])->get();

        foreach ($dealer as $d) {
            $data[] = array(
                'info' => '<strong>' . $d->name . '</strong><br>' . $d->address . 'Outlet <br>' . $d->mobile,
                'lat' => $d->latitude,
                'long' => $d->longitude,
                'province' => $d->area_code,
                'district' => $d->subarea,
                //    'info'=>"<strong>".$d->name."</strong><br>\".$d->address ." Outlet<br>\" ,
            );
        }
        if (count($dealer) == 0) {
            $data = [];
        }
        return response()->json($data);
    }

    public function getdealer(Request $request)
    {
        $dealer = Dealer::whereIn('subarea', $request->data)->get();
        return response()->json($dealer);
    }

    public function dealerExport()
    {
        ob_start();
        ob_end_clean();
        return Excel::download(new DealerExport, 'Dealer_' . date('Y-m-d') . '_' . time() . '.xlsx');
    }

    public function dealerOrRetailer(Request $request)
    {
        $type = $request->input('type');

        if (!in_array($type, ['dealer', 'retailer'])) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $query = Dealer::select('code', 'name')
            ->where('active_status', 'Active');

        $query->when($type === 'dealer', fn($q) => $q->whereNull('main_dealer_code'))
            ->when($type === 'retailer', fn($q) => $q->whereNotNull('main_dealer_code'));

        return response()->json($query->get());
    }
}

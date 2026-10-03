<?php

namespace App\Http\Controllers;

use App\Models\AutoCheckout;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $distance = Setting::first();
        return view('dashboards.distance.distance', ['distance' => $distance]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'distance' => 'required|numeric|digits_between:2,5',
        ]);

        $dist = Setting::first();
        if ($dist) {
            $dist->distance = $request['distance'];
        } else {
            $dist = new Setting();

            $dist->distance = $request['distance'];
        }
        $dist->save();

        return redirect('/distance')->with('Status', 'New Distance limit Set');
    }

    public function checkoutIndex()
    {
        $data['checkoutTime'] = AutoCheckout::where('user_id', null)->value('checkout_time');
        return view('dashboards.distance.checkouttime', $data);
    }

    public function checkoutStore(Request $request)
    {
        $this->validate($request, [
            'checkoutTime' => 'required|date_format:H:i',
        ]);

        $checkout = AutoCheckout::where('user_id', null)->first();
        if ($checkout) {
            $checkout->checkout_time = $request['checkoutTime'];
        } else {
            $checkout = new AutoCheckout();
            $checkout->user_id = null;
            $checkout->checkout_time = $request['checkoutTime'];
        }
        $checkout->save();

        return redirect('/checkout-time')->with('Status', 'Checkout time updated successfully.');
    }
}

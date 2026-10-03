<?php

namespace App\Exports;

use App\Models\Area;
use App\Models\Bank;
use App\Models\Mobileuser;
use App\Models\Payment;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ZeroReport implements FromView
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    protected $from, $to, $month, $user;

    public function __construct($from, $to, $month, $user)
    {
        $this->from = $from;
        $this->to = $to;
        $this->month = $month;
        $this->user = $user;
    }

    public function view(): View
    {
        $user = $this->user;
        // dd($user);
        $dateElements = explode('-', $this->to);
        if ($this->from == $this->to) {
            $this->from = $dateElements[0] . '-' . $dateElements[1] . '-01';
        }

        $diff = strtotime($this->from) - strtotime($this->to);

        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        $totaldays = abs(round($diff / 86400));


        if ($this->from) {
            $from = $this->custom_BS_to_AD($this->from);
        }
        if ($this->to) {
            $to = $this->custom_BS_to_AD($this->to);
        }
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
        } else {
            $admin_area = Area::pluck('areacode');
        }

        $banks = Bank::get();

        if (isset($user)) {
            foreach ($banks as $bank) {
                $bank->chequeAmt = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where(['bank_id' => $bank->id, 'user_id' => $user])->sum('amount');
            }

            $oldCollection = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where(['payment_type' => 'cash (against)', 'user_id' => $user])->sum('amount');
            $other = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where(['payment_type' => 'other', 'user_id' => $user])->sum('amount');

            $bankDeposit = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'])->where(['status' => 'Deposited into Bank', 'user_id' => $user])->sum('amount');
            $sentOffice = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'])->where(['status' => 'Sent to Office', 'user_id' => $user])->sum('amount');
            $direct = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'direct')->where('user_id', $user)->sum('amount');

            $discount = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('user_id', $user)->sum('discount');
            $PDC = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'PDC')->where('user_id', $user)->sum('amount');
            $token = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'token')->where('user_id', $user)->sum('amount');

            $userName = Mobileuser::findorfail($user)->name;


            return view('dashboards.report.zeroReport', [
                'from' => $this->from,
                'to' => $this->to,
                'bankDeposit' => $bankDeposit,
                'PDC' => $PDC,
                'sentOffice' => $sentOffice,
                'token' => $token,
                'direct' => $direct,
                'discount' => $discount,
                'banks' => $banks,
                'other' => $other,
                'totaldays' => $totaldays,
                'month' => $this->month,
                'oldCollection' => $oldCollection,
                'userName' => $userName
            ]);
        } else {
            foreach ($banks as $bank) {
                $bank->chequeAmt = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where(['bank_id' => $bank->id])->sum('amount');
                // $bank->cashAmt = Payment::whereIn('areacode',$admin_area)->whereBetween('collection_date', [$from, $to])->where(['bank_id' => $bank->id])->sum('amount');
            }
            $oldCollection = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'cash (against)')->sum('amount');
            $other = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'other')->sum('amount');

            $bankDeposit = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'])->where('status', 'Deposited into Bank')->sum('amount');
            $sentOffice = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->whereIn('payment_type', ['cheque (bearer)', 'cheque (payee)', 'cheque', 'cash (account payee)'])->where('status', 'Sent to Office')->sum('amount');
            $direct = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'direct')->sum('amount');

            $discount = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->sum('discount');
            $PDC = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'PDC')->sum('amount');
            $token = Payment::whereIn('areacode', $admin_area)->whereBetween('collection_date', [$from, $to])->where('payment_type', 'token')->sum('amount');
        }
        // dd($from);

        return view('dashboards.report.zeroReport', [
            'from' => $this->from,
            'to' => $this->to,
            'bankDeposit' => $bankDeposit,
            'sentOffice' => $sentOffice,
            'token' => $token,
            'direct' => $direct,
            'discount' => $discount,
            'banks' => $banks,
            'other' => $other,
            'totaldays' => $totaldays,
            'month' => $this->month,
            'oldCollection' => $oldCollection,
        ]);
    }
}

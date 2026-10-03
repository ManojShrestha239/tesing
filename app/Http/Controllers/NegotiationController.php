<?php

namespace App\Http\Controllers;

use App\Models\Mobileuser;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    public function index()
    {
        $date = now()->toDateString();
        $today = $this->custom_AD_to_BS($date);

        $negotiationQuery = Session::with(['user.area', 'visitPurposes'])
            ->where('date', $date)
            ->where('status', 'Unlisted')
            ->select('visits.*', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('user_id');

        if (Auth::user()->hasRole('ASM')) {
            $adminAreaCodes = Auth::user()->area->pluck('areacode');

            $negotiationQuery->whereHas('user.areas', function ($query) use ($adminAreaCodes) {
                $query->whereIn('areas.areacode', $adminAreaCodes);
            });
        }

        $negotiations = $negotiationQuery->get()->map(function ($visit) use ($date) {
            $visit->purposes = $visit->visitPurposes
                ->where('date', $date)
                ->where('status', 'Unlisted')
                ->groupBy('purpose_id')
                ->map(function ($group) {
                    return (object) [
                        'purpose' => $group->first()->purposes->purpose,
                        'visit_count' => $group->count()
                    ];
                })
                ->values();

            return $visit;
        });

        return view('dashboards.negotiations.default', [
            'list' => $negotiations,
            'from' => $today,
            'to' => $today
        ]);
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'start-date' => ['required', 'date_format:Y-m-d'],
            'end-date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start-date']
        ]);

        if ($request['start-date'] > $request['end-date']) {
            return back()->withInput($request->input())->withErrors(['end-date' => 'end date must be after start date']);
        }

        $startDateBS = $validated['start-date'];
        $endDateBS = $validated['end-date'];

        $from = $this->custom_BS_to_AD($startDateBS);
        $to = $this->custom_BS_to_AD($endDateBS);

        $todayBS = $this->custom_AD_to_BS(now()->toDateString());

        if ($startDateBS > $todayBS) {
            return redirect()->route('negotiation.dashboard')
                ->with('error', 'Start date must be before or equal to today');
        }

        $negotiationQuery = Session::with(['user.area', 'visitPurposes'])
            ->where('status', 'Unlisted')
            ->whereBetween('date', [$from, $to])
            ->select('visits.*', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('user_id');

        if (Auth::user()->hasRole('ASM')) {
            $adminAreaCodes = Auth::user()->area->pluck('areacode');

            $negotiationQuery->whereHas('user.areas', function ($query) use ($adminAreaCodes) {
                $query->whereIn('areas.areacode', $adminAreaCodes);
            });
        }

        $negotiations = $negotiationQuery->get()->map(function ($visit) use ($from, $to) {
            $visit->purposes = $visit->visitPurposes
                ->where('status', 'Unlisted')
                ->whereBetween('date', [$from, $to])
                ->groupBy('purpose_id')
                ->map(function ($group) {
                    return (object) [
                        'purpose' => $group->first()->purposes->purpose,
                        'visit_count' => $group->count()
                    ];
                })
                ->values();

            return $visit;
        });

        return view('dashboards.negotiations.default', [
            'list' => $negotiations,
            'from' => $startDateBS,
            'to' => $endDateBS
        ]);
    }

    public function show($id, Request $request)
    {
        $request->validate([
            'type' => ['nullable', 'in:dealer,retailer'],
            'from' => ['nullable', 'required_with:to', 'date_format:Y-m-d'],
            'to' => ['nullable', 'required_with:from', 'date_format:Y-m-d', 'after_or_equal:from']
        ]);

        $user = Mobileuser::findOrFail($id);

        $query = Session::where([
            'status' => 'Unlisted',
            'user_id' => $id
        ]);

        $type = strtolower($request->type ?? '');
        if (in_array($type, ['dealer', 'retailer'])) {
            $query->where('is_retailer', $type === 'retailer');
        }

        if ($request->filled(['from', 'to'])) {
            $from = $this->custom_BS_to_AD($request->from);
            $to = $this->custom_BS_to_AD($request->to);

            $query->whereBetween('date', [$from, $to]);
        }

        $sessions = $query->with(['visitPurposes' => function ($query) {
            $query->where('status', 'Unlisted')
                ->with('purposes');
        }])->get();

        $sessions = $sessions->map(function ($session) {
            $session->formatted_purposes = $session->visitPurposes
                ->groupBy('purpose_id')
                ->map(function ($group) {
                    return (object) [
                        'purpose' => optional($group->first()->purposes)->purpose ?? 'Unknown',
                        'visit_count' => $group->count()
                    ];
                })
                ->values();

            return $session;
        });

        return view('dashboards.negotiations.show', [
            'list' => $sessions,
            'from' => $request->from,
            'to' => $request->to,
            'user' => $user,
            'type' => $request->type
        ]);
    }

    public function map($id)
    {
        $session = Session::select('id', 'latitude', 'longitude', 'dealer_name')->findorFail($id);
        if (!$session->latitude && !$session->longitude) {
            abort(404);
        }
        $data['visit'] = $session;
        return view('dashboards.negotiations.map', $data);
    }
}

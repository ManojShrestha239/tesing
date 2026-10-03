<?php

namespace App\Http\Controllers;

use App\Models\ConstructionStage;
use App\Models\SiteFollowUp;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Http\Request;

class FollowUpControllerV2 extends Controller
{
    use CustomNepaliCalendarConverter;

    protected $todayNepaliDate;

    public function __construct()
    {
        $this->todayNepaliDate = $this->todayNepaliDate();
    }

    public function index(Request $request)
    {
        $query = SiteFollowUp::query()->with(
            'site:id,name',
            'createdBy:id,name',
            'createdByDashboard:id,name',
            'assignedTo:id,name',
        );

        // Apply filters based on status
        if ($request->has('status')) {
            $status = $request->get('status');
            $today = $this->todayNepaliDate;

            switch ($status) {
                case 'today':
                    $query->where('follow_up_date', $today);
                    break;
                case 'overdue':
                    $query->where('follow_up_date', '<', $today);
                    break;
                case 'upcoming':
                    $query->where('follow_up_date', '>', $today);
                    break;
                case 'completed':
                    $query->whereNotNull('completed_at');
                    break;
            }
        }

        // Apply search filter
        if ($request->has('search') && $request->get('search')) {
            $searchTerm = $request->get('search');
            $query->whereHas('site', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            })->orWhereHas('createdBy', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            })->orWhereHas('createdByDashboard', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            })->orWhere('follow_up_type', 'like', '%' . $searchTerm . '%');
        }

        $followUps = $query->orderByDesc('created_at')->paginate(15)->onEachSide(1);

        // Counts (always show total counts regardless of search)
        $counts = SiteFollowUp::query()
            ->selectRaw("
            COUNT(CASE WHEN follow_up_date = ? THEN 1 END) as today,
            COUNT(CASE WHEN follow_up_date < ? THEN 1 END) as overdue,
            COUNT(CASE WHEN follow_up_date > ? THEN 1 END) as upcoming,
            COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed
        ", [
                $this->todayNepaliDate,
                $this->todayNepaliDate,
                $this->todayNepaliDate
            ])
            ->first();

        $data = [
            'followUps' => $followUps,
            'today' => $counts->today ?? 0,
            'overdue' => $counts->overdue ?? 0,
            'upcoming' => $counts->upcoming ?? 0,
            'completed' => $counts->completed ?? 0,
        ];

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('dashboards.new-site.followUpDetail', $data);
    }

    public function create()
    {
        return view('dashboards.new-site.create-followUp');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use App\Models\VisitPurpose;
use App\Models\Payment;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use App\Exports\TadaReportExport;
use Maatwebsite\Excel\Facades\Excel;

class TadaReportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    private function isDashboardWebUser(): bool
    {
        $user = auth()->user();

        return $user instanceof \App\Models\User;
    }

    private function getDailyVisitListForReport($report)
    {
        if (empty($report->user_id) || empty($report->entry_date)) {
            return collect();
        }

        $visitList = Session::with('dealer')
            ->with('visits.purposes')
            ->where('user_id', $report->user_id)
            ->where('date', $report->entry_date)
            ->orderBy('id', 'asc')
            ->get();

        $tadaVisits = $report->visits()->with('dealer')->get();
        foreach ($tadaVisits as $visit) {
            $matchingVisit = $visitList->first(function ($dailyVisit) use ($visit) {
                return trim((string) $dailyVisit->dealer_code) === trim((string) $visit->dealer_code);
            });

            if ($matchingVisit) {
                $visit->setRelation('visits', $matchingVisit->visits);
                $matchingVisit->setAttribute('status', $visit->visit_type ?? $matchingVisit->status);
                $matchingVisit->setAttribute('visit_type', $visit->visit_type ?? $matchingVisit->visit_type);
                $matchingVisit->setAttribute('remarks', $matchingVisit->remarks ?: $visit->remarks);
                $matchingVisit->setAttribute('is_physical_visit', $matchingVisit->is_physical_visit ?? $visit->is_physical_visit);
                $matchingVisit->setAttribute('unlisted_dealer_name', $visit->unlisted_dealer_name ?? $matchingVisit->unlisted_dealer_name);
                $matchingVisit->setAttribute('dealer_name', $visit->dealer_name ?? $matchingVisit->dealer_name ?? $visit->unlisted_dealer_name ?? $matchingVisit->unlisted_dealer_name);
                $matchingVisit->setAttribute('dealer_code', $visit->dealer_code ?? $matchingVisit->dealer_code);
                continue;
            }

            $visit->setAttribute('status', $visit->visit_type);
            $visit->setAttribute('start_time', null);
            $visit->setAttribute('end_time', null);
            $visit->setAttribute('is_physical_visit', $visit->is_physical_visit);
            $visitList->push($visit);
        }

        return $visitList;
    }

    private function fillMissingAttendanceKilometers($report): void
    {
        $attendanceKm = \App\Models\TadaEntry::attendanceKilometersForUserDate(
            $report->user_id,
            $report->entry_date
        );

        if (($report->starting_km === null || (float) $report->starting_km === 0.0)
            && $attendanceKm['starting_km'] !== null
        ) {
            $report->starting_km = $attendanceKm['starting_km'];
        }

        if (($report->ending_km === null || (float) $report->ending_km === 0.0)
            && $attendanceKm['ending_km'] !== null
        ) {
            $report->ending_km = $attendanceKm['ending_km'];
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->filled('start-date') && !$request->filled('end-date')) {
            $todayBs = $this->custom_AD_to_BS(now()->toDateString());
            $request->merge([
                'start-date' => $todayBs,
                'end-date' => $todayBs,
            ]);
        }

        if ($request->query->count() > 0) {
            $input = $request->all();

            if (!empty($input['start-date']) && $this->isNepaliDateString($input['start-date'])) {
                $input['start-date'] = $this->custom_BS_to_AD($input['start-date']);
            }

            if (!empty($input['end-date']) && $this->isNepaliDateString($input['end-date'])) {
                $input['end-date'] = $this->custom_BS_to_AD($input['end-date']);
            }

            $validator = Validator::make($input, [
                'start-date' => ['required', 'date_format:Y-m-d'],
                'end-date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start-date'],
            ], [
                'start-date.required' => 'From Date is required.',
                'start-date.date_format' => 'From Date must be in YYYY-MM-DD format.',
                'end-date.required' => 'To Date is required.',
                'end-date.date_format' => 'To Date must be in YYYY-MM-DD format.',
                'end-date.after_or_equal' => 'To Date must be greater than or equal to From Date.',
            ]);

            if ($validator->fails()) {
                return redirect()->route('tada-report.index')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $users = \App\Models\Mobileuser::orderBy('name')->get();
        $selectedUserIds = collect((array) $request->input('user', []))
            ->filter(static fn($userId) => is_numeric($userId) && (int) $userId > 0)
            ->map(static fn($userId) => (int) $userId)
            ->values()
            ->all();

        $query = \App\Models\TadaEntry::with([
            'user',
            'visits.dealer',
            'dealerExpenses.dealer',
            'dealerExpenses.expenseAuthorizedUser',
            'otherExpenses.expenseAuthorizedUser',
            'authorizedUser',
        ])
            ->whereHas('authorizedUser');

        if ($selectedUserIds !== []) {
            $query->whereIn('user_id', $selectedUserIds);
        }

        if ($request->has('start-date') && !empty($request->get('start-date'))) {
            $startDate = $request->get('start-date');
            if ($this->isNepaliDateString($startDate)) {
                $startDate = $this->custom_BS_to_AD($startDate);
            }
            $query->where('entry_date', '>=', $startDate);
        }

        if ($request->has('end-date') && !empty($request->get('end-date'))) {
            $endDate = $request->get('end-date');
            if ($this->isNepaliDateString($endDate)) {
                $endDate = $this->custom_BS_to_AD($endDate);
            }
            $query->where('entry_date', '<=', $endDate);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', strtoupper($request->status));
        }

        $list = $query->orderBy('id')->get();
        $list->transform(function ($item) {
            $this->fillMissingAttendanceKilometers($item);
            $item->entry_date_bs = $item->entry_date ? $this->custom_AD_to_BS($item->entry_date) : null;
            $item->map_url = $item->entry_date_bs
                ? route('testm', ['id' => $item->user_id, 'date' => $item->entry_date_bs])
                : null;
            $item->preview_visits = $this->getDailyVisitListForReport($item);

            $visitDealerCodes = $item->preview_visits
                ->pluck('dealer_code')
                ->filter()
                ->map(static fn($code) => trim((string) $code));

            $expenseOnlyRows = $item->dealerExpenses
                ->filter(static fn($expense) => !$visitDealerCodes->contains(trim((string) $expense->dealer_code)))
                ->map(static function ($expense) {
                    return (object) [
                        'dealer_code' => $expense->dealer_code,
                        'dealer' => $expense->dealer,
                        'dealer_name' => $expense->dealer?->name,
                        'visit_type' => null,
                        'status' => null,
                        'is_physical_visit' => $expense->is_physical_visit,
                        'remarks' => $expense->remarks,
                        'is_expense_only' => true,
                        'expense_record' => $expense,
                    ];
                });

            $item->preview_visits = $item->preview_visits->concat($expenseOnlyRows);

            $attendanceImages = \App\Models\AttendanceImage::where([
                'mobileuser_id' => $item->user_id,
                'date' => $item->entry_date,
            ])->get()->keyBy('status');

            foreach (['checkin', 'checkout'] as $status) {
                $image = $attendanceImages->get($status);
                $item->{$status . '_photo_url'} = $image && $image->image
                    ? url(Storage::disk('public')->url('attendance/' . $image->image))
                    : null;
            }

            return $item;
        });

        $data = [
            'users'          => $users,
            'list'           => $list,
            'userid'         => $selectedUserIds,
            'from'           => $request->get('start-date'),
            'to'             => $request->get('end-date'),
            'selectedStatus' => $request->get('status', ''),
        ];

        return view('dashboards.visit-reports.tada_list', $data);
    }

    public function expenseReport(Request $request)
    {
        $input = $request->all();
        $displayStartDate = $request->input('start-date');
        $displayEndDate = $request->input('end-date');
        foreach (['start-date', 'end-date'] as $dateField) {
            if (!empty($input[$dateField]) && $this->isNepaliDateString($input[$dateField])) {
                $input[$dateField] = $this->custom_BS_to_AD($input[$dateField]);
            }
        }

        $request->merge($input);
        $request->validate([
            'start-date' => ['nullable', 'date_format:Y-m-d'],
            'end-date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start-date'],
        ], [
            'start-date.date_format' => 'From Date must be in YYYY-MM-DD format.',
            'end-date.date_format' => 'To Date must be in YYYY-MM-DD format.',
            'end-date.after_or_equal' => 'To Date must be greater than or equal to From Date.',
        ]);

        $users = \App\Models\Mobileuser::orderBy('name')->get();
        $selectedUserIds = collect((array) $request->input('user', []))
            ->filter(static fn($userId) => is_numeric($userId) && (int) $userId > 0)
            ->map(static fn($userId) => (int) $userId)
            ->values()
            ->all();

        $selectedExpenseTypes = collect((array) $request->input('expense_type', []))
            ->filter(static fn($type) => !empty(trim((string)$type)))
            ->map(static fn($type) => strtolower(trim((string)$type)))
            ->values()
            ->all();

        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $query = \App\Models\TadaEntry::with([
            'user',
            'dealerExpenses.expenseAuthorizedUser',
            'otherExpenses.expenseAuthorizedUser',
        ])->whereHas('authorizedUser');

        if ($selectedUserIds !== []) {
            $query->whereIn('user_id', $selectedUserIds);
        }
        if ($startDate) {
            $query->where('entry_date', '>=', $this->isNepaliDateString($startDate)
                ? $this->custom_BS_to_AD($startDate) : $startDate);
        }
        if ($endDate) {
            $query->where('entry_date', '<=', $this->isNepaliDateString($endDate)
                ? $this->custom_BS_to_AD($endDate) : $endDate);
        }

        $amountSort = in_array($request->input('amount_sort'), ['asc', 'desc'], true)
            ? $request->input('amount_sort')
            : null;

        $rawExpenses = collect();
        $expenseTypeOptions = [
            'travel' => 'Travel',
            'food' => 'Food',
            'accommodation' => 'Accommodation',
            'courier/transport' => 'Courier/Transport',
            'vehicle maintenance' => 'Vehicle Maintenance',
            'other' => 'Other',
        ];
        foreach ($query->orderByDesc('entry_date')->orderByDesc('id')->get() as $entry) {
            $entryDateBs = $entry->entry_date ? $this->custom_AD_to_BS($entry->entry_date) : null;

            foreach ($entry->dealerExpenses as $expense) {
                $type = $expense->expense_type ?: 'Dealer Expense';
                $normalizedType = strtolower(trim($type));
                $expenseTypeOptions[$normalizedType] ??= ucwords($normalizedType);
                if ($this->matchesExpenseType($type, $selectedExpenseTypes)) {
                    $rawExpenses->push((object) [
                        'date' => $entry->entry_date,
                        'date_bs' => $entryDateBs,
                        'officer' => $entry->user?->name,
                        'expense_type' => $type,
                        'amount' => (float) $expense->amount,
                        'authorized_by' => $expense->expenseAuthorizedUser?->name,
                        'remarks' => $expense->remarks,
                    ]);
                }
            }
            foreach ($entry->otherExpenses as $expense) {
                $type = $expense->expense_details ?: 'Other Expense';
                $normalizedType = strtolower(trim($type));
                $expenseTypeOptions[$normalizedType] ??= ucwords($normalizedType);
                if ($this->matchesExpenseType($type, $selectedExpenseTypes)) {
                    $rawExpenses->push((object) [
                        'date' => $entry->entry_date,
                        'date_bs' => $entryDateBs,
                        'officer' => $entry->user?->name,
                        'expense_type' => $type,
                        'amount' => (float) $expense->amount,
                        'authorized_by' => $expense->expenseAuthorizedUser?->name,
                        'remarks' => $expense->remarks,
                    ]);
                }
            }
        }

        if ($amountSort === 'asc') {
            $rawExpenses = $rawExpenses->sortBy(static fn($expense) => (float) $expense->amount)->values();
        } elseif ($amountSort === 'desc') {
            $rawExpenses = $rawExpenses->sortByDesc(static fn($expense) => (float) $expense->amount)->values();
        }

        $totalExpensesAmount = (float) $rawExpenses->sum('amount');

        $perPage = 20;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $expenses = new \Illuminate\Pagination\LengthAwarePaginator(
            $rawExpenses->forPage($currentPage, $perPage)->values(),
            $rawExpenses->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url()]
        );
        $expenses->appends($request->except('page'));

        return view('dashboards.visit-reports.expense_report', compact(
            'expenses',
            'users',
            'selectedUserIds',
            'selectedExpenseTypes',
            'expenseTypeOptions',
            'displayStartDate',
            'displayEndDate',
            'amountSort',
            'totalExpensesAmount'
        ));
    }

    public function exportExpenseReport(Request $request)
    {
        $input = $request->all();
        foreach (['start-date', 'end-date'] as $dateField) {
            if (!empty($input[$dateField]) && $this->isNepaliDateString($input[$dateField])) {
                $input[$dateField] = $this->custom_BS_to_AD($input[$dateField]);
            }
        }

        $request->merge($input);
        $request->validate([
            'start-date' => ['nullable', 'date_format:Y-m-d'],
            'end-date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start-date'],
        ]);

        $selectedUserIds = collect((array) $request->input('user', []))
            ->filter(static fn($userId) => is_numeric($userId) && (int) $userId > 0)
            ->map(static fn($userId) => (int) $userId)
            ->values()
            ->all();
        $selectedExpenseTypes = collect((array) $request->input('expense_type', []))
            ->filter(static fn($type) => !empty(trim((string) $type)))
            ->map(static fn($type) => strtolower(trim((string) $type)))
            ->values()
            ->all();

        $query = \App\Models\TadaEntry::with([
            'user',
            'dealerExpenses.expenseAuthorizedUser',
            'otherExpenses.expenseAuthorizedUser',
        ])->whereHas('authorizedUser');

        if ($selectedUserIds !== []) {
            $query->whereIn('user_id', $selectedUserIds);
        }
        if ($request->filled('start-date')) {
            $query->where('entry_date', '>=', $request->input('start-date'));
        }
        if ($request->filled('end-date')) {
            $query->where('entry_date', '<=', $request->input('end-date'));
        }

        $rows = [];
        foreach ($query->orderByDesc('entry_date')->orderByDesc('id')->get() as $entry) {
            $entryDateBs = $entry->entry_date ? $this->custom_AD_to_BS($entry->entry_date) : null;

            foreach ($entry->dealerExpenses as $expense) {
                $type = $expense->expense_type ?: 'Dealer Expense';
                if ($this->matchesExpenseType($type, $selectedExpenseTypes)) {
                    $rows[] = [
                        $entryDateBs,
                        $entry->user?->name,
                        $type,
                        (float) $expense->amount,
                        $expense->expenseAuthorizedUser?->name,
                        $expense->remarks,
                    ];
                }
            }
            foreach ($entry->otherExpenses as $expense) {
                $type = $expense->expense_details ?: 'Other Expense';
                if ($this->matchesExpenseType($type, $selectedExpenseTypes)) {
                    $rows[] = [
                        $entryDateBs,
                        $entry->user?->name,
                        $type,
                        (float) $expense->amount,
                        $expense->expenseAuthorizedUser?->name,
                        $expense->remarks,
                    ];
                }
            }
        }

        $amountSort = $request->input('amount_sort');
        if ($amountSort === 'asc') {
            usort($rows, static fn($left, $right) => $left[3] <=> $right[3]);
        } elseif ($amountSort === 'desc') {
            usort($rows, static fn($left, $right) => $right[3] <=> $left[3]);
        }

        $totalExpensesAmount = collect($rows)->sum(static fn($row) => (float) $row[3]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'dashboards.visit-reports.expense_report_pdf',
            [
                'rows' => $rows,
                'startDate' => $request->input('start-date'),
                'endDate' => $request->input('end-date'),
                'totalExpensesAmount' => $totalExpensesAmount,
            ]
        )->setPaper('a4', 'landscape');

        return $pdf->download('Expense_Report.pdf');
    }

    private function matchesExpenseType(string $expenseType, array $selectedTypes): bool
    {
        if (empty($selectedTypes)) {
            return true;
        }

        $normalizedType = strtolower(trim($expenseType));

        foreach ($selectedTypes as $selected) {
            $selectedLower = strtolower(trim($selected));

            if ($selectedLower === 'travel') {
                if ($normalizedType === 'travel' || str_contains($normalizedType, 'travel') || str_contains($normalizedType, 'tour')) {
                    return true;
                }
            } elseif ($selectedLower === 'food') {
                if ($normalizedType === 'food' || str_contains($normalizedType, 'food') || str_contains($normalizedType, 'meal')) {
                    return true;
                }
            } elseif ($selectedLower === 'accommodation') {
                if ($normalizedType === 'accommodation' || str_contains($normalizedType, 'accommodation') || str_contains($normalizedType, 'hotel') || str_contains($normalizedType, 'stay')) {
                    return true;
                }
            } elseif ($selectedLower === 'courier/transport' || $selectedLower === 'courier' || $selectedLower === 'transport') {
                if (
                    $normalizedType === 'courier/transport'
                    || str_contains($normalizedType, 'courier')
                    || str_contains($normalizedType, 'transport')
                    || str_contains($normalizedType, 'coureier')
                ) {
                    return true;
                }
            } elseif ($selectedLower === 'other') {
                if (
                    $normalizedType === 'other'
                    || str_contains($normalizedType, 'other')
                    || str_contains($normalizedType, 'misc')
                    || (!str_contains($normalizedType, 'travel')
                        && !str_contains($normalizedType, 'food')
                        && !str_contains($normalizedType, 'accommodation')
                        && !str_contains($normalizedType, 'courier')
                        && !str_contains($normalizedType, 'transport')
                        && !str_contains($normalizedType, 'coureier'))
                ) {
                    return true;
                }
            } else {
                if ($normalizedType === $selectedLower || str_contains($normalizedType, $selectedLower)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isNepaliDateString(string $date): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        $year = (int) substr($date, 0, 4);

        // In this app, BS dates are in the 2070+ range while AD dates are current 2000s.
        return $year >= 2070;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\Mobileuser::orderBy('name')->get();
        $dealers = \App\Models\Dealer::orderBy('name')->get();
        $authorizedUsers = \App\Models\TadaAuthorizedUser::where('status', 'ACTIVE')->orderBy('name')->get();
        $expenseAuthorizedUsers = \App\Models\TadaExpenseAuthorizedUser::where('status', 'ACTIVE')->orderBy('name')->get();
        return view('dashboards.visit-reports.tada_create', compact('users', 'dealers', 'authorizedUsers', 'expenseAuthorizedUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:mobileusers,id',
            'entry_date' => 'nullable|date',
            'route' => 'required|string',
            // Accept visit_category in any case (mobile sends lowercase)
            'visit_category' => 'required|string',
            'authorized_user_id' => 'required|exists:tada_authorized_users,id',
            'combined_with' => 'nullable|string',
            'starting_km' => 'nullable|numeric',
            'ending_km' => 'nullable|numeric',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $entryDate = $request->entry_date ?? now()->toDateString();
            $attendanceKm = \App\Models\TadaEntry::attendanceKilometersForUserDate($request->user_id, $entryDate);

            $entry = \App\Models\TadaEntry::create([
                'user_id' => $request->user_id,
                'entry_date' => $entryDate,
                'route' => $request->route,
                'visit_category' => $request->visit_category,
                'stay_at' => $request->stay_at,
                'is_combined_visit' => $request->is_combined_visit ?? false,
                'combined_with' => $request->combined_with,
                'starting_km' => $request->filled('starting_km')
                    ? $request->starting_km
                    : ($attendanceKm['starting_km'] ?? null),
                'ending_km' => $request->filled('ending_km')
                    ? $request->ending_km
                    : ($attendanceKm['ending_km'] ?? null),
                'remarks' => $request->remarks,
                'authorized_user_id' => $request->authorized_user_id,
                'status' => 'PENDING', // Reports start as Pending
            ]);

            if ($request->has('visits')) {
                foreach ($request->visits as $visit) {
                    if (!empty($visit['dealer_code'])) {
                        $entry->visits()->create([
                            'dealer_code' => $visit['dealer_code'],
                            'is_physical_visit' => $visit['is_physical_visit'] ?? null,
                            'visit_type' => 'offline',
                            'remarks' => $visit['remarks'] ?? null,
                        ]);

                        if (!empty($visit['amount']) && $visit['amount'] > 0) {
                            $de = $entry->dealerExpenses()->create([
                                'dealer_code' => $visit['dealer_code'],
                                'expense_type' => 'Dealer Visit',
                                'amount' => $visit['amount'],
                                'is_physical_visit' => $visit['is_physical_visit'] ?? null,
                                'expenses_authorized_by' => $visit['expenses_authorized_by'] ?? null,
                                'authorized_user_id' => $request->authorized_user_id,
                                'remarks' => $visit['remarks'] ?? null,
                            ]);

                            if (isset($visit['images']) && is_array($visit['images'])) {
                                foreach ($visit['images'] as $img) {
                                    \App\Models\TadaImage::create([
                                        'tada_entry_id' => $entry->id,
                                        'tada_dealer_expense_id' => $de->id,
                                        'receipt_image_path' => $img
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            if ($request->has('expenses')) {
                foreach ($request->expenses as $expense) {
                    if (!empty($expense['amount'])) {
                        // Accept either `expense_type` (mobile) or `expense_details` and preserve casing
                        $expenseDetails = $expense['expense_type'] ?? $expense['expense_details'] ?? 'Miscellaneous';
                        $oe = $entry->otherExpenses()->create([
                            'expense_details' => $expenseDetails,
                            'amount' => $expense['amount'],
                            'expenses_authorized_by' => $expense['expenses_authorized_by'] ?? null,
                            'authorized_user_id' => $request->authorized_user_id,
                            'remarks' => $expense['remarks'] ?? null,
                        ]);

                        if (isset($expense['images']) && is_array($expense['images'])) {
                            foreach ($expense['images'] as $img) {
                                \App\Models\TadaImage::create([
                                    'tada_entry_id' => $entry->id,
                                    'tada_other_expense_id' => $oe->id,
                                    'receipt_image_path' => $img
                                ]);
                            }
                        }
                    }
                }
            }

            // Trigger disabled: audit log on insert is commented out.
            // \App\Models\TadaActivityLog::create([
            //     'tada_entry_id' => $entry->id,
            //     'action_by_user_id' => \Illuminate\Support\Facades\Auth::id(),
            //     'action' => 'CREATED',
            //     'new_values' => $entry->toArray(),
            //     'ip_address' => $request->ip(),
            // ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('tada-report.index')->with('success', 'TADA Entry saved successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = \App\Models\TadaEntry::with(['visits.dealer', 'dealerExpenses.dealer', 'dealerExpenses.images', 'dealerExpenses.expenseAuthorizedUser', 'otherExpenses.images', 'otherExpenses.expenseAuthorizedUser', 'images', 'user', 'authorizedUser'])->findOrFail($id);
        $this->fillMissingAttendanceKilometers($report);
        // Provide Nepali date and totals for the view
        $report->entry_date_bs = $report->entry_date ? $this->custom_AD_to_BS($report->entry_date) : null;
        $dealerTotal = $report->dealerExpenses->sum('amount');
        $otherTotal = $report->otherExpenses->sum('amount');
        $report->total_expenses = ($dealerTotal ?? 0) + ($otherTotal ?? 0);
        $visitList = $this->getDailyVisitListForReport($report);
        $from = $report->entry_date_bs ?? $report->entry_date;
        $to = $from;

        // If the request is AJAX (modal), return the modal partial.
        if (request()->ajax()) {
            return view('dashboards.visit-reports.tada_show_modal', compact('report', 'visitList', 'from', 'to'));
        }

        // Otherwise show a full-page view with a Back button.
        return view('dashboards.visit-reports.tada_show_page', compact('report', 'visitList', 'from', 'to'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->isDashboardWebUser()) {
            return redirect()->route('tada-report.index')->with('error', 'Dashboard users can only review TADA reports. Editing report details is disabled.');
        }

        $report = \App\Models\TadaEntry::with(['visits', 'dealerExpenses', 'otherExpenses', 'images'])->findOrFail($id);
        $users = \App\Models\Mobileuser::orderBy('name')->get();
        $dealers = \App\Models\Dealer::orderBy('name')->get();
        $authorizedUsers = \App\Models\TadaAuthorizedUser::where('status', 'ACTIVE')->orderBy('name')->get();
        $expenseAuthorizedUsers = \App\Models\TadaExpenseAuthorizedUser::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('dashboards.visit-reports.tada_edit', compact('report', 'users', 'dealers', 'authorizedUsers', 'expenseAuthorizedUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $entry = \App\Models\TadaEntry::findOrFail($id);
        $oldStatus = $entry->status;
        $dashboardUser = $this->isDashboardWebUser();

        if ($dashboardUser) {
            $request->validate([
                'status' => 'required|in:PENDING,INREVIEW,APPROVED,REJECTED',
                'admin_remarks' => ['nullable', 'string'],
                'km_remarks' => 'nullable|string|max:500',
                'starting_km' => 'nullable|numeric',
                'ending_km' => 'nullable|numeric',
                'remarks' => 'nullable|string|max:500',
            ]);

            if ($oldStatus !== $request->status && $request->status !== 'PENDING' && empty(trim($request->input('admin_remarks', '')))) {
                return back()->withInput()->with('error', 'Please provide review remarks when changing the report status.');
            }
        } else {
            $request->validate([
                'user_id' => 'required|exists:mobileusers,id',
                'route' => 'required|string',
                // Accept visit_category in any case (mobile sends lowercase)
                'visit_category' => 'required|string',
                'authorized_user_id' => 'required|exists:tada_authorized_users,id',
                'is_combined_visit' => 'nullable',
                'combined_with' => 'nullable|string',
                'starting_km' => 'nullable|numeric',
                'ending_km' => 'nullable|numeric',
                'status' => 'required|in:PENDING,INREVIEW,APPROVED,REJECTED',
            ]);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Capture original values BEFORE update (getOriginal() syncs after update)
            $oldValues = $entry->toArray();

            if ($dashboardUser) {
                $updateData = [
                    'status' => $request->status,
                ];

                if ($request->filled('admin_remarks')) {
                    $updateData['admin_remarks'] = $request->admin_remarks;
                }

                if ($request->filled('km_remarks') || $request->has('km_remarks')) {
                    $updateData['km_remarks'] = $request->km_remarks;
                }

                if ($request->filled('starting_km') || $request->has('starting_km')) {
                    $updateData['starting_km'] = $request->starting_km;
                }

                if ($request->filled('ending_km') || $request->has('ending_km')) {
                    $updateData['ending_km'] = $request->ending_km;
                }

                if ($request->filled('remarks') || $request->has('remarks')) {
                    $updateData['remarks'] = $request->remarks;
                }

                $entry->update($updateData);
            } else {
                $updatedUserId = $request->user_id ?? $entry->user_id;
                $updatedEntryDate = $request->entry_date ?? $entry->entry_date;
                $attendanceKm = \App\Models\TadaEntry::attendanceKilometersForUserDate($updatedUserId, $updatedEntryDate);

                $entry->update([
                    'user_id' => $updatedUserId,
                    'entry_date' => $updatedEntryDate,
                    'route' => $request->route,
                    'visit_category' => $request->visit_category,
                    'stay_at' => $request->stay_at,
                    'is_combined_visit' => $request->is_combined_visit ?? false,
                    'combined_with' => $request->combined_with,
                    'starting_km' => $request->filled('starting_km')
                        ? $request->starting_km
                        : ($attendanceKm['starting_km'] ?? null),
                    'ending_km' => $request->filled('ending_km')
                        ? $request->ending_km
                        : ($attendanceKm['ending_km'] ?? null),
                    'status' => $request->status,
                    'authorized_user_id' => $request->authorized_user_id,
                ]);

                // Sync Visits and Dealer Expenses
                foreach ($entry->dealerExpenses as $de) {
                    $this->deleteExpenseImages($de);
                    $de->delete();
                }
                $entry->visits()->delete();
                if ($request->has('visits')) {
                    foreach ($request->visits as $v) {
                        if (!empty($v['dealer_code'])) {
                            $entry->visits()->create([
                                'dealer_code' => $v['dealer_code'],
                                'is_physical_visit' => $v['is_physical_visit'] ?? null,
                                'visit_type' => 'offline',
                                'remarks' => $v['remarks'] ?? null,
                            ]);

                            if (!empty($v['amount']) && $v['amount'] > 0) {
                                $de = $entry->dealerExpenses()->create([
                                    'dealer_code' => $v['dealer_code'],
                                    'expense_type' => 'Dealer Visit',
                                    'amount' => $v['amount'],
                                    'is_physical_visit' => $v['is_physical_visit'] ?? null,
                                    'expenses_authorized_by' => $v['expenses_authorized_by'] ?? null,
                                    'authorized_user_id' => $request->authorized_user_id,
                                    'remarks' => $v['remarks'] ?? null,
                                ]);

                                if (isset($v['images']) && is_array($v['images'])) {
                                    foreach ($v['images'] as $img) {
                                        \App\Models\TadaImage::create([
                                            'tada_entry_id' => $entry->id,
                                            'tada_dealer_expense_id' => $de->id,
                                            'receipt_image_path' => $img
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                // Sync Other Expenses
                foreach ($entry->otherExpenses as $oe) {
                    $this->deleteExpenseImages($oe);
                    $oe->delete();
                }
                if ($request->has('expenses')) {
                    foreach ($request->expenses as $ex) {
                        if (!empty($ex['amount'])) {
                            $expenseDetails = $ex['expense_type'] ?? $ex['expense_details'] ?? 'Miscellaneous';
                            $oe = $entry->otherExpenses()->create([
                                'expense_details' => $expenseDetails,
                                'amount' => $ex['amount'],
                                'expenses_authorized_by' => $ex['expenses_authorized_by'] ?? null,
                                'authorized_user_id' => $request->authorized_user_id,
                                'remarks' => $ex['remarks'] ?? null,
                            ]);

                            if (isset($ex['images']) && is_array($ex['images'])) {
                                foreach ($ex['images'] as $img) {
                                    \App\Models\TadaImage::create([
                                        'tada_entry_id' => $entry->id,
                                        'tada_other_expense_id' => $oe->id,
                                        'receipt_image_path' => $img
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // Commit the main update first - the activity log should not block the report update.
            \Illuminate\Support\Facades\DB::commit();

            // Trigger disabled: update audit log is commented out.
            // try {
            //     \App\Models\TadaActivityLog::create([
            //         'tada_entry_id'     => $entry->id,
            //         'action_by_user_id' => \Illuminate\Support\Facades\Auth::id(),
            //         'action'            => 'UPDATED',
            //         'old_values'        => $oldValues,
            //         'new_values'        => $entry->fresh()->toArray(),
            //         'ip_address'        => $request->ip(),
            //     ]);
            // } catch (\Exception $logEx) {
            //     \Illuminate\Support\Facades\Log::warning('TADA activity log failed: ' . $logEx->getMessage());
            // }

            // Notification: send FCM to mobile user when status changes (include admin remarks)

            if ($oldStatus !== $request->status) {
                $mobileUser = \App\Models\Mobileuser::find($entry->user_id);

                if ($mobileUser && !empty($mobileUser->fcm_id)) {
                    $notifTitle = null;
                    $notifMessage = null;

                    $adminRemarks = trim((string)$request->input('admin_remarks', ''));

                    if ($request->status === 'INREVIEW') {
                        $notifTitle = "TADA Report Under Review";
                        $notifMessage = "Your TADA report for {$entry->entry_date} is now under review.";
                    } elseif ($request->status === 'APPROVED') {
                        $notifTitle = "TADA Report Approved";
                        $notifMessage = "Your TADA report for {$entry->entry_date} has been approved.";
                    } elseif ($request->status === 'REJECTED') {
                        $notifTitle = "TADA Report Rejected";
                        $notifMessage = "Your TADA report for {$entry->entry_date} has been rejected.";
                    } elseif ($request->status === 'PENDING') {
                        $notifTitle = "TADA Report Reset to Pending";
                        $notifMessage = "Your TADA report for {$entry->entry_date} has been reset to Pending.";
                    }

                    if (!empty($adminRemarks)) {
                        $notifMessage .= " Remarks: " . $adminRemarks;
                    }

                    if ($notifTitle) {
                        try {
                            $notification = new \App\Http\Controllers\NotificationController();
                            $notification->individualNotificationSend($mobileUser->fcm_id, $notifTitle, "TADA_STATUS", [
                                'Message' => $notifMessage,
                                'status' => $request->status === 'INREVIEW' ? 'In-Review' : strtolower($request->status)
                            ]);
                        } catch (\Exception $ne) {
                            // Notification failure is intentionally not logged here.
                        }
                    }
                }
            }

            $isKmUpdate = $request->isMethod('PATCH') || $request->has('starting_km') || $request->has('ending_km');
            $message = $dashboardUser
                ? ($isKmUpdate ? 'TADA KMs and remarks updated successfully.' : 'TADA status updated successfully.')
                : 'TADA Report updated successfully.';

            return redirect()->route('tada-report.index')->with('success', $message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Error updating report: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF of the specified resource.
     */
    public function downloadPdf($id)
    {
        $report = \App\Models\TadaEntry::with([
            'user',
            'visits',
            'dealerExpenses.images',
            'dealerExpenses.expenseAuthorizedUser',
            'otherExpenses.images',
            'otherExpenses.expenseAuthorizedUser',
            'images',
            'authorizedUser'
        ])->findOrFail($id);
        $this->fillMissingAttendanceKilometers($report);

        // Ensure Nepali date and totals are available in PDF
        $report->entry_date_bs = $report->entry_date ? $this->custom_AD_to_BS($report->entry_date) : null;
        $dealerTotal = $report->dealerExpenses->sum('amount');
        $otherTotal = $report->otherExpenses->sum('amount');
        $report->total_expenses = ($dealerTotal ?? 0) + ($otherTotal ?? 0);
        $visitList = $this->getDailyVisitListForReport($report);
        $from = $report->entry_date_bs ?? $report->entry_date;
        $to = $from;

        $fileNameDate = $report->entry_date_bs ?? str_replace('-', '', now()->toDateString());

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboards.visit-reports.tada_pdf', compact('report', 'visitList', 'from', 'to'))
            ->setOptions(['isRemoteEnabled' => true, 'chroot' => public_path()]);

        return $pdf->download('Daily_Report_' . $fileNameDate . '.pdf');
    }

    /**
     * Export filtered TADA list to Excel
     */
    public function export(Request $request)
    {
        $query = \App\Models\TadaEntry::with(['user', 'visits.dealer', 'dealerExpenses', 'otherExpenses']);

        if ($request->has('user') && !empty($request->user)) {
            $query->where('user_id', $request->user);
        }

        if ($request->has('start-date') && !empty($request->get('start-date'))) {
            $startDate = $request->get('start-date');
            if ($this->isNepaliDateString($startDate)) {
                $startDate = $this->custom_BS_to_AD($startDate);
            }
            $query->where('entry_date', '>=', $startDate);
        }

        if ($request->has('end-date') && !empty($request->get('end-date'))) {
            $endDate = $request->get('end-date');
            if ($this->isNepaliDateString($endDate)) {
                $endDate = $this->custom_BS_to_AD($endDate);
            }
            $query->where('entry_date', '<=', $endDate);
        }

        $list = $query->orderBy('id')->get();

        $rows = [];
        foreach ($list as $entry) {
            $this->fillMissingAttendanceKilometers($entry);
            // compute Nepali date and totals for the entry
            $entry->entry_date_bs = $entry->entry_date ? $this->custom_AD_to_BS($entry->entry_date) : null;
            $dealerTotal = $entry->dealerExpenses->sum('amount');
            $otherTotal = $entry->otherExpenses->sum('amount');
            $entryTotal = ($dealerTotal ?? 0) + ($otherTotal ?? 0);

            // base columns: Entry Date, Entry Date (BS), Officer, Route, TADA Type, KM, Status
            $base = [
                $entry->entry_date,
                $entry->entry_date_bs,
                $entry->user->name ?? '-',
                $entry->route,
                $entry->visit_category,
                number_format(max(0, ($entry->ending_km ?? 0) - ($entry->starting_km ?? 0)), 2),
                $entry->status,
            ];

            $hasDealerRows = false;

            // For each dealer visit, include a row per dealer
            foreach ($entry->visits as $visit) {
                $visitAmount = $entry->dealerExpenses->where('dealer_code', $visit->dealer_code)->sum('amount');
                $visitTypes = $entry->dealerExpenses->where('dealer_code', $visit->dealer_code)->pluck('expense_type')->filter()->unique()->values()->all();
                $hasDealerRows = true;

                // Columns after base: Dealer Name, Expense Amount, Expense Types, Total Expenses, Physical Visit, Remarks
                $rows[] = array_merge($base, [
                    $visit->dealer->name ?? $visit->dealer_code,
                    $visitAmount,
                    !empty($visitTypes) ? implode(', ', $visitTypes) : '-',
                    $entryTotal,
                    $visit->is_physical_visit === null ? 'N/A' : ($visit->is_physical_visit ? 'Yes' : 'No'),
                    $visit->remarks,
                ]);
            }

            // Include other expenses (when dealer is not selected) as separate rows in Excel
            foreach ($entry->otherExpenses as $otherExpense) {
                $rows[] = array_merge($base, [
                    '-',
                    (float) $otherExpense->amount,
                    $otherExpense->expense_details ?? 'Other',
                    $entryTotal,
                    '-',
                    $otherExpense->remarks ?? '-',
                ]);
            }

            // If there are no dealer visits and no other expenses, add a single row with base info only
            if (!$hasDealerRows && $entry->otherExpenses->count() === 0) {
                $rows[] = array_merge($base, ['-', 0, '-', $entryTotal, '-', $entry->remarks]);
            }
        }

        return Excel::download(new TadaReportExport($rows), 'Daily_Report.xlsx');
    }

    /**
     * Admin Daily Report export – PDF download.
     *
     * Supports:
     *  • Single user + single date
     *  • Single user + date range
     *  • All users + today (when no dates provided)
     *  • All users + date range
     */
    public function adminExport(Request $request)
    {
        $query = \App\Models\TadaEntry::with([
            'user',
            'visits.dealer',
            'dealerExpenses.dealer',
            'dealerExpenses.expenseAuthorizedUser',
            'otherExpenses.expenseAuthorizedUser',
            'authorizedUser',
        ]);

        if ($request->has('user') && !empty($request->user)) {
            $query->where('user_id', $request->user);
        }

        $hasStart = $request->has('start-date') && !empty($request->get('start-date'));
        $hasEnd   = $request->has('end-date')   && !empty($request->get('end-date'));

        if ($hasStart) {
            $startDate = $request->get('start-date');
            if ($this->isNepaliDateString($startDate)) {
                $startDate = $this->custom_BS_to_AD($startDate);
            }
            $query->where('entry_date', '>=', $startDate);
        }

        if ($hasEnd) {
            $endDate = $request->get('end-date');
            if ($this->isNepaliDateString($endDate)) {
                $endDate = $this->custom_BS_to_AD($endDate);
            }
            $query->where('entry_date', '<=', $endDate);
        }

        if (!$hasStart && !$hasEnd) {
            $query->where('entry_date', '=', now()->toDateString());
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', strtoupper($request->status));
        }

        $list = $query->orderBy('entry_date')->orderBy('user_id')->get();

        $list->transform(function ($item) {
            $this->fillMissingAttendanceKilometers($item);
            $item->entry_date_bs = $item->entry_date ? $this->custom_AD_to_BS($item->entry_date) : null;
            $item->map_url = $item->entry_date_bs
                ? route('testm', ['id' => $item->user_id, 'date' => $item->entry_date_bs])
                : null;
            $item->preview_visits = $this->getDailyVisitListForReport($item);

            $visitDealerCodes = $item->preview_visits
                ->pluck('dealer_code')
                ->filter()
                ->map(static fn($code) => trim((string) $code));

            $expenseOnlyRows = $item->dealerExpenses
                ->filter(static fn($expense) => !$visitDealerCodes->contains(trim((string) $expense->dealer_code)))
                ->map(static function ($expense) {
                    return (object) [
                        'dealer_code' => $expense->dealer_code,
                        'dealer' => $expense->dealer,
                        'dealer_name' => $expense->dealer?->name,
                        'visit_type' => null,
                        'status' => null,
                        'is_physical_visit' => $expense->is_physical_visit,
                        'remarks' => $expense->remarks,
                        'is_expense_only' => true,
                        'expense_record' => $expense,
                    ];
                });

            $item->preview_visits = $item->preview_visits->concat($expenseOnlyRows);

            $attendanceImages = \App\Models\AttendanceImage::where([
                'mobileuser_id' => $item->user_id,
                'date' => $item->entry_date,
            ])->get()->keyBy('status');

            foreach (['checkin', 'checkout'] as $status) {
                $image = $attendanceImages->get($status);
                $item->{$status . '_photo_url'} = $image && $image->image
                    ? url(\Illuminate\Support\Facades\Storage::disk('public')->url('attendance/' . $image->image))
                    : null;
            }

            return $item;
        });

        if ($hasStart && $hasEnd) {
            $dateLabel = $request->get('start-date') . ' to ' . $request->get('end-date');
        } elseif ($hasStart) {
            $dateLabel = $request->get('start-date');
        } elseif ($hasEnd) {
            $dateLabel = $request->get('end-date');
        } else {
            $todayBs = $this->custom_AD_to_BS(now()->toDateString());
            $dateLabel = $todayBs . ' (Today)';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'dashboards.visit-reports.tada_admin_pdf',
            compact('list', 'dateLabel')
        )->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => true, 'chroot' => public_path()]);

        return $pdf->download('Sales_Officers_Daily_Report.pdf');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = \App\Models\TadaEntry::findOrFail($id);

        $entry->delete();

        // Trigger disabled: delete audit log is commented out.
        // \App\Models\TadaActivityLog::create([
        //     'tada_entry_id' => $id,
        //     'action_by_user_id' => \Illuminate\Support\Facades\Auth::id(),
        //     'action' => 'DELETED',
        //     'old_values' => $entry->toArray(),
        //     'ip_address' => request()->ip(),
        // ]);

        return redirect()->route('tada-report.index')->with('success', 'TADA Report deleted successfully.');
    }

    private function deleteExpenseImages($expense): void
    {
        foreach ($expense->images as $img) {
            if (empty($img->receipt_image_path)) {
                $img->delete();
                continue;
            }

            $storagePath = $img->receipt_image_path;
            if (preg_match('/^https?:\/\//i', $storagePath)) {
                $parsed = parse_url($storagePath);
                if (isset($parsed['path'])) {
                    $storagePath = $parsed['path'];
                    $pos = strpos($storagePath, 'uploads/');
                    if ($pos !== false) {
                        $storagePath = substr($storagePath, $pos);
                    }
                }
            }

            $storagePath = ltrim($storagePath, '/');
            $hasOtherReferences = \App\Models\TadaImage::where('receipt_image_path', $img->receipt_image_path)
                ->where('id', '!=', $img->id)
                ->exists();

            if (!$hasOtherReferences && Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            $img->delete();
        }
    }
}

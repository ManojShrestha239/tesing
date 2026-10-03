<div class="modal-body" style="padding: 20px;">
    <div class="row">
        <div class="col-md-6">
            <p class="margin-bottom-10"><strong>Officer:</strong> {{ $report->user->name ?? 'N/A' }}</p>
            <p class="margin-bottom-10"><strong>Route:</strong> {{ $report->route }}</p>
            <p class="margin-bottom-10"><strong>Category:</strong> {{ $report->visit_category }}</p>
            <p class="margin-bottom-10"><strong>Stay At:</strong> {{ $report->stay_at ?? 'N/A' }}</p>
        </div>
        <div class="col-md-6">
            <p class="margin-bottom-10"><strong>Combined Visit:</strong>
                @if($report->is_combined_visit && !empty(trim($report->combined_with ?? '')))
                Yes ({{ $report->combined_with }})
                @else
                No
                @endif
            </p>
            <p class="margin-bottom-10"><strong>Starting KM:</strong> {{ number_format($report->starting_km ?? 0, 2) }} KM</p>
            <p class="margin-bottom-10"><strong>Ending KM:</strong> {{ number_format($report->ending_km ?? 0, 2) }} KM</p>
            <p class="margin-bottom-10"><strong>Distance:</strong> {{ number_format(max(0, ($report->ending_km ?? 0) - ($report->starting_km ?? 0)), 2) }} KM</p>
        </div>
    </div>



    @if($report->dealerExpenses && $report->dealerExpenses->count())

    <hr>
    <h5><strong>Dealer Expenses</strong></h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Dealer Name</th>
                    <th>Expense Type</th>
                    <th>Physical Expense</th>
                    <th>Amount</th>
                    <th>Expenses Authorized By</th>
                    <th>Remarks</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->dealerExpenses->groupBy('dealer_code') as $dealerCode => $expenses)
                <tr>
                    @php
                    $visit = $report->visits->firstWhere('dealer_code', $dealerCode);
                    $amt = $expenses->sum('amount');
                    $types = $expenses->pluck('expense_type')->filter()->unique()->values()->all();
                    $imageUrls = $expenses->flatMap(function($exp) {
                    return $exp->images->map(function($img) {
                    return Storage::disk('public')->url($img->receipt_image_path);
                    });
                    })->values()->all();
                    @endphp

                    <td>{{ $expenses->first()->dealer->name ?? $visit->dealer->name ?? $dealerCode }} [{{ $dealerCode }}]</td>
                    <td>{{ !empty($types) ? implode(', ', $types) : '-' }}</td>
                    @php
                    $physicalExpense = $expenses->first(fn($expense) => $expense->is_physical_visit !== null);
                    $physicalVisit = $physicalExpense?->is_physical_visit ?? $visit?->is_physical_visit;
                    @endphp
                    <td>{{ $physicalVisit === null ? 'N/A' : ($physicalVisit ? 'Yes' : 'No') }}</td>
                    <td>Rs. {{ number_format($amt, 2) }}</td>
                    <td>
                        @php
                        $expenseAuthorizers = $expenses->pluck('expenseAuthorizedUser.name')->filter()->unique()->values()->all();
                        @endphp
                        {{ !empty($expenseAuthorizers) ? implode(', ', $expenseAuthorizers) : '-' }}
                    </td>
                    <td>{{ $expenses->pluck('remarks')->filter()->unique()->implode(', ') ?: ($visit->remarks ?? '-') }}</td>
                    <td>
                        @if(!empty($imageUrls))
                        <button type="button" class="btn btn-info btn-sm view-expense-images-btn"
                            data-images='@json($imageUrls)'>View Files</button>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($report->otherExpenses && $report->otherExpenses->count())
    <hr>
    <h5><strong>User Expenses</strong></h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Expense Type</th>
                    <th>Amount</th>
                    <th>Expenses Authorized By</th>
                    <th>Remarks</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->otherExpenses as $exp)
                <tr>
                    <td>{{ $exp->expense_details }}</td>
                    <td>Rs. {{ number_format($exp->amount, 2) }}</td>
                    <td>{{ $exp->expenseAuthorizedUser->name ?? '-' }}</td>
                    <td>{{ $exp->remarks }}</td>
                    <td>
                        @if($exp->images && $exp->images->count())
                        @php
                        $imageUrls = $exp->images->map(function($img) {
                        return Storage::disk('public')->url($img->receipt_image_path);
                        })->all();
                        @endphp
                        <button type="button" class="btn btn-info btn-sm view-expense-images-btn"
                            data-images='@json($imageUrls)'>View Files</button>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if (!empty($visitList) && count($visitList))
    <hr>
    <h5><strong>Daily Visits</strong></h5>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th> Visit No.</th>
                    <th> Dealer </th>
                    <th> Purpose </th>
                    <!-- <th> Date </th> -->
                    <th> Start To End Time </th>
                    <th> Physical Visit </th>
                    <th> Visit Status </th>
                    <th> Remarks </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($visitList as $lis)
                <tr>
                    <td> {{ $lis['id'] }} </td>
                    @php
                    $dailyDealerName = !empty($lis->unlisted_dealer_name)
                    ? ($lis->unlisted_dealer_name . ' (Unlisted)')
                    : ($lis->dealer->name ?? $lis->dealer_name ?? $lis->dealer_code ?? 'N/A');
                    $physicalVisitValue = $lis->is_physical_visit ?? $lis->physical_visit;
                    $visitStatus = strtolower(trim((string) ($lis->status ?? $lis->visit_type ?? '')));
                    $physicalVisitLabel = $visitStatus === 'online'
                    ? 'present'
                    : ($visitStatus === 'unlisted'
                    ? (filter_var($physicalVisitValue, FILTER_VALIDATE_BOOLEAN) ? 'present' : 'remote')
                    : ($physicalVisitValue === null ? 'N/A' : ($physicalVisitValue ? 'present' : 'remote')));
                    @endphp
                    <td> {{ $dailyDealerName }}</td>
                    <td>
                        @if(isset($lis->visits))
                        @foreach ($lis->visits as $v)
                        {{ $v->purposes['purpose'] }},
                        @endforeach
                        @endif
                    </td>
                    <!-- <td> {{ app('App\Utilities\Helpers')->convertDate($lis['date']) }}</td> -->
                    <td> {{ $lis['start_time'] }} To {{ $lis['end_time'] }}
                        @if(!empty($lis['start_time']) && !empty($lis['end_time']))
                        <?php
                        $start = new DateTime($lis['start_time']);
                        $end = new DateTime($lis['end_time']);

                        $interval = $end->diff($start);
                        echo '(' . $interval->format('%h') . ' hrs ' . $interval->format('%i') . ' min' . $interval->format('%s') . ' sec)';
                        ?>
                        @endif
                    </td>
                    <td>{{ $physicalVisitLabel }}</td>
                    <td>
                        @if (strtolower($lis['status']) === 'online')
                        <button class='btn btn-success' style='background-color: #28a745 !important; border-color: #28a745 !important; color: #fff !important;'><strong>{{ $lis['status'] }}</strong></button>
                        @elseif (strtolower($lis['status']) === 'unlisted')
                        <button class='btn btn-warning' style='background-color: #e4dd12 !important; border-color: #e4dd12 !important; color: #565656 !important;'><strong>{{ $lis['status'] }}</strong></button>
                        @else
                        <button class='btn btn-danger' style='background-color: #dc3545 !important; border-color: #dc3545 !important; color: #fff !important;'><strong>{{ $lis['status'] }}</strong></button>
                        @endif
                    </td>
                    <td> {{ $lis['remarks'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Attachments removed per UI request --}}

    {{-- Move Report Remarks to bottom of the page per UI request --}}
    {{-- Total Expenses above remarks --}}
    <div class="row" style="margin-top:10px;">
        <div class="col-md-12">
            <p><strong>Total Expenses:</strong> Rs. {{ number_format($report->total_expenses ?? 0, 2) }}</p>
        </div>
    </div>

    @if($report->remarks || $report->admin_remarks || $report->km_remarks)
    <hr>
    <h5><strong>Report Remarks</strong></h5>
    <div class="row">
        @if($report->remarks)
        <div class="col-md-12">
            <p><strong>Submitted Remarks:</strong><br>{{ $report->remarks }}</p>
        </div>
        @endif
        @if($report->admin_remarks)
        <div class="col-md-12">
            <p><strong>Admin Review Remarks:</strong><br>{{ $report->admin_remarks }}</p>
        </div>
        @endif
        @if($report->km_remarks)
        <div class="col-md-12">
            <p><strong>KM Edit Remarks:</strong><br>{{ $report->km_remarks }}</p>
        </div>
        @endif
    </div>
    @endif
</div>

@if(request()->ajax())
<div class="modal-footer">
    <button type="button" class="btn btn-default" onclick="document.getElementById('tadaDetailsModal').style.display='none'; document.body.classList.remove('modal-open'); var b=document.getElementsByClassName('modal-backdrop')[0]; if(b) b.parentNode.removeChild(b);">Close</button>
</div>
@endif
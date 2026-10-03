<!DOCTYPE html>
<html>

<head>
    <title>Daily Report - {{ $report->entry_date_bs ?? '' }}</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
            margin-bottom: 5px;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .header-title {
            text-align: center;
        }

        .header-title h2 {
            margin: 0;
            font-size: 15px;
        }

        .header-title p {
            margin: 2px 0;
            font-size: 10px;
        }

        .report-title-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .report-title-table td {
            padding: 0;
        }

        .report-title-cell {
            width: 34%;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        .header-date-cell {
            width: 33%;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
        }

        .spacer-cell {
            width: 33%;
        }

        .info-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 11px;
            border-collapse: collapse;
            border-spacing: 0;
            page-break-inside: avoid;
        }

        .info-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            white-space: nowrap;
            padding-right: 4px !important;
            width: 1%;
        }

        .value {
            border-bottom: 1px dotted #666;
            padding-left: 4px !important;
            padding-right: 10px !important;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .section-title-small {
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            padding-bottom: 3px;
        }

        .value-plain {
            padding-left: 4px !important;
            padding-right: 10px !important;
        }

        .visits-label {
            font-weight: bold;
            white-space: nowrap;
            padding-right: 4px !important;
            width: 1%;
            font-size: 13px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #999;
            padding: 3px;
            text-align: left;
        }

        table.data-table th {
            background-color: #f2f2f2;
            font-size: 11px;
        }

        table.data-table td {
            font-size: 11px;
        }

        .footer {
            margin-top: 50px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 60px;
            font-size: 11px;
        }

        .sig-left {
            float: left;
        }

        .sig-right {
            float: right;
        }

        .status-badge {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: #e7505a;
        }

        .clear {
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .km-row td {
            padding: 1px 2px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-title">
            <h2>Jagannath Group of Industries Pvt. Ltd.</h2>
        </div>
        <table class="report-title-table">
            <tr>
                <td class="spacer-cell">Daily Visit ID: {{ $report->id }}</td>
                <td class="report-title-cell">Daily Report</td>
                <td class="header-date-cell">Date: {{ $report->entry_date_bs ?? '' }}</td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Name:</td>
            <td class="value">{{ $report->user->name }}</td>
            <td class="label">Visit Cat:</td>
            <td class="value">{{ $report->visit_category }}</td>
        </tr>
        <tr>
            <td class="label">Route:</td>
            <td class="value">{{ $report->route }}</td>
            <td class="label">Stay at:</td>
            <td class="value">{{ $report->stay_at ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Combined V.:</td>
            <td class="value">
                @if($report->is_combined_visit && !empty(trim($report->combined_with ?? '')))
                YES
                @else
                NO
                @endif
            </td>
            <td class="label">With:</td>
            <td class="value">
                @if($report->is_combined_visit && !empty(trim($report->combined_with ?? '')))
                {{ $report->combined_with }}
                @else
                N/A
                @endif
            </td>
        </tr>
        <tr class="km-row">
            <td colspan="4" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="label">Start KM:</td>
                        <td class="value">{{ number_format($report->starting_km ?? 0, 2) }}</td>
                        <td class="label">End KM:</td>
                        <td class="value">{{ number_format($report->ending_km ?? 0, 2) }}</td>
                        <td class="label">Total Distance:</td>
                        <td class="value">{{ number_format(max(0, ($report->ending_km ?? 0) - ($report->starting_km ?? 0)), 2) }} KM</td>
                    </tr>
                </table>
            </td>
        </tr>
        @if($report->remarks)
        <tr>
            <td class="label">Remarks:</td>
            <td class="value" colspan="3">{{ $report->remarks }}</td>
        </tr>
        @endif
        @if($report->admin_remarks)
        <tr>
            <td class="label">Admin Review Remarks:</td>
            <td class="value" colspan="3">{{ $report->admin_remarks }}</td>
        </tr>
        @endif
        <tr>
            <td class="visits-label">VISITS</td>
            <td class="value-plain">&nbsp;</td>
            <td class="label text-right">Route Authorized By:</td>
            <td class="value">
                @if($report->authorizedUser)
                {{ $report->authorizedUser->name }}@if($report->authorizedUser->designation) ({{ $report->authorizedUser->designation }})@endif
                @else
                &nbsp;
                @endif
            </td>
        </tr>
    </table>

    @if (!empty($visitList) && count($visitList) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Visit No.</th>
                <th>Date</th>
                <th>Dealer</th>
                <th>Purpose</th>
                <th>Start To End Time</th>
                <th>Physical Visit</th>
                <th>Visit Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visitList as $lis)
            @php
            $physicalVisitValue = $lis->is_physical_visit ?? $lis->physical_visit;
            $visitStatus = strtolower(trim((string) ($lis->status ?? $lis->visit_type ?? '')));
            $physicalVisitLabel = $visitStatus === 'online'
            ? 'present'
            : ($visitStatus === 'unlisted'
            ? (filter_var($physicalVisitValue, FILTER_VALIDATE_BOOLEAN) ? 'present' : 'remote')
            : ($physicalVisitValue === null ? 'N/A' : ($physicalVisitValue ? 'present' : 'remote')));
            @endphp
            <tr>
                <td>{{ $lis['id'] }}</td>
                <td>{{ app('App\Utilities\Helpers')->convertDate($lis['date']) }}</td>
                @php
                $dailyDealerName = !empty($lis->unlisted_dealer_name)
                ? ($lis->unlisted_dealer_name . ' (Unlisted)')
                : ($lis->dealer->name ?? $lis->dealer_name ?? $lis->dealer_code ?? 'N/A');
                @endphp
                <td>{{ $dailyDealerName }}</td>
                <td>
                    @if(isset($lis->visits))
                    @foreach ($lis->visits as $v)
                    {{ $v->purposes['purpose'] }},
                    @endforeach
                    @endif
                </td>
                <!-- <td>{{ app('App\Utilities\Helpers')->convertDate($lis['date']) }}</td> -->
                <td>{{ $lis['start_time'] }} To {{ $lis['end_time'] }}
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
                    <span style="color: #28a745; font-weight: bold;">{{ ucfirst($lis['status']) }}</span>
                    @elseif (strtolower($lis['status']) === 'unlisted')
                    <span style="color: #e4dd12; font-weight: bold;">{{ ucfirst($lis['status']) }}</span>
                    @else
                    <span style="color: #dc3545; font-weight: bold;">{{ ucfirst($lis['status']) }}</span>
                    @endif
                </td>
                <td>{{ $lis['remarks'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @php
    $dealerExpenseVisits = $report->visits->filter(function ($visit) use ($report) {
    return $report->dealerExpenses->where('dealer_code', $visit->dealer_code)->isNotEmpty();
    });
    @endphp
    @if($dealerExpenseVisits->isNotEmpty())
    <div class="section-title-small">DEALER EXPENSES</div>
    <table class="data-table">
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
            @foreach($dealerExpenseVisits as $visit)
            <tr>
                <td>
                    @if($visit->visit_type == 'unlisted')
                    {{ $visit->unlisted_dealer_name }} (Unlisted)
                    @else
                    {{ $visit->dealer->name ?? $visit->dealer_code }}
                    @endif
                </td>
                @php
                $visitExpenses = $report->dealerExpenses->where('dealer_code', $visit->dealer_code);
                $visitAmount = $visitExpenses->sum('amount');
                $visitTypes = $visitExpenses->pluck('expense_type')->filter()->unique()->values()->all();
                $imageNames = $visitExpenses->flatMap(function($exp) {
                return $exp->images->map(function($img) {
                return basename($img->receipt_image_path);
                });
                })->values()->all();
                @endphp
                <td>{{ !empty($visitTypes) ? implode(', ', $visitTypes) : '-' }}</td>
                <td>{{ $visit->is_physical_visit === null ? 'N/A' : ($visit->is_physical_visit ? 'Yes' : 'No') }}</td>
                <td>Rs. {{ number_format($visitAmount, 2) }}</td>
                <td>
                    @php
                    $expenseAuthorizers = $visitExpenses->pluck('expenseAuthorizedUser.name')->filter()->unique()->values()->all();
                    @endphp
                    {{ !empty($expenseAuthorizers) ? implode(', ', $expenseAuthorizers) : '-' }}
                </td>
                <td>{{ $visit->remarks }}</td>
                <td>{{ !empty($imageNames) ? 'Available' : 'Not Available' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($report->otherExpenses && $report->otherExpenses->count() > 0)
    <div class="section-title-small">USER EXPENSES</div>
    <table class="data-table">
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
            @php
            $otherImageNames = $exp->images->map(function($img) {
            return basename($img->receipt_image_path);
            })->values()->all();
            @endphp
            <tr>
                <td>{{ $exp->expense_details }}</td>
                <td>Rs. {{ number_format($exp->amount, 2) }}</td>
                <td>{{ $exp->expenseAuthorizedUser->name ?? '-' }}</td>
                <td>{{ $exp->remarks }}</td>
                <td>{{ !empty($otherImageNames) ? 'Available' : 'Not Available' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="data-table">
        <tbody>
            @php
            $visitTotal = $report->dealerExpenses->sum('amount');
            $otherTotal = $report->otherExpenses->sum('amount');
            $grandTotal = $visitTotal + $otherTotal;
            @endphp
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="3" style="text-align: right;">Total Party Visit Expenses</td>
                <td>Rs. {{ number_format($visitTotal, 2) }}</td>
            </tr>
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="3" style="text-align: right;">Total User Expenses</td>
                <td>Rs. {{ number_format($otherTotal, 2) }}</td>
            </tr>
            <tr style="font-weight: bold; background: #eee; font-size: 1.1em;">
                <td colspan="3" style="text-align: right;">GRAND TOTAL</td>
                <td>Rs. {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">

        <div class="signature-box sig-right" style="border-top: none; text-align: left;">
            _________________________<br>
            <br>
            Name:- ......................................<br><br>
            Designation:- ...........................
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
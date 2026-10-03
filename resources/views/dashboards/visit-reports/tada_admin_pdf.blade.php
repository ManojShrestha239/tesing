<!DOCTYPE html>
<html>

<head>
    <title>Sales Officers Daily Report</title>
    <style>
        @page {
            size: landscape;
            margin: 5mm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
        }

        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }

        .date-line {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #555;
            padding: 2px 3px;
            vertical-align: top;
            text-align: left;
        }

        table.data-table th {
            background-color: #d9d9d9;
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        .rs {
            text-align: center;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 7px;
            color: #fff;
        }

        .status-online {
            background-color: #28a745;
        }

        .status-unlisted {
            background-color: #e4dd12;
            color: #333;
        }

        .status-offline {
            background-color: #dc3545;
        }

        tr.group-separator td {
            border-top: 2px solid #000 !important;
        }

        .no-data {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="report-title">Sales Officers Daily Report</div>
    <div class="date-line">Report Period: {{ $dateLabel }}</div>

    @if(isset($list) && count($list) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:3%;">SN</th>
                <th style="width:7%;">Date</th>
                <th style="width:8%;">Mobile User</th>
                <th style="width:11%;">Dealer Name</th>
                <th style="width:8%;">Visit Status</th>
                <th style="width:11%;">Expenses</th>
                <th style="width:8%;">Expenses<br>Authorized By</th>
                <th style="width:9%;">Remarks</th>
                <th style="width:7%;">Route</th>
                <th style="width:7%;">Route<br>Approved By</th>
                <th style="width:5%;">Start<br>KM</th>
                <th style="width:5%;">End<br>KM</th>
                <th style="width:5%;">Distance</th>
                <th style="width:4%;">TADA<br>Type</th>
                <th style="width:4%;">Combined<br>Visit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $reportIndex => $lis)
            @php
            $reportVisits = ($lis->preview_visits ?? $lis->visits)
            ->reject(fn ($row) => !empty($row->is_expense_only))
            ->values();
            $reportExpenses = $lis->dealerExpenses->concat($lis->otherExpenses)->values();

            $visitCount = $reportVisits->count();
            $expenseCount = $reportExpenses->count();
            $rowspan = max(1, $visitCount, $expenseCount);

            $visitMap = [];
            if ($visitCount > 0) {
            $baseSpan = intdiv($rowspan, $visitCount);
            $remainder = $rowspan % $visitCount;
            $currentRow = 0;
            for ($i = 0; $i < $visitCount; $i++) {
                $span=$baseSpan + ($i < $remainder ? 1 : 0);
                $visitMap[$currentRow]=[ 'visit'=> $reportVisits[$i],
                'rowspan' => $span
                ];
                $currentRow += $span;
                }
                }

                $expenseMap = [];
                if ($expenseCount > 0) {
                $baseSpan = intdiv($rowspan, $expenseCount);
                $remainder = $rowspan % $expenseCount;
                $currentRow = 0;
                for ($i = 0; $i < $expenseCount; $i++) {
                    $span=$baseSpan + ($i < $remainder ? 1 : 0);
                    $expenseMap[$currentRow]=[ 'expense'=> $reportExpenses[$i],
                    'rowspan' => $span
                    ];
                    $currentRow += $span;
                    }
                    }

                    $authorizedUser = $lis->authorizedUser->name ?? '-';
                    $distance = max(0, ($lis->ending_km ?? 0) - ($lis->starting_km ?? 0));
                    $combinedWith = trim((string) ($lis->combined_with ?? ''));
                    $isCombinedVisit = filter_var($lis->is_combined_visit, FILTER_VALIDATE_BOOLEAN)
                    && strcasecmp($combinedWith, 'alone') !== 0;
                    @endphp

                    @for ($rowIndex = 0; $rowIndex < $rowspan; $rowIndex++)
                        <tr class="{{ ($rowIndex === 0 && $reportIndex > 0) ? 'group-separator' : '' }}">
                        @if($rowIndex === 0)
                        <td rowspan="{{ $rowspan }}" class="rs">{{ $reportIndex + 1 }}</td>
                        <td rowspan="{{ $rowspan }}" class="rs">{{ $lis->entry_date_bs ?? $lis->entry_date ?? '-' }}</td>
                        <td rowspan="{{ $rowspan }}" class="rs">
                            {{ $lis->user['name'] ?? 'N/A' }}
                        </td>
                        @endif

                        {{-- Visit columns --}}
                        @if($visitCount === 0)
                        @if($rowIndex === 0)
                        <td rowspan="{{ $rowspan }}"></td>
                        <td rowspan="{{ $rowspan }}"></td>
                        @endif
                        @elseif(isset($visitMap[$rowIndex]))
                        @php
                        $vData = $visitMap[$rowIndex];
                        $visit = $vData['visit'];
                        $visitRowspan = $vData['rowspan'];
                        $dealerName = empty($visit->is_expense_only)
                        ? ((strtolower((string) ($visit->visit_type ?? '')) === 'unlisted' || !empty($visit->unlisted_dealer_name))
                        ? (($visit->unlisted_dealer_name ?? $visit->dealer_name ?? 'Unlisted') . ' (Unlisted)')
                        : ($visit->dealer->name ?? $visit->dealer_name ?? $visit->dealer_code ?? 'N/A'))
                        : '';
                        $rawStatus = $visit ? ($visit->status ?? $visit->visit_type ?? '') : '';
                        $badgeClass = 'status-offline';
                        if (strtolower($rawStatus) === 'online') {
                        $badgeClass = 'status-online';
                        } elseif (strtolower($rawStatus) === 'unlisted') {
                        $badgeClass = 'status-unlisted';
                        }
                        $physicalInfo = '';
                        if (strtolower(trim((string) $rawStatus)) === 'online') {
                        $physicalInfo = ' (present)';
                        } elseif (strtolower(trim((string) $rawStatus)) === 'unlisted') {
                        $physicalInfo = $visit && filter_var($visit->is_physical_visit, FILTER_VALIDATE_BOOLEAN)
                        ? ' (present)'
                        : ' (remote)';
                        } elseif ($visit && !empty($visit->is_physical_visit)) {
                        $physicalInfo = ' (present)';
                        } elseif ($visit && $visit->is_physical_visit === false) {
                        $physicalInfo = ' (remote)';
                        } elseif ($visit) {
                        $physicalInfo = ' (N/A)';
                        }
                        @endphp
                        <td rowspan="{{ $visitRowspan }}">{{ $dealerName }}</td>
                        <td rowspan="{{ $visitRowspan }}">
                            @if($rawStatus)
                            <span class="status-badge {{ $badgeClass }}">{{ $rawStatus }}</span>
                            @endif
                            {{ $physicalInfo }}
                        </td>
                        @endif

                        {{-- Expense columns (Expenses, Expenses Authorized By, Remarks) --}}
                        @if($expenseCount === 0)
                        @if($rowIndex === 0)
                        @php
                        $allVisitRemarks = $reportVisits->pluck('remarks')->filter()->implode(', ');
                        @endphp
                        <td rowspan="{{ $rowspan }}"></td>
                        <td rowspan="{{ $rowspan }}"></td>
                        <td rowspan="{{ $rowspan }}">{{ $allVisitRemarks }}</td>
                        @endif
                        @elseif(isset($expenseMap[$rowIndex]))
                        @php
                        $eData = $expenseMap[$rowIndex];
                        $expense = $eData['expense'];
                        $expenseRowspan = $eData['rowspan'];
                        $expenseLabel = '';
                        $expenseAuthorizedBy = '';
                        $expenseRemark = '';
                        if ($expense) {
                        if ($expense instanceof \App\Models\TadaDealerExpense) {
                        $expenseDealerName = $expense->dealer?->name ?? $expense->dealer_code;
                        $expenseLabel = ($expense->expense_type ?: 'Dealer Expense') . ' (' . $expenseDealerName . ')';
                        } else {
                        $expenseLabel = $expense->expense_details ?: 'Other Expense';
                        }
                        $expenseAuthorizedBy = $expense->expenseAuthorizedUser?->name ?? '-';
                        $expenseRemark = $expense->remarks ?: '';
                        }
                        @endphp
                        <td rowspan="{{ $expenseRowspan }}">{{ $expenseLabel }}</td>
                        <td rowspan="{{ $expenseRowspan }}">{{ $expenseAuthorizedBy }}</td>
                        <td rowspan="{{ $expenseRowspan }}">{{ $expenseRemark }}</td>
                        @endif

                        {{-- Report level columns --}}
                        @if($rowIndex === 0)
                        <td rowspan="{{ $rowspan }}">{{ $lis->route }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $authorizedUser }}</td>
                        <td rowspan="{{ $rowspan }}" class="rs">
                            {{ number_format($lis->starting_km ?? 0, 2) }}
                        </td>
                        <td rowspan="{{ $rowspan }}" class="rs">
                            {{ number_format($lis->ending_km ?? 0, 2) }}
                        </td>
                        <td rowspan="{{ $rowspan }}" class="rs">{{ number_format($distance, 2) }}</td>
                        <td rowspan="{{ $rowspan }}" class="rs">{{ $lis->visit_category }}</td>
                        <td rowspan="{{ $rowspan }}" class="rs">
                            {{ $isCombinedVisit ? 'yes' . (!empty($combinedWith) ? ' (with ' . $combinedWith . ')' : '') : 'no' }}
                        </td>
                        @endif
                        </tr>
                        @endfor
                        @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">No data found for the selected period.</div>
    @endif

</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Expense Report</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 10px;
        }

        h1 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 18px;
        }

        .meta {
            margin-bottom: 14px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #e5e7eb;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #9ca3af;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .total {
            margin-top: 12px;
            text-align: right;
            font-weight: bold;
        }

        .empty {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Daily Expense Report</h1>
    <div class="meta">
        @if($startDate || $endDate)
        Report Period: {{ $startDate ?: 'All' }} to {{ $endDate ?: 'All' }}
        @else
        Report Period: All Dates
        @endif
    </div>

    @if(count($rows) > 0)
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Officer</th>
                <th>Expense Type</th>
                <th class="amount">Amount</th>
                <th>Expense Authorized By</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                <td>{{ $row[1] ?: '-' }}</td>
                <td>{{ $row[2] }}</td>
                <td class="amount">Rs. {{ number_format((float) $row[3], 2) }}</td>
                <td>{{ $row[4] ?: '-' }}</td>
                <td>{{ $row[5] ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total">Total Expenses: Rs. {{ number_format($totalExpensesAmount, 2) }}</div>
    @else
    <div class="empty">No expenses found for the selected filters.</div>
    @endif
</body>

</html>
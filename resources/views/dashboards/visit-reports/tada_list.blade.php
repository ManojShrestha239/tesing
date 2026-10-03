@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Daily Report History</h1>
            <!-- Add TADA Report button disabled per request. Commented out to prevent dashboard-based creation. -->
            <!-- <a href="#" class="btn btn-danger pull-right" style="margin-top: 10px;">+ Add TADA Report</a> -->
            <!-- Sidebar Toggle Button -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
            <!-- Sidebar Toggle Button -->
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Officer</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            @include('dashboards.dynamicNav.report')

                        </nav>
                    </div>
                </div>

                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <div class="page-main-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-search font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Filter</span>
                                    </div>
                                    <div class="tools"></div>
                                </div>
                                <div class="portlet-body">
                                    <form id="tadaFilterForm" method="get" action="{{ route('tada-report.index') }}"
                                        novalidate>
                                        <div class="row">

                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="control-label bolds">Officer</label>
                                                    <select class="form-control selectpicker" data-live-search="true"
                                                        data-live-search-style="startsWith" name="user[]" id="user"
                                                        multiple>
                                                        <option value="">select officer</option>
                                                        @foreach ($users as $use)
                                                            <option value="{{ $use->id }}"
                                                                @if (isset($userid) && $userid == $use->id) selected
                                                        @elseif (old('user') == $use->id) selected @endif>
                                                                {{ $use->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="control-label bolds">Status</label>
                                                    <select class="form-control selectpicker" name="status"
                                                        id="statusFilterSelect">
                                                        <option value="">All Status</option>
                                                        <option value="PENDING"
                                                            @if (old('status', $selectedStatus ?? '') == 'PENDING') selected @endif>Pending
                                                        </option>
                                                        <option value="INREVIEW"
                                                            @if (old('status', $selectedStatus ?? '') == 'INREVIEW') selected @endif>In Review
                                                        </option>
                                                        <option value="APPROVED"
                                                            @if (old('status', $selectedStatus ?? '') == 'APPROVED') selected @endif>Approved
                                                        </option>
                                                        <option value="REJECTED"
                                                            @if (old('status', $selectedStatus ?? '') == 'REJECTED') selected @endif>Rejected
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="control-label bolds">From<span
                                                            class="required">*</span></label>
                                                    <input type="text" id="nepaliDateF" name="start-date"
                                                        autocomplete="off" placeholder="YYYY-MM-DD"
                                                        value="{{ $from ?? old('start-date') }}"
                                                        class="form-control nepali-calendar" required>
                                                    @if ($errors->has('start-date'))
                                                        <span
                                                            class="help-block required"><strong>{{ $errors->first('start-date') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="control-label bolds">To<span
                                                            class="required">*</span></label>
                                                    <input type="text" id="nepaliDateFa" name="end-date"
                                                        autocomplete="off" value="{{ $to ?? old('end-date') }}"
                                                        placeholder="YYYY-MM-DD" class="form-control nepali-calendar"
                                                        required>
                                                    <span class="help-block required text-danger" id="dateRangeError"
                                                        style="display:none;"></span>
                                                    @if ($errors->has('end-date'))
                                                        <span
                                                            class="help-block required"><strong>{{ $errors->first('end-date') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary btn-block"
                                                        style="margin-top:24px;">View</button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                    <strong>Success!</strong> {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                    <strong>Error!</strong> {{ session('error') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-globe font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Total Reports - @if (!empty($list))
                                                {{ count($list) }}
                                            @endif
                                    </div>


                                    <div class="uptable">
                                        <div class="pull-right" style="margin-top:8px;">
                                            <form id="exportExcelForm" action="{{ route('tada-report.export') }}"
                                                method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="user" id="exportUser">
                                                <input type="hidden" name="start-date" id="exportStartDate">
                                                <input type="hidden" name="end-date" id="exportEndDate">
                                                <button type="submit" class="btn btn-primary btn-sm"><i
                                                        class="fa fa-download"></i> Export Excel</button>
                                            </form>
                                            <form id="adminExportForm" action="{{ route('tada-report.admin-export') }}"
                                                method="post" style="display:inline; margin-left: 6px;">
                                                @csrf
                                                <input type="hidden" name="user" id="adminExportUser">
                                                <input type="hidden" name="status" id="adminExportStatus">
                                                <input type="hidden" name="start-date" id="adminExportStartDate">
                                                <input type="hidden" name="end-date" id="adminExportEndDate">
                                                <button type="submit" class="btn btn-primary btn-sm"><i
                                                        class="fa fa-download"></i> Download Daily Report</button>
                                            </form>
                                        </div>
                                        <div class="row">
                                            @if (!empty($amt))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Total Collection</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.{{ number_format($amt, 2, '.', ',') }}</td>
                                                    </table>
                                                </div>
                                            @endif


                                            @if (!empty($discount))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Total Discount</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.{{ number_format($discount, 2, '.', ',') }}</td>
                                                    </table>
                                                </div>
                                            @endif


                                            @if (!empty($discount))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Total TDS</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.{{ number_format(($discount * 15) / 100, 2, '.', ',') }}
                                                            </td>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row">
                                            @if (!empty($cashamt))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cash Collection</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.{{ number_format($cashamt, 2, '.', ',') }}</td>
                                                    </table>
                                                </div>
                                            @endif


                                            @if (!empty($discountcash))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cash Discount</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.{{ number_format($discountcash, 2, '.', ',') }}</td>
                                                    </table>
                                                </div>
                                            @endif

                                            @if (!empty($discountcash))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cash TDS</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.{{ number_format(($discountcash * 15) / 100, 2, '.', ',') }}
                                                            </td>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row">
                                            @if (!empty($chequeamt))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cheque Collection</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs. {{ number_format($chequeamt, 2, '.', ',') }}</td>
                                                    </table>
                                                </div>
                                            @endif

                                            @if (!empty($discountcheque))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cheque Discount</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs. {{ number_format($discountcheque, 2, '.', ',') }} </td>
                                                    </table>
                                                </div>
                                            @endif

                                            @if (!empty($discountcheque))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cheque TDS</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs.
                                                                {{ number_format(($discountcheque * 15) / 100, 2, '.', ',') }}
                                                            </td>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row">
                                            @if (!empty($tokenamt))
                                                <div class="column">
                                                    <table class="up">
                                                        <tr>
                                                            <th>Cheque Collection</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Rs. {{ number_format($tokenamt, 2, '.', ',') }}</td>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="portlet-body tada-report-table-body">
                                        <style>
                                            /* ---- Portlet body: full width, no clipping ---- */
                                            .tada-report-table-body {
                                                width: 100%;
                                                min-width: 0;
                                                overflow: visible;
                                                /* was hidden — that's what clipped the table */
                                            }

                                            /* ---- DataTables wrapper: don't clip, don't add scroll ---- */
                                            .tada-report-table-body .dataTables_wrapper {
                                                width: 100%;
                                                max-width: 100%;
                                                min-width: 0;
                                                overflow: visible;
                                                /* was hidden — removed */
                                            }

                                            /* DataTables inserts its own .row for controls — kill the negative margins */
                                            .tada-report-table-body .dataTables_wrapper>.row {
                                                margin-left: 0;
                                                margin-right: 0;
                                            }

                                            /* ---- This is the actual scroll container ---- */
                                            .tada-report-table-body .table-responsive {
                                                width: 100%;
                                                overflow-x: auto;
                                                overflow-y: visible;
                                                -webkit-overflow-scrolling: touch;
                                            }

                                            /* ---- The table ---- */
                                            #sample_2 {
                                                table-layout: auto;
                                                width: 100%;
                                                min-width: 1400px;
                                                margin-bottom: 0;
                                            }

                                            #sample_2 th,
                                            #sample_2 td {
                                                vertical-align: top;
                                                white-space: normal;
                                                word-wrap: break-word;
                                                overflow-wrap: break-word;
                                                line-height: 1.4;
                                                font-size: 13px;
                                                padding: 8px 6px;
                                            }

                                            #sample_2 thead th {
                                                background: #f8fafc;
                                                font-size: 12px;
                                                font-weight: 700;
                                                text-transform: uppercase;
                                                letter-spacing: 0.3px;
                                                color: #4b5563;
                                                white-space: nowrap;
                                            }

                                            #sample_2 tbody tr:hover>td {
                                                background: #f9fafb !important;
                                            }

                                            #sample_2 tr.report-group-hover>td {
                                                background: #eef5fb !important;
                                            }

                                            /* ---- Action cell ---- */
                                            #sample_2 td:last-child {
                                                min-width: 240px;
                                                width: 240px;
                                            }

                                            #sample_2 .actions-cell {
                                                display: flex;
                                                flex-direction: row;
                                                flex-wrap: nowrap;
                                                justify-content: center;
                                                align-items: center;
                                                gap: 4px;
                                                white-space: nowrap;
                                                width: max-content;
                                                margin: 0 auto;
                                            }

                                            #sample_2 .actions-cell .btn,
                                            #sample_2 .actions-cell form,
                                            #sample_2 .actions-cell form.area-action-form {
                                                display: inline-flex;
                                                align-items: center;
                                                margin: 0;
                                                padding: 0;
                                            }

                                            #sample_2 .actions-cell form button {
                                                margin: 0;
                                            }
                                        </style>
                                        <div class="table-responsive tada-report-table-container">
                                            <table class="table table-striped table-bordered table-hover" id="sample_2">
                                                <thead>
                                                    <tr>
                                                        <th>SN</th>
                                                        <th>Date</th>
                                                        <th>Users</th>
                                                        <th>Dealer Name</th>
                                                        <th>Visit Status</th>
                                                        <th>Expenses</th>
                                                        <th>Remarks</th>
                                                        <th>Route</th>
                                                        <th>Route Approved By</th>
                                                        <th>Start KM</th>
                                                        <th>End KM</th>
                                                        <th>Distance</th>
                                                        <th>Expense Visit Type</th>
                                                        <th>Combined Visit</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (!empty($list))
                                                        @foreach ($list as $reportIndex => $lis)
                                                            @php
                                                                $reportVisits = ($lis->preview_visits ?? $lis->visits)
                                                                    ->reject(fn($row) => !empty($row->is_expense_only))
                                                                    ->values();
                                                                $reportExpenses = $lis->dealerExpenses
                                                                    ->concat($lis->otherExpenses)
                                                                    ->values();

                                                                $visitCount = $reportVisits->count();
                                                                $expenseCount = $reportExpenses->count();
                                                                $rowspan = max(1, $visitCount, $expenseCount);

                                                                $visitMap = [];
                                                                if ($visitCount > 0) {
                                                                    $baseSpan = intdiv($rowspan, $visitCount);
                                                                    $remainder = $rowspan % $visitCount;
                                                                    $currentRow = 0;
                                                                    for ($i = 0; $i < $visitCount; $i++) {
                                                                        $span = $baseSpan + ($i < $remainder ? 1 : 0);
                                                                        $visitMap[$currentRow] = [
                                                                            'visit' => $reportVisits[$i],
                                                                            'rowspan' => $span,
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
                                                                        $span = $baseSpan + ($i < $remainder ? 1 : 0);
                                                                        $expenseMap[$currentRow] = [
                                                                            'expense' => $reportExpenses[$i],
                                                                            'rowspan' => $span,
                                                                        ];
                                                                        $currentRow += $span;
                                                                    }
                                                                }

                                                                $authorizedUser = $lis->authorizedUser->name ?? '-';
                                                                $distance = max(
                                                                    0,
                                                                    ($lis->ending_km ?? 0) - ($lis->starting_km ?? 0),
                                                                );
                                                                $combinedWith = trim(
                                                                    (string) ($lis->combined_with ?? ''),
                                                                );
                                                                $isCombinedVisit =
                                                                    filter_var(
                                                                        $lis->is_combined_visit,
                                                                        FILTER_VALIDATE_BOOLEAN,
                                                                    ) && strcasecmp($combinedWith, 'alone') !== 0;
                                                            @endphp
                                                            @for ($rowIndex = 0; $rowIndex < $rowspan; $rowIndex++)
                                                                <tr data-report-id="{{ $lis->id }}">
                                                                    @if ($rowIndex === 0)
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $reportIndex + 1 }}</td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $lis->entry_date_bs ?? ($lis->entry_date ?? '-') }}
                                                                        </td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $lis->user['name'] ?? 'N/A' }}
                                                                            @if ($lis->map_url)
                                                                                <br><a href="{{ $lis->map_url }}"
                                                                                    target="_blank" rel="noopener">Map</a>
                                                                            @endif
                                                                        </td>
                                                                    @endif

                                                                    @if ($visitCount === 0)
                                                                        @if ($rowIndex === 0)
                                                                            <td rowspan="{{ $rowspan }}"></td>
                                                                            <td rowspan="{{ $rowspan }}"></td>
                                                                        @endif
                                                                    @elseif(isset($visitMap[$rowIndex]))
                                                                        @php
                                                                            $vData = $visitMap[$rowIndex];
                                                                            $visit = $vData['visit'];
                                                                            $visitRowspan = $vData['rowspan'];
                                                                            $dealerName = empty($visit->is_expense_only)
                                                                                ? (strtolower(
                                                                                    (string) ($visit->visit_type ?? ''),
                                                                                ) === 'unlisted' ||
                                                                                !empty($visit->unlisted_dealer_name)
                                                                                    ? ($visit->unlisted_dealer_name ??
                                                                                            ($visit->dealer_name ??
                                                                                                'Unlisted')) .
                                                                                        ' (Unlisted)'
                                                                                    : $visit->dealer->name ??
                                                                                        ($visit->dealer_name ??
                                                                                            ($visit->dealer_code ??
                                                                                                'N/A')))
                                                                                : '';
                                                                            $rawStatus = $visit
                                                                                ? $visit->status ??
                                                                                    ($visit->visit_type ?? '')
                                                                                : '';
                                                                            $visitStatusHtml = '';
                                                                            if ($rawStatus !== '') {
                                                                                $statusLower = strtolower($rawStatus);
                                                                                if ($statusLower === 'online') {
                                                                                    $visitStatusHtml =
                                                                                        "<button class='btn btn-success btn-xs' style='background-color: #28a745 !important; border-color: #28a745 !important; color: #fff !important; pointer-events: none;'><strong>" .
                                                                                        e($rawStatus) .
                                                                                        '</strong></button>';
                                                                                } elseif ($statusLower === 'unlisted') {
                                                                                    $visitStatusHtml =
                                                                                        "<button class='btn btn-warning btn-xs' style='background-color: #e4dd12 !important; border-color: #e4dd12 !important; color: #565656 !important; pointer-events: none;'><strong>" .
                                                                                        e($rawStatus) .
                                                                                        '</strong></button>';
                                                                                } else {
                                                                                    $visitStatusHtml =
                                                                                        "<button class='btn btn-danger btn-xs' style='background-color: #dc3545 !important; border-color: #dc3545 !important; color: #fff !important; pointer-events: none;'><strong>" .
                                                                                        e($rawStatus) .
                                                                                        '</strong></button>';
                                                                                }
                                                                            }
                                                                            $physicalVisitValue =
                                                                                $visit?->is_physical_visit;
                                                                            if ($physicalVisitValue !== null) {
                                                                                $physicalVisitValue = filter_var(
                                                                                    $physicalVisitValue,
                                                                                    FILTER_VALIDATE_BOOLEAN,
                                                                                );
                                                                            }
                                                                            if ($physicalVisitValue !== null) {
                                                                                $visitStatusHtml .= $physicalVisitValue
                                                                                    ? ' (present)'
                                                                                    : ' (remote)';
                                                                            } elseif (
                                                                                strtolower(
                                                                                    trim((string) $rawStatus),
                                                                                ) === 'online'
                                                                            ) {
                                                                                $visitStatusHtml .= ' (present)';
                                                                            } elseif (
                                                                                strtolower(
                                                                                    trim((string) $rawStatus),
                                                                                ) === 'unlisted'
                                                                            ) {
                                                                                $visitStatusHtml .= ' (remote)';
                                                                            } elseif ($visit) {
                                                                                $visitStatusHtml .= ' (N/A)';
                                                                            }
                                                                        @endphp
                                                                        <td rowspan="{{ $visitRowspan }}">
                                                                            {{ $dealerName }}</td>
                                                                        <td rowspan="{{ $visitRowspan }}">
                                                                            {!! $visitStatusHtml !!}</td>
                                                                    @endif

                                                                    @if ($expenseCount === 0)
                                                                        @if ($rowIndex === 0)
                                                                            @php
                                                                                $allVisitRemarks = $reportVisits
                                                                                    ->pluck('remarks')
                                                                                    ->filter()
                                                                                    ->implode(', ');
                                                                            @endphp
                                                                            <td rowspan="{{ $rowspan }}"></td>
                                                                            <td rowspan="{{ $rowspan }}">
                                                                                {{ $allVisitRemarks }}</td>
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
                                                                                if (
                                                                                    $expense instanceof
                                                                                    \App\Models\TadaDealerExpense
                                                                                ) {
                                                                                    $expenseDealerName =
                                                                                        $expense->dealer?->name ??
                                                                                        $expense->dealer_code;
                                                                                    $expenseLabel =
                                                                                        ($expense->expense_type ?:
                                                                                            'Dealer Expense') .
                                                                                        ' (' .
                                                                                        $expenseDealerName .
                                                                                        ')';
                                                                                } else {
                                                                                    $expenseLabel =
                                                                                        $expense->expense_details ?:
                                                                                        'Other Expense';
                                                                                }
                                                                                $expenseAuthorizedBy =
                                                                                    $expense->expenseAuthorizedUser
                                                                                        ?->name;
                                                                                if ($expenseAuthorizedBy) {
                                                                                    $expenseLabel .=
                                                                                        ' [' .
                                                                                        $expenseAuthorizedBy .
                                                                                        ']';
                                                                                }
                                                                                $expenseLabel = e($expenseLabel);
                                                                                $expenseRemark = e(
                                                                                    $expense->remarks ?: '',
                                                                                );
                                                                            }
                                                                        @endphp
                                                                        <td rowspan="{{ $expenseRowspan }}">
                                                                            {!! $expenseLabel !!}</td>
                                                                        <td rowspan="{{ $expenseRowspan }}">
                                                                            {{ $expenseRemark }}</td>
                                                                    @endif

                                                                    @if ($rowIndex === 0)
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $lis->route }}</td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $authorizedUser }}</td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ number_format($lis->starting_km ?? 0, 2) }}
                                                                            @if ($lis->checkin_photo_url)
                                                                                <br><a
                                                                                    href="{{ $lis->checkin_photo_url }}"
                                                                                    target="_blank"
                                                                                    rel="noopener">Photo</a>
                                                                            @endif
                                                                        </td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ number_format($lis->ending_km ?? 0, 2) }}
                                                                            @if ($lis->checkout_photo_url)
                                                                                <br><a
                                                                                    href="{{ $lis->checkout_photo_url }}"
                                                                                    target="_blank"
                                                                                    rel="noopener">Photo</a>
                                                                            @endif
                                                                        </td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ number_format($distance, 2) }}
                                                                            <a href="#" class="edit-km-btn"
                                                                                data-id="{{ $lis->id }}"
                                                                                data-starting-km="{{ $lis->starting_km ?? '' }}"
                                                                                data-ending-km="{{ $lis->ending_km ?? '' }}"
                                                                                data-km-remarks="{{ $lis->km_remarks ?? '' }}"
                                                                                data-status="{{ $lis->status }}"
                                                                                title="Edit KM and KM Remarks"
                                                                                style="margin-left: 6px;">
                                                                                <i
                                                                                    class="fa fa-pencil font-blue-sharp"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $lis->visit_category }}</td>
                                                                        <td rowspan="{{ $rowspan }}">
                                                                            {{ $isCombinedVisit ? 'yes' : 'no' }}
                                                                            @if ($isCombinedVisit && $combinedWith)
                                                                                <br>with {{ $combinedWith }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center"
                                                                            rowspan="{{ $rowspan }}">
                                                                            <div class="actions-cell"
                                                                                style="display: flex; flex-direction: row; flex-wrap: nowrap; justify-content: center; align-items: center; gap: 4px; white-space: nowrap; width: max-content; margin: 0 auto;">
                                                                                @php
                                                                                    $statusClass = match (
                                                                                        $lis->status
                                                                                    ) {
                                                                                        'PENDING'
                                                                                            => 'btn btn-warning btn-xs',
                                                                                        'INREVIEW'
                                                                                            => 'btn btn-info btn-xs',
                                                                                        'APPROVED'
                                                                                            => 'btn btn-success btn-xs',
                                                                                        'REJECTED'
                                                                                            => 'btn btn-danger btn-xs',
                                                                                        default
                                                                                            => 'btn btn-default btn-xs',
                                                                                    };
                                                                                @endphp
                                                                                <button type="button"
                                                                                    class="{{ $statusClass }} status-review-btn"
                                                                                    data-id="{{ $lis->id }}"
                                                                                    data-status="{{ $lis->status }}"
                                                                                    data-remarks="{{ $lis->admin_remarks }}"
                                                                                    title="Change status">{{ $lis->status }}</button>
                                                                                <a href="{{ route('tada-report.show', $lis->id) }}"
                                                                                    class="btn btn-outline btn-primary btn-xs"
                                                                                    title="Open in full page"
                                                                                    style="margin: 0;"><i
                                                                                        class="fa fa-eye"></i> View</a>
                                                                                <a href="{{ route('tada-report.pdf', $lis->id) }}"
                                                                                    class="btn btn-primary btn-xs"
                                                                                    title="Download PDF"
                                                                                    style="margin: 0;"><i
                                                                                        class="fa fa-file-pdf-o"></i></a>
                                                                                <form
                                                                                    action="{{ route('tada-report.destroy', $lis->id) }}"
                                                                                    method="POST"
                                                                                    class="area-action-form"
                                                                                    onsubmit="return confirm('Are you sure you want to delete this report?');"
                                                                                    style="margin: 0; display: inline-block;">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary btn-xs"
                                                                                        title="Delete"
                                                                                        style="margin: 0;"><i
                                                                                            class="fa fa-trash"></i>
                                                                                        Delete</button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endfor
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                            </div>

                            <!-- END SIDEBAR CONTENT LAYOUT -->
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- TADA Details Modal -->
        <div class="modal" id="tadaDetailsModal" tabindex="-1" role="dialog" aria-hidden="true"
            style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="modal_content_body">
                    <div class="modal-body text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading...
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Images Modal -->
        <div class="modal" id="tadaExpenseImagesModal" tabindex="-1" role="dialog" aria-hidden="true"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 11050; overflow: auto;">
            <div class="modal-dialog modal-lg" role="document" style="z-index:11060; margin: 60px auto;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tadaExpenseImagesModalLabel">Expense Files</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="tadaExpenseImagesCarousel" class="carousel slide" data-ride="carousel"
                            data-interval="false">
                            <div class="carousel-inner"></div>
                            <a class="left carousel-control carousel-control-prev" href="#tadaExpenseImagesCarousel"
                                role="button" data-slide="prev" data-bs-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control carousel-control-next" href="#tadaExpenseImagesCarousel"
                                role="button" data-slide="next" data-bs-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <div class="no-files-message text-center text-muted" style="display:none; padding: 40px 0;">
                            No files available.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tadaKmModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" style="margin: 10% auto;">
                <div class="modal-content" style="box-shadow: 0 4px 16px rgba(0,0,0,0.18); border-radius: 4px;">
                    <form id="tadaKmForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" id="km_status">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Edit KM Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="startingKmInput">Starting KM</label>
                                <input type="number" step="0.01" name="starting_km" id="startingKmInput"
                                    class="form-control" placeholder="Starting KM">
                            </div>
                            <div class="form-group">
                                <label for="endingKmInput">Ending KM</label>
                                <input type="number" step="0.01" name="ending_km" id="endingKmInput"
                                    class="form-control" placeholder="Ending KM">
                            </div>
                            <div class="form-group">
                                <label for="remarksInput">KM Edit Remarks</label>
                                <textarea name="km_remarks" id="remarksInput" class="form-control" rows="3" placeholder="KM edit remarks"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tadaStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" style="margin: 10% auto;">
                <div class="modal-content" style="box-shadow: 0 4px 16px rgba(0,0,0,0.18); border-radius: 4px;">
                    <form id="tadaStatusForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Change TADA Status</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="statusSelect" class="form-control">
                                    <option value="PENDING">PENDING</option>
                                    <option value="INREVIEW">INREVIEW</option>
                                    <option value="APPROVED">APPROVED</option>
                                    <option value="REJECTED">REJECTED</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="admin_remarks">Review Remark</label>
                                <textarea name="admin_remarks" id="remarksTextarea" class="form-control" rows="3"
                                    placeholder="Add review comments or reason"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(document).ready(function() {
                    $('#tadaKmModal, #tadaStatusModal, #tadaDetailsModal, #tadaExpenseImagesModal').appendTo('body');

                    // ── Sync filter form values to export hidden fields before submission ──
                    function syncExportFields() {
                        var filterUser = $('#tadaFilterForm #user').val();
                        var filterStatus = $('#tadaFilterForm #statusFilterSelect').val();
                        var filterFrom = $('#tadaFilterForm #nepaliDateF').val();
                        var filterTo = $('#tadaFilterForm #nepaliDateFa').val();

                        // Export Excel
                        $('#exportUser').val(filterUser || '');
                        $('#exportStartDate').val(filterFrom || '');
                        $('#exportEndDate').val(filterTo || '');

                        // Admin Export PDF
                        $('#adminExportUser').val(filterUser || '');
                        $('#adminExportStatus').val(filterStatus || '');
                        $('#adminExportStartDate').val(filterFrom || '');
                        $('#adminExportEndDate').val(filterTo || '');
                    }

                    $('#exportExcelForm').on('submit', syncExportFields);
                    $('#adminExportForm').on('submit', syncExportFields);

                    function isImageUrl(url) {
                        return /\.(png|jpe?g|gif|webp|bmp|svg)(\?|$)/i.test(url);
                    }

                    function extractFileName(url) {
                        var parts = url.split('/');
                        var name = parts[parts.length - 1] || url;
                        return decodeURIComponent(name.split('?')[0]);
                    }

                    function buildExpenseImageCarousel(images) {
                        var inner = $('#tadaExpenseImagesCarousel .carousel-inner');
                        var prevControl = $('#tadaExpenseImagesCarousel .carousel-control-prev');
                        var nextControl = $('#tadaExpenseImagesCarousel .carousel-control-next');
                        var noFilesMessage = $('#tadaExpenseImagesModal .no-files-message');
                        inner.empty();
                        noFilesMessage.hide();

                        var imageUrls = images.filter(function(url) {
                            return isImageUrl(url);
                        });

                        if (!imageUrls.length) {
                            $('#tadaExpenseImagesCarousel').hide();
                            prevControl.hide();
                            nextControl.hide();
                            noFilesMessage.show();
                            return false;
                        }

                        $('#tadaExpenseImagesCarousel').show();
                        noFilesMessage.hide();

                        imageUrls.forEach(function(url, index) {
                            var itemClass = 'item carousel-item' + (index === 0 ? ' active' : '');
                            var item = $('<div>').addClass(itemClass + ' text-center');
                            item.append(
                                $('<img>').attr('src', url).addClass('expense-preview-img')
                            );
                            item.append($('<div>').addClass('text-center mt-3 small text-muted').text(
                                extractFileName(url)));
                            inner.append(item);
                        });

                        if (imageUrls.length > 1) {
                            prevControl.show();
                            nextControl.show();
                        } else {
                            prevControl.hide();
                            nextControl.hide();
                        }

                        $('#tadaExpenseImagesCarousel').carousel({
                            interval: false,
                            wrap: true
                        });
                        $('#tadaExpenseImagesCarousel').carousel(0);
                        return true;
                    }

                    function showModal(modal) {
                        if (!$('.modal-backdrop').length) {
                            $('<div class="modal-backdrop fade in"></div>').appendTo('body');
                        }
                        $('.modal-backdrop').addClass('show in');
                        modal.css({
                            'display': 'block',
                            'visibility': 'visible',
                            'opacity': '1',
                            'pointer-events': 'auto'
                        }).addClass('show in');
                        modal.attr('aria-hidden', 'false');
                        $('body').addClass('modal-open');
                    }

                    function hideModal(modal) {
                        modal.css({
                            'display': 'none',
                            'visibility': 'hidden',
                            'opacity': '0',
                            'pointer-events': 'none'
                        }).removeClass('show in');
                        modal.attr('aria-hidden', 'true');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }

                    $(document).on('click', '.view-expense-images-btn', function() {
                        var imagesRaw = $(this).attr('data-images') || '[]';
                        var images = [];
                        try {
                            images = JSON.parse(imagesRaw);
                        } catch (e) {
                            console.error('Invalid image list:', imagesRaw, e);
                            images = [];
                        }
                        buildExpenseImageCarousel(images);
                        showModal($('#tadaExpenseImagesModal'));
                    });

                    $(document).on('click',
                        '#tadaExpenseImagesModal [data-dismiss="modal"], #tadaExpenseImagesModal .close',
                        function() {
                            hideModal($('#tadaExpenseImagesModal'));
                        });

                    $(document).on('click', '.btn-view-report', function() {
                        var id = $(this).data('id');
                        $('#modal_content_body').html(
                            '<div class="modal-body text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading...</div>'
                        );
                        showModal($('#tadaDetailsModal'));

                        $.ajax({
                            url: "{{ url('tada-report') }}/" + id,
                            type: 'GET',
                            success: function(data) {
                                $('#modal_content_body').html(data);
                            },
                            error: function() {
                                $('#modal_content_body').html(
                                    '<div class="modal-body alert alert-danger">Error loading report details.</div>'
                                );
                            }
                        });
                    });

                    function clearDateRangeError() {
                        $('#dateRangeError').hide().text('');
                    }

                    function showDateRangeError(message) {
                        $('#dateRangeError').text(message).show();
                    }

                    function isValidDateInput(value) {
                        return /^\d{4}-\d{2}-\d{2}$/.test(value);
                    }

                    function validateDateRange() {
                        var fromDate = $('#nepaliDateF').val().trim();
                        var toDate = $('#nepaliDateFa').val().trim();

                        clearDateRangeError();

                        if (!fromDate || !toDate) {
                            showDateRangeError('Both From Date and To Date are required.');
                            return false;
                        }

                        if (!isValidDateInput(fromDate) || !isValidDateInput(toDate)) {
                            showDateRangeError('Dates must be in YYYY-MM-DD format.');
                            return false;
                        }

                        if (fromDate > toDate) {
                            showDateRangeError('To Date must be greater than or equal to From Date.');
                            return false;
                        }

                        return true;
                    }

                    $(document).on('submit', '#tadaFilterForm', function(e) {
                        if (!validateDateRange()) {
                            e.preventDefault();
                        }
                    });

                    $(document).on('change', '#statusFilterSelect', function() {
                        if (validateDateRange()) {
                            $('#tadaFilterForm')[0].submit();
                        }
                    });

                    $(document).on('click', '.status-review-btn', function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        var status = $(this).data('status');
                        var remarks = $(this).data('remarks') || '';

                        $('#statusSelect').val(status);
                        $('#remarksTextarea').val(remarks);
                        $('#tadaStatusForm').attr('action', "{{ url('tada-report') }}/" + id);

                        showModal($('#tadaStatusModal'));
                    });

                    $(document).on('click', '.edit-km-btn', function(e) {
                        e.preventDefault();

                        var id = $(this).data('id');
                        var startingKm = $(this).data('starting-km');
                        var endingKm = $(this).data('ending-km');
                        var kmRemarks = $(this).data('km-remarks') || '';
                        var status = $(this).data('status');

                        $('#startingKmInput').val(startingKm);
                        $('#endingKmInput').val(endingKm);
                        $('#remarksInput').val(kmRemarks);
                        $('#km_status').val(status);
                        $('#tadaKmForm').attr('action', "{{ url('tada-report') }}/" + id);

                        showModal($('#tadaKmModal'));
                    });

                    $(document).on('click', '[data-dismiss="modal"], .close', function() {
                        var modal = $(this).closest('.modal');
                        if (modal.length) {
                            hideModal(modal);
                        }
                    });

                    $(document).on('mouseenter', 'tr[data-report-id]', function() {
                        var reportId = $(this).attr('data-report-id');
                        if (reportId) {
                            $('tr[data-report-id="' + reportId + '"]').addClass('report-group-hover');
                        }
                    }).on('mouseleave', 'tr[data-report-id]', function() {
                        var reportId = $(this).attr('data-report-id');
                        if (reportId) {
                            $('tr[data-report-id="' + reportId + '"]').removeClass('report-group-hover');
                        }
                    });
                });
            </script>
            <style>
                #sample_2 tr.report-group-hover>td {
                    background-color: #eef5fb !important;
                }

                #tadaStatusModal,
                #tadaKmModal,
                #tadaDetailsModal,
                #tadaExpenseImagesModal {
                    display: none;
                    position: fixed;
                    inset: 0;
                    background: transparent;
                    /* use backdrop for dimming */
                    overflow: auto;
                    z-index: 99999 !important;
                }

                .modal-backdrop {
                    z-index: 99990 !important;
                }

                #tadaStatusModal .modal-dialog,
                #tadaKmModal .modal-dialog,
                #tadaDetailsModal .modal-dialog,
                #tadaExpenseImagesModal .modal-dialog {
                    margin: 24px auto;
                    max-height: calc(100vh - 48px);
                }

                body.modal-open {
                    overflow: hidden;
                }

                #tadaStatusModal .modal-content,
                #tadaKmModal .modal-content,
                #tadaDetailsModal .modal-content,
                #tadaExpenseImagesModal .modal-content {
                    background-color: #ffffff !important;
                }

                #tadaStatusModal .modal-content,
                #tadaKmModal .modal-content {
                    padding-bottom: 0;
                }

                #tadaStatusModal .modal-header,
                #tadaStatusModal .modal-footer {
                    padding: 12px 15px;
                }

                #tadaStatusModal .modal-body {
                    padding: 10px 15px;
                }

                .pdf-placeholder {
                    min-height: 60vh;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    text-align: center;
                    padding: 40px 20px;
                }

                .expense-preview-img {
                    max-height: 75vh;
                    max-width: 100%;
                    object-fit: contain;
                    display: block;
                    margin: 0 auto;
                }

                .carousel-inner .item,
                .carousel-inner .carousel-item {
                    min-height: 60vh;
                    align-items: center;
                    justify-content: center;
                }

                .carousel-inner .item.active,
                .carousel-inner .carousel-item.active {
                    display: flex !important;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                }

                #tadaExpenseImagesModal .no-files-message {
                    font-size: 1rem;
                    line-height: 1.5;
                }

                #tadaExpenseImagesModal .modal-body {
                    background-color: #ffffff !important;
                }

                #tadaExpenseImagesCarousel {
                    background-color: #ffffff !important;
                }

                #tadaExpenseImagesCarousel .carousel-inner {
                    background-color: #ffffff !important;
                }

                #tadaExpenseImagesCarousel .carousel-item,
                #tadaExpenseImagesCarousel .item {
                    background-color: #ffffff !important;
                }

                /* Carousel Navigation Controls Styles */
                #tadaExpenseImagesModal .carousel-control {
                    background: transparent !important;
                    background-image: none !important;
                    opacity: 0.5;
                    transition: opacity 0.2s ease-in-out;
                    width: 8% !important;
                }

                #tadaExpenseImagesModal .carousel-control:hover,
                #tadaExpenseImagesModal .carousel-control:focus {
                    opacity: 0.9;
                    background: transparent !important;
                    background-image: none !important;
                }

                #tadaExpenseImagesModal .carousel-control .glyphicon,
                #tadaExpenseImagesModal .carousel-control .carousel-control-prev-icon,
                #tadaExpenseImagesModal .carousel-control .carousel-control-next-icon {
                    color: #333333 !important;
                    text-shadow: none !important;
                }

                #tadaExpenseImagesModal .carousel-control-prev-icon,
                #tadaExpenseImagesModal .carousel-control-next-icon {
                    filter: invert(1) brightness(0.2) !important;
                }

                /* actions cell - reuse theme pattern */
                .actions-cell {
                    display: flex;
                    gap: 5px;
                    flex-wrap: wrap;
                    align-items: center;
                    justify-content: center;
                }

                .area-action-form {
                    display: inline !important;
                    margin: 0 !important;
                }

                /* Flex layout for sidebar + content - overrides framework table layout */
                .page-content-row {
                    display: flex !important;
                    gap: 0;
                    width: 100%;
                }

                .page-sidebar {
                    flex-shrink: 0;
                }

                .page-main-content {
                    flex: 1;
                    min-width: 0;
                    overflow: hidden;
                }

                /* Remove constraints from Bootstrap grid inside main content */
                .page-main-content .row {
                    margin-left: -15px !important;
                    margin-right: -15px !important;
                    width: calc(100% + 30px);
                }

                .page-main-content .col-md-12 {
                    padding-left: 15px !important;
                    padding-right: 15px !important;
                    width: 100%;
                }

                .page-main-content .portlet {
                    width: 100%;
                    box-sizing: border-box;
                }

                .page-main-content form {
                    width: 100%;
                }
            </style>
        @endpush
    @endsection

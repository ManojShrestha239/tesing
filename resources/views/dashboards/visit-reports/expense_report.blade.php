@extends('dashboards.sessions.layout')

@section('content')
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Expense Report</h1>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
            <span class="sr-only">Toggle navigation</span>
            <span class="toggle-icon"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></span>
        </button>
    </div>
    <div class="page-content-container">
        <div class="page-content-row">
            <div class="page-sidebar">
                <div class="portlet light portlet-fit">
                    <nav class="navbar" role="navigation">@include('dashboards.dynamicNav.report')</nav>
                </div>
            </div>
            <div class="page-content-col">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption"><span class="caption-subject font-red bold uppercase">Expense Report</span></div>
                        <div class="actions pull-right" style="margin-top: -5px;">
                            <div style="background-color: #f8f9fa; border: 1px solid #e1e5ec; border-radius: 4px; padding: 6px 16px; text-align: right; display: inline-block;">
                                <span style="font-size: 11px; font-weight: 600; color: #666; text-transform: uppercase; display: block; margin-bottom: 2px;">Total Expenses</span>
                                <span style="font-size: 16px; font-weight: 700; color: #e7505a;">Rs. {{ number_format($totalExpensesAmount ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form method="GET" action="{{ route('expense-report.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3"><label class="control-label">Officer</label>
                                    <select name="user[]" id="expenseOfficerFilter" class="form-control selectpicker" data-live-search="true" data-live-search-style="startsWith" multiple title="Select officer">
                                        @foreach($users as $user)<option value="{{ $user->id }}" {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>{{ $user->name }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-md-3"><label class="control-label">Expense Type</label>
                                    <select name="expense_type[]" id="expenseTypeFilter" class="form-control selectpicker" data-live-search="true" multiple title="Select expense type">
                                        @foreach($expenseTypeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ in_array($value, $selectedExpenseTypes ?? []) ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2"><label class="control-label">From Date</label><input type="text" id="nepaliDateF" name="start-date" autocomplete="off" placeholder="YYYY-MM-DD" value="{{ old('start-date', $displayStartDate) }}" class="form-control nepali-calendar"></div>
                                <div class="col-md-2"><label class="control-label">To Date</label><input type="text" id="nepaliDateFa" name="end-date" autocomplete="off" placeholder="YYYY-MM-DD" value="{{ old('end-date', $displayEndDate) }}" class="form-control nepali-calendar"></div>
                                <div class="col-md-2" style="padding-top:25px"><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button> <a href="{{ route('expense-report.index') }}" class="btn btn-default">Reset</a></div>
                            </div>
                        </form>
                        <div class="text-right" style="margin-top: 15px;">
                            <a href="{{ route('expense-report.export', request()->except('page')) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-file-pdf-o"></i> Download PDF
                            </a>
                        </div>
                        <div class="table-responsive" style="margin-top:25px">
                            <table class="table table-bordered table-hover" id="sample_2">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Date</th>
                                        <th>Officer</th>
                                        <th>Expense Type</th>
                                        <th>Amount
                                            <!-- <a href="{{ request()->fullUrlWithQuery(['amount_sort' => 'asc', 'page' => null]) }}" title="Sort amount ascending" class="{{ $amountSort === 'asc' ? 'font-red' : '' }}">&#9650;</a>
                                            <a href="{{ request()->fullUrlWithQuery(['amount_sort' => 'desc', 'page' => null]) }}" title="Sort amount descending" class="{{ $amountSort === 'desc' ? 'font-red' : '' }}">&#9660;</a> -->
                                        </th>
                                        <th>Authorized By</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>@forelse($expenses as $expense)<tr>
                                        <td>{{ $expenses->firstItem() + $loop->index }}</td>
                                        <td>{{ $expense->date_bs }}</td>
                                        <td>{{ $expense->officer }}</td>
                                        <td>{{ $expense->expense_type }}</td>
                                        <td>{{ number_format((float) $expense->amount, 2) }}</td>
                                        <td>{{ $expense->authorized_by }}</td>
                                        <td>{{ $expense->remarks }}</td>
                                    </tr>@empty<tr>
                                        <td colspan="7" class="text-center">No expenses found for the selected filters.</td>
                                    </tr>@endforelse</tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .expense-pagination .pagination {
        display: flex !important;
        width: 100%;
        justify-content: flex-end;
        margin: 20px 0 0;
    }
</style>
<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>
@endpush
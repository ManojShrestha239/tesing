@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Daily Report</h1>
            <a href="{{ route('tada-report.index') }}" class="btn btn-default pull-right"
                style="margin-top: -32px; border-radius: 20px !important;">Cancel</a>
        </div>

        <div class="page-content-container">
            <div class="page-content-row">
                <!-- BEGIN PAGE SIDEBAR -->
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Officer</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            @include('dashboards.dynamicNav.report')
                        </nav>
                    </div>
                </div>
                <!-- END PAGE SIDEBAR -->

                <!-- BEGIN PAGE CONTENT -->
                <div class="col-md-9" style="padding-left: 20px;">
                    <div class="portlet light bordered glass-card">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Daily Report Form</span>
                            </div>
                            <div class="actions">
                                <a href="{{ route('tada-report.index') }}" class="btn btn-circle btn-default btn-sm">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissable" style="margin: 20px;">
                                    <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <!-- Company Header -->
                            <div class="company-header text-center"
                                style="margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px;">
                                <h3 style="margin: 0; font-weight: bold; color: #333;">Jagannath Group of Industries Pvt.
                                    Ltd.</h3>
                                <p style="margin: 5px 0; font-size: 1.1em; color: #666;">Bharatpur 2, Chitwan</p>
                                <h2
                                    style="margin: 10px 0; font-weight: 800; color: #e7505a; letter-spacing: 5px; font-size: 2.5em;">
                                    EURO PAINTS</h2>
                                <!-- <h4 style="margin: 5px 0; font-style: italic; color: #444;">Daily Report of <span id="dynamic_staff_name">....................................</span></h4> -->
                            </div>

                            <!-- FORM START -->
                            <form action="{{ route('tada-report.store') }}" method="POST"
                                class="form-horizontal tada-form">
                                @csrf
                                <div class="form-body">
                                    <h4 class="form-section">1. Basic Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Sales Team Name</label>
                                                <div class="col-md-8">
                                                    <select name="user_id" class="form-control selectpicker"
                                                        data-live-search="true" required disabled>
                                                        <option value="">Select Staff</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Route</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="route" class="form-control"
                                                        placeholder="Enter planned route" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Visit Type</label>
                                                <div class="col-md-8">
                                                    <select name="visit_category" class="form-control" id="tada_type_select"
                                                        required>
                                                        <option value="Local">Local</option>
                                                        <option value="Up-country">Up-country</option>
                                                        <option value="Ex-up country">Ex-up country</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="hotel_field" style="display: none;">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Stay at</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="stay_at" class="form-control"
                                                        placeholder="Type Stay At">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Combined Visit?</label>
                                                <div class="col-md-8">
                                                    <div class="toggle-switch">
                                                        <label class="radio-inline"><input type="radio"
                                                                name="is_combined_visit" value="1"> Yes</label>
                                                        <label class="radio-inline"><input type="radio"
                                                                name="is_combined_visit" value="0" checked> No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="combined_field" style="display: none;">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Choose With (Name)</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="combined_with" class="form-control"
                                                        placeholder="Enter colleague name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="form-section">2. Odometer Reading (KM)</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Starting KM</label>
                                                <div class="col-md-8">
                                                    <input type="number" step="0.01" name="starting_km"
                                                        class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Ending KM</label>
                                                <div class="col-md-8">
                                                    <input type="number" step="0.01" name="ending_km"
                                                        class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row section-title-row">
                                        <div class="col-md-6">
                                            <h4 class="form-section">3. Dealer Name</h4>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="button" class="btn btn-sm btn-info add-row-btn"
                                                data-target="party_table">+ Add Party</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered dynamic-table" id="party_table">
                                            <thead>
                                                <tr>
                                                    <th>Dealer Name</th>
                                                    <th width="120">Physical Visit</th>
                                                    <th width="150">Amount (Expense)</th>
                                                    <th width="220">Expenses Authorized By</th>
                                                    <th>Purpose / Remarks</th>
                                                    <th width="50"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="dynamic-row">
                                                    <td>
                                                        <select name="visits[0][dealer_code]"
                                                            class="form-control selectpicker party-select"
                                                            data-live-search="true" required>
                                                            <option value="">Select Dealer</option>
                                                            @foreach ($dealers as $dealer)
                                                                <option value="{{ $dealer->code }}">{{ $dealer->name }}
                                                                    ({{ $dealer->code }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <select name="visits[0][is_physical_visit]"
                                                            class="form-control input-sm">
                                                            <option value="1">Yes</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="visits[0][amount]"
                                                            class="form-control input-sm" placeholder="0.00">
                                                    </td>
                                                    <td>
                                                        <select name="visits[0][expenses_authorized_by]"
                                                            class="form-control input-sm selectpicker"
                                                            data-live-search="true">
                                                            <option value="">Select Authorizer</option>
                                                            @foreach ($expenseAuthorizedUsers as $authUser)
                                                                <option value="{{ $authUser->id }}">{{ $authUser->name }}
                                                                    ({{ $authUser->designation }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="visits[0][remarks]"
                                                            class="form-control input-sm" placeholder="Purpose of visit">
                                                    </td>
                                                    <td><button type="button" class="btn btn-xs btn-danger remove-row"
                                                            title="Remove row">×</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row section-title-row">
                                        <div class="col-md-6">
                                            <h4 class="form-section">4. User Expenses</h4>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="button" class="btn btn-sm btn-info add-row-btn"
                                                data-target="expense_table">+ Add Expense</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered dynamic-table" id="expense_table">
                                            <thead>
                                                <tr>
                                                    <th>Expense Description</th>
                                                    <th width="200">Amount</th>
                                                    <th width="220">Expenses Authorized By</th>
                                                    <th width="50"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="dynamic-row">
                                                    <td>
                                                        <input type="text" name="expenses[0][expense_details]"
                                                            class="form-control input-sm"
                                                            placeholder="e.g. Fuel, Lunch, Stationery">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="expenses[0][amount]"
                                                            class="form-control input-sm" placeholder="0.00">
                                                    </td>
                                                    <td>
                                                        <select name="expenses[0][expenses_authorized_by]"
                                                            class="form-control input-sm selectpicker"
                                                            data-live-search="true">
                                                            <option value="">Select Authorizer</option>
                                                            @foreach ($expenseAuthorizedUsers as $authUser)
                                                                <option value="{{ $authUser->id }}">{{ $authUser->name }}
                                                                    ({{ $authUser->designation }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-xs btn-danger remove-row"
                                                            title="Remove row">×</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" style="margin-top: 20px;">
                                                <label class="control-label col-md-4 bold">Authorized By <span
                                                        style="color:red;">*</span></label>
                                                <div class="col-md-8">
                                                    <select name="authorized_user_id" class="form-control selectpicker"
                                                        data-live-search="true" required disabled>
                                                        <option value="">Select Authorized Personnel</option>
                                                        @foreach ($authorizedUsers as $authUser)
                                                            <option value="{{ $authUser->id }}">{{ $authUser->name }}
                                                                ({{ $authUser->designation }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions text-center"
                                    style="background: transparent; border-top: 1px solid rgba(255,255,255,0.1); padding: 20px;">
                                    <button type="submit" class="btn btn-danger btn-lg" style="width: 250px;">Save Daily
                                        Report</button>
                                    <a href="{{ route('tada-report.index') }}" class="btn btn-default btn-lg">Back to
                                        List</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- END PAGE CONTENT -->
            </div>
        </div>
    </div>

    <!-- Styles for Advanced Aesthetic -->
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            border-radius: 20px !important;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            max-width: 850px;
            margin: 0 auto;
        }

        .form-section {
            border-bottom: 2px solid #e7505a !important;
            padding-bottom: 10px;
            color: #333;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 40px 0 20px 0;
            font-size: 1.2em;
        }

        .form-horizontal .control-label {
            font-weight: 600;
            color: #555;
        }

        .dynamic-table th {
            background: #f9f9f9;
            color: #e7505a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .dynamic-row:hover {
            background: rgba(231, 80, 90, 0.05);
        }

        .add-row-btn {
            border-radius: 20px !important;
            background-color: #32c5d2;
            border-color: #32c5d2;
            font-weight: 600;
        }

        .add-row-btn:hover {
            background-color: #26a69a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .section-title-row {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        .section-title-row .form-section {
            margin-top: 0;
            width: 100%;
        }

        .form-control {
            border-radius: 8px !important;
            border: 1px solid #ccc;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #e7505a;
            box-shadow: 0 0 8px rgba(231, 80, 90, 0.2);
        }

        .btn-danger {
            border-radius: 50px !important;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-danger:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(231, 80, 90, 0.4);
        }
    </style>

    <!-- Dynamic Interactivity -->
    <!-- Templates for Dynamic Rows -->
    <script type="text/template" id="party-row-template">
    <tr class="dynamic-row">
            <td>
                <select name="visits[INDEX][dealer_code]" class="form-control selectpicker party-select" data-live-search="true" required>
                    <option value="">Select Dealer</option>
                    @foreach($dealers as $dealer)
                        <option value="{{ $dealer->code }}">{{ $dealer->name }} ({{ $dealer->code }})</option>
                    @endforeach
                </select>
            </td>
            <td class="text-center">
                <select name="visits[INDEX][is_physical_visit]" class="form-control input-sm">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="visits[INDEX][amount]" class="form-control input-sm" placeholder="0.00">
            </td>
            <td>
                <select name="visits[INDEX][expenses_authorized_by]" class="form-control input-sm selectpicker" data-live-search="true">
                    <option value="">Select Authorizer</option>
                    @foreach($expenseAuthorizedUsers as $authUser)
                    <option value="{{ $authUser->id }}">{{ $authUser->name }} ({{ $authUser->designation }})</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="visits[INDEX][remarks]" class="form-control input-sm" placeholder="Purpose of visit">
            </td>
            <td><button type="button" class="btn btn-xs btn-danger remove-row" title="Remove row">×</button></td>
        </tr>
    </script>

    <script type="text/template" id="expense-row-template">
    <tr class="dynamic-row">
            <td>
                <input type="text" name="expenses[INDEX][expense_details]" class="form-control input-sm" placeholder="e.g. Fuel, Lunch, Stationery">
            </td>
            <td>
                <input type="number" step="0.01" name="expenses[INDEX][amount]" class="form-control input-sm" placeholder="0.00">
            </td>
            <td>
                <select name="expenses[INDEX][expenses_authorized_by]" class="form-control input-sm selectpicker" data-live-search="true">
                    <option value="">Select Authorizer</option>
                    @foreach($expenseAuthorizedUsers as $authUser)
                    <option value="{{ $authUser->id }}">{{ $authUser->name }} ({{ $authUser->designation }})</option>
                    @endforeach
                </select>
            </td>
            <td><button type="button" class="btn btn-xs btn-danger remove-row" title="Remove row">×</button></td>
        </tr>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dynamic Name Header
            const staffSelect = document.querySelector('select[name="user_id"]');
            const dynamicName = document.getElementById('dynamic_staff_name');

            $(staffSelect).on('change', function() {
                const name = $(this).find('option:selected').text();
                if (name && name !== 'Select Staff') {
                    dynamicName.textContent = name;
                } else {
                    dynamicName.textContent = '....................................';
                }
            });

            // TADA type toggle
            const tadaSelect = document.getElementById('tada_type_select');
            const hotelField = document.getElementById('hotel_field');

            tadaSelect.addEventListener('change', function() {
                if (this.value === 'Up-country' || this.value === 'Ex-up country') {
                    $(hotelField).fadeIn();
                } else {
                    $(hotelField).fadeOut();
                }
            });

            // Combined visit toggle
            const combinedRadios = document.querySelectorAll('input[name="is_combined_visit"]');
            const combinedField = document.getElementById('combined_field');

            combinedRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === '1') {
                        $(combinedField).fadeIn();
                    } else {
                        $(combinedField).fadeOut();
                    }
                });
            });

            // Function to update available parties across all dropdowns
            function updateAvailableParties() {
                const selects = document.querySelectorAll('.party-select');
                const selectedValues = Array.from(selects).map(s => s.value).filter(v => v !== "");

                selects.forEach(select => {
                    const currentValue = select.value;
                    const options = select.querySelectorAll('option');
                    let changed = false;

                    options.forEach(option => {
                        if (option.value !== "") {
                            const shouldBeDisabled = (option.value !== currentValue &&
                                selectedValues.includes(option.value));
                            if ($(option).prop('disabled') !== shouldBeDisabled) {
                                $(option).prop('disabled', shouldBeDisabled);
                                changed = true;
                            }
                        }
                    });

                    if (changed) {
                        $(select).selectpicker('refresh');
                    }
                });
            }

            // Initial call
            updateAvailableParties();

            // Handle change event for party selects
            $(document).on('change', '.party-select', function() {
                updateAvailableParties();
            });

            // Row Cloning Logic using Templates
            let partyIndex = 1;
            let expenseIndex = 1;

            document.querySelectorAll('.add-row-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const templateId = (targetId === 'party_table') ? 'party-row-template' :
                        'expense-row-template';
                    const template = document.getElementById(templateId).innerHTML;

                    const index = (targetId === 'party_table') ? partyIndex++ : expenseIndex++;
                    const newRowHtml = template.replace(/INDEX/g, index);

                    const table = document.getElementById(targetId);
                    const tbody = table.querySelector('tbody');

                    // Create element from HTML
                    const tr = document.createElement('tr');
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = '<table>' + newRowHtml + '</table>';
                    const newRow = tempDiv.querySelector('tr');

                    tbody.appendChild(newRow);

                    // Initialize Selectpicker for new row
                    if (targetId === 'party_table') {
                        $(newRow).find('.selectpicker').selectpicker();
                        updateAvailableParties();
                    }
                });
            });

            // Remove Row logic
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-row')) {
                    const row = e.target.closest('tr');
                    const isPartyRow = row.closest('table').id === 'party_table';
                    const tbody = row.parentNode;
                    if (tbody.querySelectorAll('tr').length > 1) {
                        row.remove();
                        if (isPartyRow) {
                            updateAvailableParties();
                        }
                    } else {
                        alert('At least one row is required.');
                    }
                }
            });
        });
    </script>

@endsection

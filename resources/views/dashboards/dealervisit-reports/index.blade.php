@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Visit History</h1>
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
                            @include('dashboards.dynamicNav.report')
                        </nav>
                    </div>
                </div>

                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                <form method="get" class="page-content-container" action="/visithistory-dealer">
                    <div class="row">
                        {{-- <div class="col-lg-2">
                            <div class="form-group">
                                <label class="control-label bolds">Type</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="">Select Type</option>
                                    <option value="dealer" @if (isset($request->type) && strtolower($request->type) === 'dealer') selected @endif>Dealer</option>
                                    <option value="retailer" @if (isset($request->type) && strtolower($request->type) === 'retailer') selected @endif>Retailer
                                    </option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label bolds" id="dealer-label">Dealers</label>
                                <select class="form-control selectpicker" data-live-search="true"
                                    data-live-search-style="startsWith" name="dealer" id="dealer"
                                    data-dealerid="{{ $dealerid ?? '' }}">
                                    <option value="">Select dealer</option>
                                    @foreach ($dealers as $dealer)
                                        <option value="{{ $dealer->code }}" @selected(($dealerid ?? old('user')) == $dealer->code)>
                                            {{ $dealer->name }} ({{ $dealer->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="field-validation-valid" data-valmsg-for="resCountry"
                                    data-valmsg-replace="true"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="control-label bolds">From<span
                                        class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateF" name="start-date" autocomplete="off"
                                        placeholder="YYYY-MM-DD" value="{{ $from ?? old('start-date') }}"
                                        class="form-control nepali-calendar" required>
                                </div>
                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('start-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('start-date') }}</strong>
                                    </span>
                                @endif
                                @if (session('status'))
                                    <span class="help-block required">
                                        <strong>{{ session('status') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="control-label bolds">To<span
                                        class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                <div class="date" data-provide="datepicker" class="form-control">
                                    <input type="text" id="nepaliDateFa" name="end-date" autocomplete="off"
                                        placeholder="YYYY-MM-DD" value="{{ $to ?? old('end-date') }}"
                                        class="form-control nepali-calendar" required>
                                </div>
                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('end-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('end-date') }}</strong>
                                    </span>
                                @endif

                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"
                                    style="margin-top:24px;width: 100%;">View</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        @if (!empty($de))
                                            @if (empty($from) && empty($to))
                                                Total Dealer Visit - ({{ $de }})
                                            @elseif(!empty($from) && empty($to))
                                                Dealer Visit({{ $from }} TO Now) -({{ $de }})
                                            @elseif(empty($from) && !empty($to))
                                                Dealer Visit (till {{ $to }}) -({{ $de }})
                                            @else
                                                Dealer visit ({{ $from }} TO {{ $to }})
                                                -({{ $de }})
                                            @endif
                                        @else
                                            @if (empty($from) && empty($to))
                                                Total Dealer Visit
                                            @elseif(!empty($from) && empty($to))
                                                Dealer Visit({{ $from }} TO Now)
                                            @elseif(empty($from) && !empty($to))
                                                Dealer Visit (till {{ $to }})
                                            @else
                                                Dealer visit ({{ $from }} TO {{ $to }})
                                            @endif
                                        @endif
                                        - @if (!empty($list))
                                            {{ count($list) }}
                                        @else
                                            0
                                        @endif <br><br>
                                        &nbsp; &nbsp;
                                    </span>
                                </div>
                                <div style="float:right">
                                    <form action="/dealer-visit-export" method="post">
                                        @csrf
                                        <input type="hidden" name="dealer"
                                            value="@if (isset($dealerid)) {{ $dealerid }} @endif">
                                        <input type="hidden" name="status"
                                            value="@if (isset($status)) {{ $status }} @endif">
                                        <input type="hidden" name="from"
                                            value="@if (isset($from)) {{ $from }} @endif">
                                        <input type="hidden" name="to"
                                            value="@if (isset($to)) {{ $to }} @endif">
                                        <button class="btn btn-primary btn-xs">
                                            <i class="fa fa-download" style="padding-right: 5px;"></i>Excel</button>
                                    </form>
                                </div>
                                <br>

                                <div class="uptable">
                                    <div class="row">

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($amt, 2, '.', ',') }}</td>
                                            </table>
                                        </div>

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discount, 2, '.', ',') }}</td>
                                            </table>
                                        </div>

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format(($discount * 15) / 100, 2, '.', ',') }}</td>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($cashamt, 2, '.', ',') }}</td>
                                            </table>
                                        </div>

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discountcash, 2, '.', ',') }}</td>
                                            </table>
                                        </div>

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
                                    </div>

                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($chequeamt, 2, '.', ',') }}</td>
                                            </table>
                                        </div>

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($discountcheque, 2, '.', ',') }} </td>
                                            </table>
                                        </div>

                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format(($discountcheque * 15) / 100, 2, '.', ',') }}
                                                    </td>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Token Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($tokenamt, 2, '.', ',') }}</td>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> S/N </th>
                                            <th> Dealer/Retailer Code</th>
                                            <th> Dealer/Retailer Name </th>
                                            <th> Total Visit </th>
                                            <th> Purpose wise Visit </th>
                                            <th>Total Collection</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($list))
                                            @foreach ($list as $lis)
                                                @if (!$lis->dealer_code)
                                                    @continue
                                                @endif
                                                <tr>
                                                    <td> {{ $loop->iteration }} </td>
                                                    <td>
                                                        @if ($lis->dealer_code)
                                                            {{ $lis->dealer_code }}
                                                        @else
                                                            Unlisted Dealer Visit
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($lis->dealer_code)
                                                            {{ $lis->dealer['name'] }}
                                                        @else
                                                            Unlisted Dealer Visit
                                                        @endif
                                                    </td>
                                                    <td>{{ $lis->visit_count }}</td>
                                                    <td>
                                                        @foreach ($lis->purposes as $purpose)
                                                            {{ $purpose->purposes['purpose'] }}-
                                                            {{ $purpose->visit_count }}
                                                            <br>
                                                        @endforeach
                                                    </td>

                                                    <td>
                                                        {{ $lis->collection }}
                                                    </td>
                                                    <td>{{ $lis->status }}</td>
                                                    <td>
                                                        <?php
                                                        if (isset($from)) {
                                                            $from = $from;
                                                        } else {
                                                            $from = '';
                                                        }
                                                        if (isset($to)) {
                                                            $to = $to;
                                                        } else {
                                                            $to = '';
                                                        }
                                                        if ($lis->dealer_code) {
                                                            $dealer_code = $lis->dealer_code;
                                                        }
                                                        ?>
                                                        <a href="/visitdealer/{{ $dealer_code }}?from={{ $from }}&to={{ $to }}"
                                                            class="btn btn-primary btn-sm"> View </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const dealerSelect = document.getElementById('dealer');
            const label = document.getElementById('dealer-label');

            function loadDealersOrRetailers(type) {
                dealerSelect.innerHTML = '<option value="">Loading...</option>';
                const storedDealerId = dealerSelect.dataset.dealerid;

                fetch('/dealerOrRetailer?type=' + encodeURIComponent(type))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        label.textContent = type.charAt(0).toUpperCase() + type.slice(1) + 's';
                        dealerSelect.innerHTML = '<option value="">Select ' + type + '</option>';

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.code;
                            option.textContent = item.name + ' (' + item.code + ')';
                            dealerSelect.appendChild(option);
                        });

                        if (storedDealerId) {
                            dealerSelect.value = storedDealerId;
                        }

                        if (typeof $ !== 'undefined' && $('.selectpicker').length) {
                            $('#dealer').selectpicker('refresh');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        dealerSelect.innerHTML = '<option value="">Failed to load</option>';
                    });
            }

            const initialType = typeSelect.value;
            if (initialType) {
                loadDealersOrRetailers(initialType);
            }

            typeSelect.addEventListener('change', function() {
                const type = this.value;
                if (type) {
                    loadDealersOrRetailers(type);
                }
            });
        });
    </script> --}}
@endsection

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

                <form method="get" class="page-content-container" action="/visit/areas">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label bolds">Areas</label>

                                <select class="form-control selectpicker" data-live-search="true"
                                    data-live-search-style="startsWith" name="area" id="area">
                                    <option value="">Select Areas</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->areacode }}"
                                            @if (isset($arcode)) @if ($arcode == $area->areacode)
                                    selected @endif
                                        @else @if (old('user') == $area->areacode) selected @endif @endif>{{ $area->area }}</option>
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
                                    <input type="text" id="nepaliDateFa" value="{{ $from ?? old('start-date') }}"
                                        name="start-date" autocomplete="off" placeholder="YYYY-MM-DD"
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
                                    <input type="text" id="nepaliDateF" name="end-date" autocomplete="off"
                                        value="{{ $to ?? old('end-date') }}" placeholder="YYYY-MM-DD"
                                        class="form-control nepali-calendar" required>
                                </div>
                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('end-date'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('end-date') }}</strong>
                                    </span>
                                @endif
                                @if (session('statu'))
                                    <span class="help-block required">
                                        <strong>{{ session('statu') }}</strong>
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
                                        @if (!empty($ar))
                                            @if (empty($from) && empty($to))

                                                area Visit of the day - ({{ $ar }})
                                            @elseif(empty($from) && !empty($to))
                                                Area Visit Details (Till {{ $to }} ) - {{ $ar }}
                                            @elseif(!empty($from) && empty($to))
                                                Area Visit Details ({{ $from }} TO now) - {{ $ar }}
                                            @else
                                                Area Visit Details ({{ $from }} TO {{ $to }}) -
                                                {{ $ar }}

                                            @endif
                                        @else
                                            @if (empty($from) && empty($to))

                                                Totla Area Visit
                                            @elseif(empty($from) && !empty($to))
                                                All Area Visit Details (Till {{ $to }} )
                                            @elseif(!empty($from) && empty($to))
                                                Area Visit Details ({{ $from }} TO Now)
                                            @else
                                                Visit Details ({{ $from }} TO {{ $to }})

                                            @endif

                                        @endif
                                    </span>
                                </div>
                                <div style="float:right">
                                    <form action="/area-visit-export" method="post">
                                        @csrf
                                        <input type="hidden" name="area"
                                            value="@if (isset($arcode)) {{ $arcode }} @endif">
                                        {{-- <input type="hidden" name="status" value="@if (isset($status)){{$status}}@endif"> --}}
                                        <input type="hidden" name="from"
                                            value="@if (isset($from)) {{ $from }} @endif">
                                        <input type="hidden" name="to"
                                            value="@if (isset($to)) {{ $to }} @endif">
                                        <button class="btn btn-primary btn-xs">
                                            <i class="fa fa-download" style="padding-right: 5px;"></i>Excel</button>
                                    </form>
                                </div>
                                <div class="uptable">
                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($amt, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discount, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format(($discount * 15) / 100, 2) }}</td>
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
                                                    <td>Rs.{{ number_format($cashamt, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discountcash, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format(($discountcash * 15) / 100, 2) }}</td>
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
                                                    <td>Rs. {{ number_format($chequeamt, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($discountcheque, 2) }} </td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format(($discountcheque * 15) / 100, 2) }}</td>
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
                                                    <td>Rs. {{ number_format($tokenamt, 2) }}</td>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> S/N. </th>
                                            <th> Area Code </th>
                                            <th> Area </th>
                                            <th> Total Visit </th>
                                            <th> Purpose Wise Visit </th>
                                            <th> Total Dealer </th>
                                            <th> Total Collection </th>
                                            <th> Status </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($list))

                                            @foreach ($list as $lis)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($lis->area)
                                                            {{ $lis->areacode }}
                                                        @else
                                                            Unlisted Dealer Visit
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($lis->area)
                                                            {{ $lis->area->area }}
                                                        @else
                                                            Unlisted Dealer Visit
                                                        @endif
                                                    </td>
                                                    <td> {{ $lis->visit_count }} </td>
                                                    <td>
                                                        @foreach ($lis->purposes as $purpose)
                                                            {{ $purpose->purposes['purpose'] }}-
                                                            {{ $purpose->visit_count }} <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($lis->area)
                                                            {{ $lis->dealer_count }}
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ number_format($lis->collection, 2) }}
                                                    </td>
                                                    <td> {{ $lis->status }}</td>
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
                                                        if ($lis->area) {
                                                            $areacode = $lis->areacode;
                                                        } else {
                                                            $areacode = 'Unlisted_dealer_visit';
                                                        }
                                                        ?>
                                                        <a href="/visitarea/{{ $areacode }}?from={{ $from }}&to={{ $to }}"
                                                            class="btn btn-primary btn-sm"> View </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>

                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>

            </div>
        </div>
    </div>
@endsection

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
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <ul class="nav navbar-nav margin-bottom-35 report-class">

                            <li class="active"><a href="/dashboard/visithistory">Daily Visit </a></li>
                            <li><a href="/dashboard/areavisit">Area Visit </a></li>
                            <li><a href="/dashboard/visithistory-dealer">Dealer Visit </a></li>
                            <li><a href="/dashboard/negotiation">Unlisted Dealer Visit </a></li>
                            <li><a href="/dashboard/attendance">Attendance List </a></li>
                        </ul>

                    </nav>
                </div>
            </div>

            <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
            <form method="get" class="page-content-container" action="/dashboard/history">
                <div class="row">

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label bolds">Officer</label>

                            <select class="form-control selectpicker" data-live-search="true" data-live-search-style="startsWith" name="user" id="user">
                                <option value="" name=>select officer</option>
                                @foreach ($users as $use)
                                <option value="{{ $use->id }}">{{ $use->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-validation-valid" data-valmsg-for="resCountry" data-valmsg-replace="true"></span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="control-label bolds">From<span class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                            <div class="date" data-provide="datepicker" class="form-control">
                                <input type="text" id="nepaliDateFa" name="start-date" autocomplete="off" placeholder="YYYY-MM-DD" class="form-control nepali-calendar" required>
                            </div>
                            <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                            @if ($errors->has('start-date'))
                            <span class="help-block required">
                                <strong>{{ $errors->first('start-date') }}</strong>
                            </span>
                            @endif

                            @if(session('status'))
                            <span class="help-block required">
                                <strong>{{ session('status') }}</strong>
                                @endif
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="control-label bolds">To<span class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                            <div class="date" data-provide="datepicker" class="form-control">
                                <input type="text" id="nepaliDateF" name="end-date" autocomplete="off" placeholder="YYYY-MM-DD" class="form-control nepali-calendar" required>
                            </div>
                            <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                            @if ($errors->has('end-date'))
                            <span class="help-block required">
                                <strong>{{ $errors->first('end-date') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="margin-top:24px;width: 100%;">View</button>
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
                <span class="caption-subject font-red bold uppercase">Total Visits - @if (!empty($list)) {{count($list)}} @endif
                  &nbsp; &nbsp; &nbsp; &nbsp; Today's Visits - {{$s}}
                  {{-- <br><br>
                                    &nbsp; &nbsp;
                                    Total Collection - @if (!empty($amt)) Rs. {{$amt}} @else 0 @endif &nbsp; &nbsp;
                  Total Discount - @if (!empty($discount)) Rs. {{$discount}} @else 0 @endif &nbsp; &nbsp;
                  Total TDS - @if (!empty($discount)) Rs. {{$discount*15/100}} @else 0 @endif <br><br>

                  &nbsp; &nbsp;
                  Cash Collection - @if (!empty($cashamt)) Rs. {{$cashamt}} @else 0 @endif &nbsp; &nbsp;
                  Cash Discount -@if (!empty($discountcash)) Rs. {{$discountcash}} @else 0 @endif &nbsp; &nbsp;
                  Cash TDS -@if (!empty($discountcash)) Rs. {{$discountcash*15/100}} @else 0 @endif<br><br>

                  &nbsp; &nbsp;
                  Cheque Collection - @if (!empty($chequeamt)) Rs. {{$chequeamt}} @else 0 @endif &nbsp; &nbsp;
                  Cheque Discount - @if (!empty($discountcheque)) Rs. {{$discountcheque}} @else 0 @endif &nbsp; &nbsp;
                  Cheque TDS - @if (!empty($discountcheque)) Rs. {{$discountcheque*15/100}} @else 0 @endif --}}
                </span>
                <!--@if (!empty($list))-->
                <!--<br><br>-->
                <!--                    <div class="row">-->
                <!--<span class="col-md-6">Visit Count</span>-->
                <!--<div class="col-md-5">-->
                <!--Total:{{count($list)}}-->
                <!--</div>-->
                <!--    </div>-->
                <!--    @endif-->

              </div>

              <div class="uptable">
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
                        <td>Rs.{{ number_format(($discount*15/100), 2, '.', ',') }}</td>
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
                        <td>Rs.{{ number_format(($discountcash*15/100), 2, '.', ',') }}</td>
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
                        <td>Rs. {{ number_format($discountcheque*15/100, 2, '.', ',') }}</td>
                    </table>
                  </div>
                  @endif
                </div>
              </div>


              <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="sample_2">
                  <thead>
                    <tr>
                      <th> Visit No.</th>
                      <th> User </th>
                      <th> Dealer </th>
                      <th> Purpose </th>
                      <th> Date </th>
                      <th> Start To End Time </th>
                      <th> Remarks </th>
                      <th> Visit Status </th>
                    </tr>
                  </thead>
                  <tbody>
                    @if (!empty($list))

                    @foreach($list as $lis)
                    <tr>
                      <td> {{$lis['id']}} </td>
                      <td> {{ $lis['user'] }}</td>
                      <td> {!! $lis['dealer_name'] !!} </td>
                      <td>
                        {{ implode(', ', $lis['purpose']) }}
                      </td>
                      <td> {{$lis['date']}}</td>
                      <td> {{$lis['start_time']}} To {{ $lis['end_time'] }} ({{$lis['total_hr']}}hr {{$lis['rem_min']}}min {{$lis['rem_sec']}}sec)
                      </td>
                      <td> {{ $lis['remarks'] }}</td>
                      <td>
                        @if($lis['status'] == 'Online')
                        <button class='btn btn-success'>{{ $lis['status'] }}</button>
                        @else
                        <button class='btn btn-danger'> {{ $lis['status'] }} </button>
                        @endif
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
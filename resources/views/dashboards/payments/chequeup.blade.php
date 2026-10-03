@extends('dashboards.sessions.layout')

@section('content')

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Collections</h1>
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
                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <ul class="nav navbar-nav margin-bottom-35">
                                <li><a href="/collection-report">All Collection </a></li>
                                <li><a href="/collection">Cash Collection </a></li>
                                <li class="active"><a href="/cheque-collection">Cheque Collection</a></li>

                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if(session('Status'))
                        <div class="m-heading-1 border-green m-bordered">
                            <div class="page-content-col">
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <b>{{ session('Status') }}</b>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">

                                <form method="post" class="page-content-container" action="/collection">
                                    @csrf
                                    <div class="row">

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="control-label bolds">Marketing Officer<span class="required has-feedback{{ $errors->has('user') ? ' has-error' : '' }}">*</span></label>

                                                <select name="user" class="form-control">
                                                    <option value="">Select User</option>
                                                    @foreach( $users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>

                                                <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                                @if ($errors->has('user'))
                                                    <span class="help-block required">
                        <strong>{{ $errors->first('user') }}</strong>
                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="control-label bolds">From<span class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateFa" name="start-date" autocomplete="off" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                                                </div>
                                                <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                                @if ($errors->has('start-date'))
                                                    <span class="help-block required">
                        <strong>{{ $errors->first('start-date') }}</strong>
                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="control-label bolds">To<span class="required has-feedback{{ $errors->has('end-date') ? ' has-error' : '' }}">*</span></label>
                                                <div class="date" data-provide="datepicker" class="form-control">
                                                    <input type="text" id="nepaliDateF" name="end-date" autocomplete="off" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
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
                                    <span class="caption-subject font-red bold uppercase">Collection History</span>
                                </div>

                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th> Dealer </th>
                                        <th> User </th>
                                        <th> Collection Date </th>
                                        <th> Amount </th>
                                        <th>Token Value</th>
                                        <th> Payment Type </th>
                                        <th> Cheque Date </th>
                                        <th> Cheque Number </th>
                                        <th> Receipt Number </th>
                                        <th> Bank </th>
                                        <th> Remark</th>
                                        <th> Status </th>
                                        <th>Log</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (!empty($payment))
                                        @foreach($payment as $payments)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td> {{$payments->dealer['name']}} </td>
                                                <td> {{$payments->user['name']}} </td>
                                                <td> {{$payments->collection_date}} </td>
                                                <td> {{$payments->amount}} </td>
                                                <td> {{$payments->token}}</td>
                                                <td> {{$payments->payment_type}} </td>
                                                <td> {{$payments->payment_date}} </td>
                                                <td> {{$payments->cheque_num}} </td>
                                                <td> {{$payments->receipt_num}} </td>
                                                <td> {{$payments->bank['name']}} </td>
                                                <td> {{$payments->remarks}}</td>
                                                <td>
                                                    @if ($payments->payment_type == 'cheque (bearer)')
                                                    <div>
                                                            @if ($payments->status == 'sent to office')
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverche" class="btn btn-warning btn-sm ab">{{__('Sent to Office')}}</button>
                                                            @elseif ($payments->status == 'bounced')
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverche" class="btn btn-danger btn-sm ab">{{__('Bounced')}}</button>
                                                            @elseif($payments->status == 'withdrawn')
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverche" class="btn btn-info btn-sm ab">{{ __('withdrawn')}}</button>
                                                            @else
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverche" class="btn btn-success btn-sm ab">{{ __('Withdrawn & Deposited') }}</button>
                                                            @endif
                                                    </div>
                                                                <div name="myFormche" data-toggle="popoverche" class="hide popover-content"></div>
                                                        @else
                                                        <div>
                                                            @if ($payments->payment_type == 'cheque (bearer)')
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverch" class="btn btn-warning btn-sm ab">{{ $payments->status }}</button>
                                                            @elseif ($payments->status == 'bounced')
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverch" class="btn btn-danger btn-sm ab">{{ $payments->status }}</button>
                                                            @elseif($payments->status == 'deposited')
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverch" class="btn btn-info btn-sm ab">{{ $payments->status }}</button>
                                                            @else
                                                                <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverch" class="btn btn-success btn-sm ab">{{ $payments->status }}</button>
                                                            @endif
                                                        </div>
                                                        <div name="myFormch" data-toggle="popoverch" class="hide popover-content"></div>
                                                        @endif

                                                        {{--<div name="myFormche" data-toggle="popoverche" class="hide popover-content"></div>--}}
                                                </td>
                                                <!-- <td> -->
                                                <!-- <button type="button" class="btn btn-warning btn-sm ab" data-toggle="modal" data-target="#exampleModalCenter">Log</button> -->
                                                <!-- </td> -->
                                                <!-- <td> <button type="button" data-api-parameter="{{ $payments->id }}" data-toggle="popoverlog" class="btn btn-warning btn-sm ab">{{__('Log')}}</button></td> -->
                                                <td>
                                                <a class = "order-view-popup" id = {{ $payments->id }}>
                                                    <div class="btn btn-primary btn-sm" onclick = "document.getElementById('order-popup').style.display='block'">{{__('Log')}}</div></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                        {{-- pop up after clicking log  --}}
                    <div id = "order-popup" >



                    </div>
                    <script>
                        $(document).ready(function(){
                            $(".order-view-popup").click(function(){
                            var id =  $(this).attr('id');
                            $("#order-popup").load("/collectionpop/"+ id);
                            $("#order-pop").css("display","block");
                            var modal = document.getElementById('order-popup');
                            window.onclick = function(event) {
                            if (event.target == modal) {
                                modal.style.display = "none";
                            }
                            }
                            });

                        });
                    </script>
                    </div>

                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>

            </div>
        </div>
    </div>
    </div>
    <!-- model -->



            </div>
        </div>
    </div>

@endsection

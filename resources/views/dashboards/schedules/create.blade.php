@extends('dashboards.sessions.layout')
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>ADD NEW VISIT SCHEDULE</h1>
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


                            <li class="active"><a href="/dashboard/schedule-create">Add New visit Schedule</a></li>
                                <li><a  href="/dashboard/schedule">List visit schedule </a></li>
                                <li><a href="/dashboard/bulk-schedule">Bulk Schedule</a></li>
                                <li><a href="/dashboard/repeated-schedule">Repeated Schedule</a></li>

                            <!-- <li class="active"><a>Add New visit Schedule</a></li>
                                <li><a href = "/dashboard/schedule">List visit schedule </a></li> -->


                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if(session('Status'))

                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Add New Visit Schedule</span>
                                    </div>

                                </div>

                                @if(count($errors) > 0)
                                    @foreach($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif


                                <form method="POST" action="/dashboard/schedule-create">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="user_id" class="col-md-3 col-form-label text-right">{{ __('User Name') }}</label>
                                        <div class="col-md-6">
                                            <select name="user_id" class="form-control">
                                                <option value="">Select Mobile User</option>
                                                @foreach($mobiles as $mobile)
                                                    <option value="{{ $mobile->id }}">{{$mobile->id}}&nbsp;&nbsp;{{ $mobile->name }}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                        <script>
                                    jQuery(document).ready(function(){
                                    jQuery('select[name="user_id"]').on('change',function(){
                                        var id =  jQuery(this).val();
                                        // alert(id);
                                        if(id){
                                            // alert(id);
                                            jQuery.ajax({
                                                url:"/dashboard/schedule/dealer/"+id,
                                                type:"get",
                                                dataType:"json",
                                                success:function(data)
                                                {
                                                    console.log(data);
                                                    jQuery('select[name="Dealer_code"]').empty();
                                                    $('select[name="Dealer_code"]').append('<option value="">Select Dealer</option>');
                                                    data.forEach(function(e){

                                                        $('select[name="Dealer_code"]').append('<option value="'+e+'">'+ e +'</option>');
                                                    });
                                                }
                                                // error:function(){
                                                //     console.log("errors")
                                                // }
                                            });
                                        }
                                    });
                                    });
                                    </script>
                                    <div class="form-group row">
                                        <label for="Dealer_code" class="col-md-3 col-form-label text-right">{{ __('Dealer') }}</label>
                                        <div class="col-md-6">
                                            <select name="Dealer_code" class="form-control">
                                              <option>Select Mobile User First</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="form-group row">
                                    <label for="areas" class="col-md-3 col-form-label text-right">{{ __('Purpose') }}</label>
                                    <div class="col-md-6">
                                    <select name="purpose_id[]" class="selectpicker form-control" id="number-multiple" data-live-search="true" title="Select Role" data-hide-disabled="true" data-actions-box="true" multiple>
                                    @foreach($purpose as $purposes)
                                    <option value="{{ $purposes->id }}">{{ $purposes->purpose}}</option>
                                    @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label  for="expiry_date" class="col-md-3 col-form-label text-right">{{ __('Date') }}</label>
                                    <div class="date" data-provide="datepicker" class="form-control">
                                    <div class="col-md-6">
                                        <input type="text" id="nepaliDateFa" autocomplete = "off" name="expiry_date" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
                                    </div>
                                    </div>
                                    <span class="field-validation-valid" data-valmsg-for="education" data-valmsg-replace="true"></span>
                                      @if ($errors->has('expiry_date'))
                                        <span class="help-block required">
                                    <strong>{{ $errors->first('expiry_date') }}</strong>
                                </span>
                                    @endif
                                    @if(session('status'))
                                    <span class="help-block required">
                                    <strong>{{ session('status') }}</strong>
                                    @endif
                                </div>

                                    <div class="form-group row">
                                        <label for="remarks" class="col-md-3 col-form-label text-right">{{ __('Remarks') }}</label>

                                        <div class="col-md-6">
                                            <textarea class="form-control" name="remarks" placeholder="Remarks" autofocus></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row hidden">
                                        <input name="status" type="text" value="not-approved">
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Create') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

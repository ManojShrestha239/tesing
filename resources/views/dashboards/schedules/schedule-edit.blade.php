@inject('helpers','App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
<style>
    #goback:hover {
        background: red;
        border: 2px solid #f48220;
        cursor: pointer;
        transition: 0.4s;
    }

    #goback:hover h3 {
        color: white;
    }

    #goback:hover img {
        color: white;
    }

    #goback {
        border: 2px solid #0d2444;
        width: 100px;
        border-radius: 32px;
    }

    #goback img {
        margin-top: 6px;
        margin-left: 4px;
        margin-right: 4px;
    }

    #goback h3 {
        margin-top: 6px;
        font-size: 20px;
        font-weight: 600;
    }
</style>
<div class="page-content2">
    <div id="goback" onclick="goBack()">
        <img style="float:left;width:25px; height:25px;" onclick="goBack()" src={{ asset('assets/global/img/left-arrow-key.svg') }}>
        <h3> Back</h3>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <div class="breadcrumbs">
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
                                    <span class="caption-subject font-red bold uppercase">Edit Visit Schedule</span>
                                </div>

                            </div>

                            @if(count($errors) > 0)
                            @foreach($errors->all() as $error)
                            <p class="alert alert-danger">{{ $error }}</p>
                            @endforeach
                            @endif

                            <form method="POST" action="/schedule/{{ $schedules->id }}">
                                {{ method_field('PATCH') }}
                                @csrf

                                <div class="form-group row">
                                    <label for="user_id" class="col-md-3 col-form-label text-right">{{ __('User Id') }}</label>
                                    <div class="col-md-6">
                                        <select name="user_id" class="form-control selectpicker" data-live-search="true ">
                                            <option value="">Select User</option>
                                            @foreach($mobiles as $mobile)
                                            <option value="{{ $mobile->id }}" @if ($schedules->user_id == $mobile->id) selected @endif>{{ $mobile->id }}&nbsp;{{ $mobile->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    jQuery(document).ready(function() {
                                        jQuery('select[name="user_id"]').on('change', function() {
                                            var id = jQuery(this).val();
                                            // alert(id);
                                            if (id) {

                                                jQuery.ajax({
                                                    url: "/schedule/dealer/" + id,
                                                    type: "get",
                                                    dataType: "json",
                                                    success: function(data) {

                                                        console.log(data);
                                                        jQuery('select[name="dealer_code"]').empty();
                                                        $('select[name="dealer_code"]').append('<option value="">select Dealers</option>');
                                                        data.forEach(function(e) {

                                                            $('select[name="dealer_code"]').append('<option value="' + e.code + '">' + e.name + '</option>');
                                                        });
                                                        $('select[name="dealer_code"]').selectpicker('refresh');
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
                                    <label for="dealer_code" class="col-md-3 col-form-label text-right">{{ __('Dealer Code') }}</label>
                                    <div class="col-md-6">
                                        <select name="dealer_code" class="form-control selectpicker" data-live-search="true">

                                            @foreach($dealers as $dealer)
                                            <option @if($dealer->code == $schedules->dealer_code) selected @endif value="{{$dealer->code}}">{{$dealer->code}}&nbsp;{{$dealer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="areas" class="col-md-3 col-form-label text-right">{{ __('Purpose') }}</label>
                                    <div class="col-md-6">
                                        <select name="purpose_id[]" class="selectpicker form-control" id="number-multiple" data-live-search="true" data-hide-disabled="true" data-actions-box="true" multiple>
                                            @foreach($purposes as $purpose)
                                            <option value="{{ $purpose->id }}" @foreach($schedules->schedulepurpose as $sp) @if($sp->purpose_id == $purpose->id) selected @endif @endforeach >{{ $purpose->purpose}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="expiry_date" class="col-md-3 col-form-label text-right">{{ __('Date') }}</label>
                                    <div class="col-md-6">
                                        <input type="text" id="nepaliDateFa" value="{{$helpers->convertDate($schedules->date)}}" autocomplete="off" name="expiry_date" placeholder="YYYY-MM-DD" class="form-control nepali-calendar">
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
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <label for="remarks" class="col-md-3 col-form-label text-right">{{ __('Remarks') }}</label>

                                    <div class="col-md-6">
                                        <textarea class="form-control" name="remarks" autofocus>{{$schedules->remarks}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary" style="width: 200px;">
                                            {{ __('Update') }}
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
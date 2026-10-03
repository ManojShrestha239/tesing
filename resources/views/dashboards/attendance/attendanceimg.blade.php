@extends('dashboards.sessions.layout')
<style>
    a {
        text-decoration: none;
    }

    .thumbnail-custom {
        display: block;
        padding: 4px;
        margin-bottom: 20px;
        line-height: 1.42857;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        -webkit-transition: border 0.2s ease-in-out;
        -o-transition: border 0.2s ease-in-out;
        transition: border 0.2s ease-in-out;
        max-width: 600px;
        height: 500px;
        object-fit: fill;
    }

    .custom-flex {
        flex-wrap: wrap;
    }
</style>
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Attendance Image Update KM</h1>

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
                    @if (session('Status'))
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
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Attendance images</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row  custom-flex">
                                        @foreach ($attendanceimages as $attendanceimage)
                                            <div class="col-md-6">
                                                <div class="thumbnail-custom" style="height: 485px;width: 600px;">
                                                    <a href="{{ asset('storage/attendance/' . $attendanceimage->image) }}"
                                                        target="_blank">
                                                        <img height="440px" width="550px"
                                                            src="{{ asset('storage/attendance/' . $attendanceimage->image) }}"
                                                            alt="{{ $attendanceimage->image }}" style="width:100%">
                                                        <div class="caption">
                                                            <p>{{ $attendanceimage->image }}</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light portlet-fit bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class=" fa fa-user font-red"></i>
                                                    <span class="caption-subject font-red bold uppercase">Change
                                                        checkin</span>
                                                </div>

                                            </div>

                                            @if (count($errors) > 0)
                                                @foreach ($errors->all() as $error)
                                                    <p class="alert alert-danger">{{ $error }}</p>
                                                @endforeach
                                            @endif

                                            <form method="POST"
                                                action="{{ route('attendanceupdatekm', $attendance->id) }}">
                                                {{ method_field('PATCH') }}
                                                @csrf
                                                <input type="hidden" name="id" id="hidden_id"
                                                    value="{{ $attendance->id }}" />
                                                <!-- <span id='message' style="position:absolute;bottom:98px;float:right;"></span> -->
                                                <div class="form-group row">
                                                    <label for="checkin"
                                                        class="col-md-3 col-form-label text-right">{{ __('Enter Checkin KM') }}</label>

                                                    <div class="col-md-6">
                                                        <input type="number" value=" " id="checkin" step="0.1"
                                                            class="form-control" name="checkin" placeholder="100.00 KM"
                                                            required autofocus>
                                                        <!-- <span id='message'></span> -->
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="checkout"
                                                        class="col-md-3 col-form-label text-right">{{ __('Enter Checkout KM') }}</label>

                                                    <div class="col-md-6">
                                                        <input type="number" value=" " step="0.1" id="checkout"
                                                            class="form-control" onkeyup='check();' name="checkout"
                                                            placeholder="200.00 KM" required autofocus>
                                                        <span id='message'></span>
                                                    </div>
                                                </div>

                                                <div class="form-group row" id="totalkm_div" style="display: none;">
                                                    <label for="totalkm"
                                                        class="col-md-3 col-form-label text-right">{{ __('Total KM') }}</label>

                                                    <div class="col-md-6">
                                                        <input type="number" value="{{ $attendance->totalkm }}"
                                                            id="totalkm" class="form-control" onkeyup='check();'
                                                            name="totalkm" placeholder="200.00 KM" readonly>
                                                        <span id='message'></span>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <div class="col-md-6 col-md-offset-3">
                                                        <button type="submit" id="enter"
                                                            class="btn btn-primary pass-change" style="width: 200px;"
                                                            disabled>
                                                            {{ __('Update KM') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <script>
                                                var check = function() {
                                                    if (parseFloat(document.getElementById('checkin').value) <= parseFloat(document.getElementById('checkout')
                                                            .value)) {
                                                        document.getElementById('enter').removeAttribute("disabled");
                                                        document.getElementById('totalkm_div').style.display = "block";
                                                        document.getElementById('totalkm').value = parseFloat(document.getElementById('checkout').value) -
                                                            parseFloat(document.getElementById('checkin').value);
                                                        document.getElementById('message').innerHTML = '';
                                                    } else {
                                                        document.getElementById("enter").disabled = true;
                                                        document.getElementById('message').style.color = 'red';
                                                        document.getElementById('message').innerHTML = 'checkout KM must be greater or equal to checkin km';
                                                        document.createElement("br");
                                                        document.getElementById('totalkm_div').style.display = "none";
                                                    }
                                                }
                                                console.log(check);
                                            </script>



                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="page-content-col"> -->
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pop-up Form -->
    <div id="order-popup">
    </div>

    <script>
        $(document).ready(function() {
            $(".updatekm-view-popup").click(function() {
                var id = $(this).attr('id');
                var date = $(this).attr('date-attribute');
                $("#order-popup").load("/dashboard/attendance/updatekm/" + id + "?date=" + date);
                $("#order-popup").css("display", "block");
                var modal = document.getElementById('order-popup');
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            });

        });
    </script>

@endsection

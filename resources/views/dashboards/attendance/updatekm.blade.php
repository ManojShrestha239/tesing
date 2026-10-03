    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="page-content2">
        <img style="float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <div class="breadcrumbs">
            <h1>Update KM</h1>
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
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Change checkin</span>
                                    </div>

                                </div>

                                @if(count($errors) > 0)
                                @foreach($errors->all() as $error)
                                <p class="alert alert-danger">{{ $error }}</p>
                                @endforeach
                                @endif

                                <form method="POST" action="{{route('attendanceupdatekm', $attendance->id)}}">
                                    {{ method_field('PATCH') }}
                                    @csrf
                                    <input type="hidden" name="id" id="hidden_id" value="{{$attendance->id}}" />
                                    <!-- <span id='message' style="position:absolute;bottom:98px;float:right;"></span> -->
                                    <div class="form-group row">
                                        <label for="checkin" class="col-md-3 col-form-label text-right">{{ __('Enter Checkin KM') }}</label>

                                        <div class="col-md-6">
                                            <input type="number" value="{{$attendance->checkinkm}}" id="checkin" step="0.1" class="form-control" name="checkin" placeholder="100.00 KM" required autofocus>
                                            <!-- <span id='message'></span> -->
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="checkout" class="col-md-3 col-form-label text-right">{{ __('Enter Checkout KM') }}</label>

                                        <div class="col-md-6">
                                            <input type="number" value="{{$attendance->checkoutkm}}" step="0.1" id="checkout" class="form-control" onkeyup='check();' name="checkout" placeholder="200.00 KM" required autofocus>
                                            <span id='message'></span>
                                        </div>
                                    </div>

                                    <div class="form-group row" id="totalkm_div" style="display: none;">
                                        <label for="totalkm" class="col-md-3 col-form-label text-right">{{ __('Total KM') }}</label>

                                        <div class="col-md-6">
                                            <input type="number" value="{{$attendance->totalkm}}" id="totalkm" class="form-control" onkeyup='check();' name="totalkm" placeholder="200.00 KM" readonly>
                                            <span id='message'></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" id="enter" class="btn btn-primary pass-change" style="width: 200px;" disabled>
                                                {{ __('Update KM') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <script>
                                    var check = function() {
                                        if (parseFloat(document.getElementById('checkin').value) <= parseFloat(document.getElementById('checkout').value)) {
                                            document.getElementById('enter').removeAttribute("disabled");
                                            document.getElementById('totalkm_div').style.display = "block";
                                            document.getElementById('totalkm').value = parseFloat(document.getElementById('checkout').value) - parseFloat(document.getElementById('checkin').value);
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
            </div>
        </div>
    </div>
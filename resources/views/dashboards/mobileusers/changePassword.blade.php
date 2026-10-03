    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="page-content2">
        <img style="float:right;width:25px; height:25px;"
            onclick="document.getElementById('order-popup').style.display='none'"
            src={{ asset('assets/pages/img/quit.svg') }}>
        <div class="breadcrumbs">
            <h1>Change Password</h1>
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
                                        <span class="caption-subject font-red bold uppercase">Change Password</span>
                                    </div>

                                </div>

                                @if(count($errors) > 0)
                                @foreach($errors->all() as $error)
                                <p class="alert alert-danger">{{ $error }}</p>
                                @endforeach
                                @endif

                                <form method="POST" action="/user/changePassword/{{$id}}">
                                    {{ method_field('PATCH') }}
                                    @csrf
                                    <input type="hidden" id="hidden_id" value="{{$id}}" />
                                    <span id='message' style="position:absolute;bottom:98px;right:120px"></span>
                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Enter New Password') }}</label>

                                        <div class="col-md-6">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="*****" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                            class="col-md-3 col-form-label text-right">{{ __('Confirm Password') }}</label>

                                        <div class="col-md-6">
                                            <input type="password" id="con_password" class="form-control"
                                                onkeyup='check();' name="con_password" placeholder="*****" required
                                                autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" id="enter" class="btn btn-primary pass-change"
                                                style="width: 200px;" disabled>
                                                {{ __('Change Password') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <script>
                                var check = function() {
                                    if (document.getElementById('password').value == document.getElementById(
                                            'con_password').value) {
                                        document.getElementById('enter').removeAttribute("disabled");
                                        document.getElementById('message').style.color = 'green';
                                        document.getElementById('message').innerHTML = 'password matched';
                                    } else {
                                        document.getElementById("enter").disabled = true;
                                        document.getElementById('message').style.color = 'red';
                                        document.getElementById('message').innerHTML = 'Password not matched';

                                    }
                                }
                                </script>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
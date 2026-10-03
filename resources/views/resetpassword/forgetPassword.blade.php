<!DOCTYPE html>
<html>
    <!-- BEGIN HEAD -->
    <head>
@include('dashboards.partials.head')
    <link href="../assets/pages/css/login-2.css" rel="stylesheet" type="text/css" />
</head>
    <!-- END HEAD -->

    <body class=" login">
            <!-- BEGIN LOGO -->
            <div class="logo">
                <a href="#">
                    <img height="100px" src={{ asset('assets/pages/img/biraj_logo.png') }} alt="" /> </a>
            </div>

          <!-- END LOGO -->
        <div class = "login-container">

        <!-- BEGIN LOGIN -->
        <div class="content " style="padding: 75px 0;">

            <!-- BEGIN LOGIN FORM -->
            <form class="login-form" action="{{ route('forget.password.post') }}" method="post">
                @csrf
                <div class="form-title">
                    @if (Session::has('message'))
                         <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                    @endif
                    <span id = "login" class="form-title">Reset Password</span>
                    {{--<span class="form-subtitle">to continue</span>--}}
                </div>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter Email and password. </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                    <input id="email" placeholder="Email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                                        <strong style="color:red">{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif
                    @if (session('Status'))
                        <span class="invalid-feedback" role="alert">
                                        <strong style="color:red">{{ session('Status') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group">
                <div class="form-actions">
                    <button type="submit" class="btn btn-block uppercase">Send Password Reset Link</button>
                </div>
                </div>
            </form>
            <!-- END LOGIN FORM -->
        </div>
    </div>
    </body>

</html>

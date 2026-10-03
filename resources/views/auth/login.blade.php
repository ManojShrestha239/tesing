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
            <img height="100px" src="{{ asset('assets/pages/img/logo-fieldma.png') }}" alt="" /> </a>
    </div>

    <!-- END LOGO -->
    <div class="login-container">

        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form class="login-form" action="{{ route('login') }}" method="post">
                @csrf
                <div class="form-title">
                    @if (Session::has('message'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                    @endif
                    <span id="login" class="form-title">LOGIN</span>
                    {{-- <span class="form-subtitle">to continue</span> --}}
                </div>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter Email and password. </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                    <input id="email" placeholder="Email" type="email"
                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                        value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong style="color:red">{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <input id="password" type="password" placeholder="Password"
                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                        required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong style="color:red">{{ $errors->first('password') }}</strong>
                        </span>
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="btn btn-block uppercase">Login</button>
                    </div>
                    <div class="form-actions">
                        <div class="pull">
                            <label class="pull rememberme mt-checkbox mt-checkbox-outline">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                                <span></span>
                            </label>
                        </div>
                        <div class="forget-password">
                            <a href="{{ route('forget.password.get') }}" id="forget-password"
                                class="forget-password">Forgot Password?</a>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END LOGIN FORM -->
        </div>
    </div>
    <div class="copyright hide"> 2018 © BihaniTech </div>
    @include('dashboards.partials.js')
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="../assets/pages/scripts/login.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
</body>

</html>

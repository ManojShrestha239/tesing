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
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form class="login-form" action="{{ route('reset.password.post') }}" method="post">
                @csrf
                    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif
                <div class="form-title">
                    @if (session('Status'))
                        <span class="invalid-feedback" role="alert">
                                        <strong style="color:red">{{ session('Status') }}</strong>
                                    </span>
                    @endif
                    <span id = "login" class="form-title">Reset Password</span>
                    {{--<span class="form-subtitle">to continue</span>--}}
                </div>
                <input type="hidden" name="token" value={{ $token }}  required>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter Email and password. </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                     <input type="text" readonly id="email_address" class="form-control" name="email" value="{{ $email->email }}" placeholder="Email">
                                  @if ($errors->has('email'))
                                      <span class="text-danger">{{ $errors->first('email') }}</span>
                                  @endif
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <input type="password" id="password" class="form-control" name="password" required autofocus placeholder="Password">
                                  @if ($errors->has('password'))
                                      <span class="text-danger">{{ $errors->first('password') }}</span>
                                  @endif

                    <label class="control-label visible-ie8 visible-ie9">Confirm Password</label>
                     <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required autofocus placeholder="Confirm Password">
                                  @if ($errors->has('password_confirmation'))
                                      <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                  @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-block uppercase">Login</button>
                </div>
                </div>
            </form>
            <!-- END LOGIN FORM -->
        </div>
    </div>
    </body>

</html>

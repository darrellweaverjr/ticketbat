<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>{{ config('app.name', 'TicketBat Admin') }} - Login</title>
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' ; child-src 'none'; frame-ancestors 'self';">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="{{ config('app.name', 'TicketBat.com') }}" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="{{config('app.theme')}}css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{config('app.theme')}}css/login.min.css" rel="stylesheet" type="text/css" />
        <link href="{{config('app.theme')}}css/style.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="{{ asset('/themes/img/favicon.ico') }}" /> 
        <link rel="apple-touch-icon" href="{{ asset('/themes/img/favicon.ico') }}" /> 
        <link rel="apple-touch-icon-precomposed" href="{{ asset('/themes/img/favicon.ico') }}" /> 
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="/login"><img src="{{config('app.theme')}}img/logo-big.png" alt="" /></a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form class="login-form" action="{{ url('/login') }}" method="post">{{ csrf_field() }}
                <h3 class="form-title font-green">Welcome</h3>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter email and password. </span>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="email" autocomplete="off" placeholder="Email" name="username" value="{{ old('email') }}" required autofocus />
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" required/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-actions">
                    <center><button type="submit" class="btn green uppercase">Login</button></center>
                </div>
            </form>
            <!-- END LOGIN FORM -->
        </div>
        <div class="copyright"> {{date('Y')}} © TicketBat.com </div>
        <!-- BEGIN CORE PLUGINS -->
        <script src="{{config('app.theme')}}js/jquery.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/jquery.validate.js" type="text/javascript"></script>
        <script src="{{config('app.theme')}}js/login.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
    </body>

</html>

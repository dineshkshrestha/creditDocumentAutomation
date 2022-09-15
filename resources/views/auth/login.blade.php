<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login ||Civil Bank LTD</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/image/icon.png')}}"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css"
          href="{{asset('assets/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('assets/login/fonts/iconic/css/material-design-iconic-font.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/login/css/util.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/login/css/main.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/plugins/toastr/build/toastr.min.css')}}">
    <style>
        .bg {
            /* The image used */
            background-image: url("{{asset('assets/image/logo.png')}}");
            /* Full height */
            height: 100%;
            width: 100%;
            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat;
            /*background-size: cover;*/
        }
    </style>
</head>
<body>
<div class="bg">
    <div class="limiter">
        <!-- <h1 align="center" style="color: red;">Please Use Your Desktop Login Password For Login.</h1> -->
        <div class="container-login100">
            <div class="wrap-login100">
                <form class="login100-form validate-form" id="logout-form" novalidate="novalidate" method="POST"
                      action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <span class="login100-form-title p-b-26">
                        Document Automation System
                    </span>
                    <div class="wrap-input100 validate-input" data-validate="Valid username is more than 5 character: example">
                        {{--<input class="input100" type="text" name="email">--}}

                        <input id="username" type="text" class="input100" name="username" value="{{ old('username') }}" required
                               autofocus>
                        <span class="focus-input100" data-placeholder="Username"></span>
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Enter password">
                        <span class="btn-show-pass">
                            <i class="zmdi zmdi-eye"></i>
                        </span>
                        <input class="input100" type="password" name="password">
                        <span class="focus-input100" data-placeholder="Password"></span>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                    @if ($errors->has('username'))
                        <span class="help-block">
                      <b>                 {{ $errors->first('username') }}
                         </b>           </span>

                    @endif
                    @if ($errors->has('deactive'))
                        <span class="help-block">
                      <b>                 {{ $errors->first('deactive') }}
                         </b>           </span>

                    @endif

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>

                    </div>
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                Login
                            </button>
                        </div>
                        {{--<span>                                <a class="btn btn-link"--}}
                        {{--href="{{ route('password.request') }}">--}}
                        {{--Forgot Your Password?--}}
                        {{--</a>--}}
                        {{--</span>--}}
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<script src="{{asset('assets/login/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<script src="{{asset('assets/login/js/main.js')}}"></script>
<script src="{{asset('assets/backend/plugins/toastr/build/toastr.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-full-width",
            "preventDuplicates": false,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        @if(\Session::has('danger'))
toastr.error("{{ Session::get('danger') }}");
        @endif
        @if(\Session::has('warning'))
toastr.warning("{{ Session::get('warning') }}");
        @endif
        @if(\Session::has('success'))
toastr.success("{{ Session::get('success') }}");
        @endif

    });
</script>
</body>
</html>


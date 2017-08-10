<!DOCTYPE html>
<html>
  <head>
    <title>ЕГЭ-Репетитор | Вход</title>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta charset="utf-8">
    <base href="{{ config('app.url') }}">
    <link href="{{ asset('css/app.css', isProduction()) }}" rel="stylesheet" type="text/css">
    {{-- <link href="https://fonts.googleapis.com/css?family=Roboto:700" rel="stylesheet"> --}}
    <link href="css/signin.css" rel="stylesheet" type="text/css">
    @yield('scripts')

    <script src="{{ asset('/js/vendor.js', isProduction()) }}"></script>
    <script src="{{ config('app.url') }}{{ elixir('js/app.js', isProduction()) }}"></script>
    <script src='https://www.google.com/recaptcha/api.js?hl=ru'></script>

    @foreach(['moment.min', 'inputmask', 'mask', 'engine', 'ngmap.min'] as $script_name)
        <script src="{{ asset('/js/' . $script_name . '.js', isProduction()) }}"></script>
    @endforeach
    <style>
        .grecaptcha-badge {
            visibility: hidden;
        }
    </style>
    <script>
        function captchaChecked() {
            scope.goLogin()
        }
    </script>
  </head>

  <body class="content" ng-app="Egerep" ng-controller="LoginCtrl" style="background-image: url('img/wallpapper/{{ $wallpapper_id }}.jpg')">
      @yield('content')
  </body>

</html>

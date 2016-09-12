<!DOCTYPE html>
<html>
  <head>
    <title>ЕГЭ-Репетитор | Вход</title>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta charset="utf-8">
    <base href="{{ config('app.url') }}">
    <link href="{{ asset('css/app.css', isProduction()) }}" rel="stylesheet" type="text/css">
    <link href="css/signin.css" rel="stylesheet" type="text/css">
    @yield('scripts')

    <script src="{{ asset('/js/vendor.js', isProduction()) }}"></script>
    <script src="{{ config('app.url') }}{{ elixir('js/app.js', isProduction()) }}"></script>

    @foreach(['moment.min', 'inputmask', 'mask', 'engine', 'laroute', 'ngmap.min'] as $script_name)
        <script src="{{ asset('/js/' . $script_name . '.js', isProduction()) }}"></script>
    @endforeach

  </head>

  <body class="content" ng-app="Egerep" ng-controller="LoginCtrl">
      <div class="container">
            <div class="row">
              <div class="col-sm-1">
              </div>
              <div class="col-sm-10">
                  @yield('content')
              </div>
          </div>
      </div>
  </body>

</html>

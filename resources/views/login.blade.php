<!DOCTYPE html>
<html>
  <head>
    <title>Laravel</title>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta charset="utf-8">
    <base href="{{ env('BASE_URL') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
    <link href="css/signin.css" rel="stylesheet" type="text/css">
    {{-- <link href='https://fonts.googleapis.com/css?family=Ubuntu&subset=latin,cyrillic' rel='stylesheet' type='text/css'> --}}
    @yield('scripts')

    <script src="{{ asset('/js/vendor.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>

    @foreach(['moment.min', 'bootbox', 'bootstrap-datepicker.min', 'bootstrap-datetimepicker',
        'inputmask', 'jquery.cookie', 'jquery.datetimepicker',
        'jquery.fileupload', 'jquery.timepicker', 'mask', 'engine', 'laroute', 'svgmap', 'ngmap.min'
    ] as $script_name)
        <script src="{{ asset('/js/' . $script_name . '.js') }}"></script>
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

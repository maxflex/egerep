<!DOCTYPE html>
<html>
  <head>
    <title>ЕГЭ-Репетитор</title>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta charset="utf-8">
    <base href="{{ config('app.url') }}">
    <link href="{{ config('app.url') }}{{ elixir('css/app.css') }}" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="favicon.png" />
    @yield('scripts')
    <script src="{{ asset('/js/vendor.js', isProduction()) }}"></script>
    <script src="{{ config('app.url') }}{{ elixir('js/app.js', isProduction()) }}"></script>
    @foreach($js as $script_name)
        <script src="{{ asset('/js/' . $script_name . '.js', isProduction()) }}"></script>
    @endforeach
    @yield('scripts_after')
    @include('server_variables')

  </head>
  <body class="content" ng-app="Egerep" ng-controller="@yield('controller')"
        ng-init='user = {{ $user }};
        @if (isset($nginit))
            {{ $nginit }}
        @endif
    '>
    <div class="row">
      <div style="margin-left: 10px" class="col-sm-2">
        <div>
          <div class="list-group">
              @include('_menu')
          </div>
        </div>
      </div>
      <div style="padding: 0; width: 80.6%;" class="col-sm-9 content-col">
        <div class="panel panel-primary">
          <div class="panel-heading panel-heading-main">
              <div class="row">
                  <div class="col-sm-5">@yield('title')</div>
                  <div class="col-sm-2 center">
                      @yield('title-center')
                  </div>
                  <div class="col-sm-5 right">
                      <div class="pull-right links-right">@yield('title-right')</div>
                  </div>
              </div>
          </div>
          <div class="panel-body panel-frontend-loading">
              <div class="frontend-loading animate-fadeIn" ng-show='frontend_loading'>
                  <span>загрузка...</span>
              </div>
              @yield('content')
          </div>
        </div>
      </div>
    </div>
    {{-- @rights-refactored --}}
    @if(allowed(\Shared\Rights::PHONE_NOTIFICATIONS))
      <div class="phone-app">
        <?php include '../resources/assets/bower/phoneapi/dist/template/_phone_api.php' ?>
        <phone user_id="{{ $user->id }}" type="egerep" key="{{ config('app.pusher_key') }}" cluster='eu'></phone>
      </div>
    @endif
    @include('_search')
  </body>
</html>

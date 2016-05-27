<!DOCTYPE html>
<html>
  <head>
    <title>Laravel</title>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta charset="utf-8">
    <base href="{{ config('app.url') }}">
    <link href="{{ asset('css/app.css', isProduction()) }}" rel="stylesheet" type="text/css">
    {{-- <link href='https://fonts.googleapis.com/css?family=Ubuntu&subset=latin,cyrillic' rel='stylesheet' type='text/css'> --}}
    @yield('scripts')
    <script src="{{ asset('/js/vendor.js', isProduction()) }}"></script>
    <script src="{{ asset('/js/app.js', isProduction()) }}"></script>
    @foreach($js as $script_name)
        <script src="{{ asset('/js/' . $script_name . '.js', isProduction()) }}"></script>
    @endforeach
    @yield('scripts_after')


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
          <form id="global-search" action="search" method="post" style="margin-bottom: 10px">
            <div class="input-group">
              <input id="global-search-text" type="text" placeholder="Поиск..." name="global_search" class="form-control" ng-model='global_search'><span class="input-group-btn">
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search no-margin-right"></span></button></span>
            </div>
            <!-- /input-group-->
          </form>
          <div class="list-group">
              <a class="list-group-item active">Меню</a>
              <a href="requests" class="list-group-item">Заявки</a>
              <a href="attachments" class="list-group-item">Стыковки</a>
              <a class="list-group-item active">Преподаватели</a>
              <a href="tutors" class="list-group-item">Преподаватели</a>
              <a href="debt/map" class="list-group-item">Дебет</a>
              <a class="list-group-item active">Настройки</a>
              <a href="logout" class="list-group-item">Выход</a></div>
        </div>
      </div>
      <div style="padding: 0; width: 80.6%;" class="col-sm-9 content-col">
        <div class="panel panel-primary">
          <div class="panel-heading">@yield('title')
              <div class="pull-right links-right">@yield('title-right')</div>
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
  </body>
</html>

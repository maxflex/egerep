<!DOCTYPE html>
<html>
  <head>
    <title>Московский репетитор</title>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta charset="utf-8">
    <base href="{{ config('app.url') }}">
    <link href="{{ config('app.url') }}{{ elixir('css/app.css') }}" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="favicon.png" />
  </head>
  <body class="content">
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
          <div class="panel-heading">
              Страница не существует
              <div class="pull-right links-right">
                  <span class="link-like link-reverse link-white" onclick='window.history.back(-1)'>назад</span>
              </div>
          </div>
          <div class="panel-body" style="display: flex; align-items: center; justify-content: center; background-image: url('img/background/404.png')">
              <img src='img/icons/404.png' />
          </div>
        </div>
      </div>
    </div>
    @include('_search')
  </body>
</html>

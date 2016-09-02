@extends('app')
@section('title', 'Изменить договор')
@section('controller', 'ContractEdit')

@section('content')
    <div class='row mb'>
        <div class='col-sm-3' style='width: 217px'>
            <div class="input-group custom">
              <span class="input-group-addon">договор от – </span>
              <input type="text" ng-model='contract_date' class="form-control bs-date-top">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 ">
            <div id='editor' style="height: 500px">@{{ contract_html }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click="save()" ng-disabled="saving">Сохранить</button>
        </div>
    </div>
@stop

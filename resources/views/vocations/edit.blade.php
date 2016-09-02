@extends('app')
@section('title', 'Заявка на отпуск')
@section('title-right')
    <span class='link-like link-white link-reverse' ng-click='remove()'>удалить заявку</span>
@stop
@section('controller', 'VocationsIndex')

@section('content')
    @include('vocations._form')
    <div class="row" style="margin-top: 15px">
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click='edit()' ng-disabled='saving'>редактировать заявку</button>
        </div>
    </div>
@stop

@extends('app')
@section('title')
    Редактирование платежа
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('title-right')
    <a class="pointer" style="position: absolute; left: 45%" ng-click="FormService.edit()">сохранить</a>
    <a class="pointer" ng-click="FormService.delete($event)">удалить платеж</a>
@stop
@section('content')
@section('controller', 'PaymentForm')
<div class="row">
    <div class="col-sm-12">
        @include('payments._form')
    </div>
</div>
@stop

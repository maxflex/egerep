@extends('app')
@section('title')
    Редактирование источника
    <a href="payments/sources" class="title-link">к списку источников</a>
@stop
@section('title-right')
    <a class="pointer" style="position: absolute; left: 45%" ng-click="FormService.edit()">сохранить</a>
    <a class="pointer" ng-click="FormService.delete($event)">удалить источник</a>
@stop
@section('content')
@section('controller', 'PaymentSourceForm')
<div class="row">
    <div class="col-sm-12">
        @include('payments.sources._form')
    </div>
</div>
@stop

@extends('app')
@section('title')
    Редактирование статьи
    <a href="payments/expenditures" class="title-link">к списку статей</a>
@stop
@section('title-right')
    <a class="pointer" style="position: absolute; left: 45%" ng-click="FormService.edit()">сохранить</a>
    <a class="pointer" ng-click="FormService.delete($event)">удалить статью</a>
@stop
@section('content')
@section('controller', 'PaymentExpenditureForm')
<div class="row">
    <div class="col-sm-12">
        @include('payments.expenditures._form')
    </div>
</div>
@stop

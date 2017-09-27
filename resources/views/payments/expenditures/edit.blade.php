@extends('app')
@section('title')
    Редактирование статьи
    <a href="payments/expenditures" class="title-link">к списку статей</a>
@stop
@section('title-center')
    <a class="pointer" ng-click="FormService.edit()">сохранить</a>
@stop
@section('title-right')
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

@extends('app')
@section('title')
    Редактирование адресата
    <a href="payments/addressees" class="title-link">к списку статей</a>
@stop
@section('title-center')
    <a class="pointer" ng-click="FormService.edit()">сохранить</a>
@stop
@section('title-right')
    <a class="pointer" ng-click="FormService.delete($event)">удалить адресат</a>
@stop
@section('content')
@section('controller', 'PaymentExpenditureForm')
<div class="row">
    <div class="col-sm-12">
        @include('payments.expenditures._form')
    </div>
</div>
@stop

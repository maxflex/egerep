@extends('app')
@section('controller', 'PaymentExpenditureForm')
@section('title')
    Добавление статьи
    <a href="payments/expenditures" class="title-link">к списку статей</a>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('payments.expenditures._form')
        @include('modules.create_button')
    </div>
</div>
@stop

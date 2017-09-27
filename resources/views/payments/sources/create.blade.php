@extends('app')
@section('controller', 'PaymentSourceForm')
@section('title')
    Добавление источника
    <a href="payments/sources" class="title-link">к списку источников</a>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('payments.sources._form')
        @include('modules.create_button')
    </div>
</div>
@stop

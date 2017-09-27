@extends('app')
@section('controller', 'PaymentAddresseeForm')
@section('title')
    Добавление адресата
    <a href="payments/addressees" class="title-link">к списку статей</a>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('payments.addressees._form')
        @include('modules.create_button')
    </div>
</div>
@stop

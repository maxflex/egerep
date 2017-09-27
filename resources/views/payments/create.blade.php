@extends('app')
@section('controller', 'PaymentForm')
@section('title')
    Добавление платежа
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('payments._form')
        @include('modules.create_button')
    </div>
</div>
@stop

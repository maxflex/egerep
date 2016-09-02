@extends('app')
@section('title', 'Заявка на отпуск')
@section('controller', 'VocationsIndex')

@section('content')
    @include('vocations._form')
    <div class="row" style="margin-top: 15px">
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click='create()' ng-disabled='saving'>создать заявку на отпуск</button>
        </div>
    </div>
@stop

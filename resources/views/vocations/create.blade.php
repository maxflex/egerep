@extends('app')
@section('title', 'Заявка на отпуск')
@section('controller', 'VocationsIndex')

@section('content')
    @include('vocations._form')
    <div class="row" style="margin-top: 15px">
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click='create()' ng-disabled='saving || !vocation.data.length'>
                создать заявку
            </button>
            <div class='text-gray small' style='margin-top: 6px; height: 8px'>
                <plural ng-show='vocation.data.length' count='vocation.data.length' type='day'></plural>
            </div>
        </div>
    </div>
@stop

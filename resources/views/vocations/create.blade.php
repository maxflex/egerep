@extends('app')
@section('title', 'Заявка на отпуск')
@section('controller', 'VocationsIndex')

@section('content')
    @include('vocations._form')
    <div class="row">
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click='create()' ng-disabled='saving || !vocation.data.length'>
                создать заявку
            </button>
        </div>
    </div>
@stop

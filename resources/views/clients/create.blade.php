@extends('app')
@section('title', 'Добавление заявки')
@section('content')
@section('controller', 'ClientsForm')

<div class="row">
    <div class="col-sm-12">

        @include('clients._form')

        <div class="row">
            <div class="col-sm-12 center">
                <button class="btn btn-primary" ng-click="edit()" ng-disabled="saving">Добавить</button>
            </div>
        </div>
    </div>
</div>

@stop

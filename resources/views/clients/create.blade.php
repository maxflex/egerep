@extends('app')
@section('title', 'Добавление заявки')
@section('controller', 'ClientsForm')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            @include('clients._form')
            <div class="row">
                <div class="col-sm-12 center">
                    <button class="btn btn-primary" ng-click="save()" ng-disabled="saving">Добавить</button>
                </div>
            </div>
        </div>
    </div>

@stop


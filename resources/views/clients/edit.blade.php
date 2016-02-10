@extends('app')
@section('title', 'Редактирование профиля ученика')
@section('content')
@section('controller', 'ClientsForm')

<div class="row" ng-init="id = {{ $id }}; request_id = {{ $request_id }}">
    <div class="col-sm-12">

        @include('clients._form')

        <div class="row">
            <div class="col-sm-12 center">
                <button class="btn btn-primary" ng-click="edit()" ng-disabled="saving">Редактировать</button>
            </div>
        </div>
    </div>
</div>

@stop

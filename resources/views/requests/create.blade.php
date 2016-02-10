@extends('app')
@section('controller', 'TeachersForm')
@section('title', 'Добавление преподавателя')

@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('teachers._form')
        <div class="row">
            <div class="col-sm-12 center">
                <button class="btn btn-primary" ng-click="add()" ng-disabled="saving">Добавить</button>
            </div>
        </div>
    </div>
</div>

@stop

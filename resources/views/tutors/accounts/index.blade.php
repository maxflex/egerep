@extends('app')
@section('title', 'Отчетность')
@section('controller', 'AccountsCtrl')

@section('title-right')
    <span class="link-like link-reverse link-white" ng-click='addAccountDialog()'>добавить расчет</span>
@stop

@section('content')
    @include('tutors.accounts.partials._fake_table')
    @include('tutors.accounts.partials._real_table')

    <div class="row" ng-if='tutor.accounts.length > 0'>
        <div class="col-sm-12 center">
            <button class="btn btn-primary" ng-click="save()" ng-disabled="saving">Сохранить</button>
        </div>
    </div>

    @include('tutors.accounts.partials._modals')
@stop

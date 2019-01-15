@extends('app')
@section('title', 'Стрим платежей')
@section('controller', 'PaymentsIndex')
@include('payments._top_right_section')
@include('payments._modals')

@section('content')
    <div class="top-links" style='height: 15px'>
        <div class="pull-right">
            <a class="pointer" ng-class="{'active': tab == 'payments'}" ng-click="tab = 'payments'">платежи</a>
            <a class="pointer" ng-class="{'active': tab == 'stats'}" ng-click="tab = 'stats'">статистика</a>
        </div>
    </div>
    <div class="custom-dropdown__close" ng-click="payment_actions_index = null" ng-show="payment_actions_index !== null"></div>
    @include('payments._payments')
    @include('payments._stats')

    {{-- <input name="file" type="file" id="import-button" data-url="payments/import" class="ng-hide"> --}}
    <input name="file" type="file" id="import-button" data-url="payments/import" accept=".xls" class="ng-hide">
@stop

<style>
    label {
        margin: 0 0 2px 10px;
        color: #757575;
        font-size: 12px;
        font-weight: 500;
    }
    tr td {
        outline: none !important;
    }
    tr.selected td {
        background: #f5f4f4;
    }
    .dropdown-header {
        color: #b9b9b9 !important;
        font-size: 16px !important;
        font-weight: bold !important;
    }
    .fa-pencil {
        top: 1px;
        position: relative;
        margin-left: 4px;
        outline: none;
    }
    .col-xs-11.col-sm-4.alert.alert-danger {
        z-index: 9999 !important;
    }
</style>

{{-- drag & drop --}}
{{-- копировать платеж --}}

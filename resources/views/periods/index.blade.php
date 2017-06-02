@extends('app')
@section('title', 'Периоды')
@section('controller', 'PeriodsIndex')
@section('title-right')
    ошибки обновлены @{{ formatDateTime(account_errors_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='recalcErrors()' ng-class="{
        'spinning': account_errors_updating == 1
    }"></span>
@stop

@section('content')

    @include('periods.accounts')
    @include('periods.payments')
    @include('periods.planned')

    <pagination style="margin-top: 30px"
                ng-hide='data.last_page <= 1'
                ng-model="current_page"
                ng-change="pageChanged()"
                total-items="data.total"
                max-size="10"
                items-per-page="data.per_page"
                first-text="«"
                last-text="»"
                previous-text="«"
                next-text="»"
    >
    </pagination>
@stop

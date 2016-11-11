@extends('app')
@section('title', 'Периоды')
@section('controller', 'PeriodsIndex')

@section('content')
    <div class="top-links">
        <div class="pull-right">
            <a ng-href="@{{ type == 'total' ? '' : 'periods' }}" ng-class="{active: type == 'total'}">текущая</a>
            <a ng-href="@{{ type == 'planned' ? '' : 'periods/planned' }}" ng-class="{active: type == 'planned'}">назначенные расчеты</a>
        </div>
    </div>

    @include('periods.total')
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
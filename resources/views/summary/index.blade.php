@extends('app')
@section('title', 'Итоги')
@section('title-right')
    <span ng-hide="debt_updating === '1'">общий дебет на сегодня: @{{ debt_sum | number:0 }}, обновлено @{{ formatDateTime(debt_updated) }}</span>
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='updateDebt()' ng-class="{
        'spinning': debt_updating === true || debt_updating === '1',
        'full-opacity-disabled': debt_updating === '1',
    }"></span>
@stop
@section('controller', 'SummaryIndex')


@section('content')
    <div class="top-links">
        <a href="summary/@{{ type == 'payments' ? type + '/' : '' }}@{{ period_id }}"
           ng-hide="type == 'debtors'"
           ng-repeat="(period_id, period) in {day:'дни', week:'недели', month:'месяцы', year:'годы'}"
           ng-class="{active : filter == period_id}">@{{ period }}</a>

        {{-- @rights-refactored --}}
        @if (allowed(\Shared\Rights::ER_SUMMARY))
		<div class="pull-right">
			<a href="summary" ng-class="{active: type == 'total'}">итоговые данные</a>
			<a href="summary/payments" ng-class="{active: type == 'payments'}">детализация по платежам</a>
            <a href="summary/debtors" ng-class="{active: type == 'debtors'}">сводка по вечным должникам</a>
		</div>
        @endif
    </div>

	@include('summary.total')
	@include('summary.payments')
	@include('summary.debtors')

    <pagination
    	  ng-model="current_page"
    	  ng-change="pageChanged()"
    	  ng-hide="{{ ($per_page > $item_cnt ? 'true' : 'false') }} || type == 'debtors'"
    	  total-items="{{ $item_cnt }}"
    	  max-size="10"
    	  items-per-page="{{ $per_page }}"
    	  first-text="«"
    	  last-text="»"
    	  previous-text="«"
    	  next-text="»"
    >
    </pagination>
@stop

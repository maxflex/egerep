@extends('app')
@section('title', 'Итоги')
@section('title-right')
    общий дебет на сегодня: @{{ total_debt | number}}, обновлено @{{ formatDateTime(debt_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='updateDebt()' ng-class="{
        'spinning': debt_updating
    }"></span>
@stop
@section('controller', 'SummaryIndex')


@section('content')
    <div class="top-links">
        <a href="summary/@{{ type == 'payments' ? type + '/' : '' }}@{{ period_id }}"
           ng-repeat="(period_id, period) in {day:'дни', week:'недели', month:'месяцы', year:'годы'}"
           ng-class="{active : filter == period_id}">@{{ period }}</a>

		<div class="pull-right" ng-show="user.show_summary">
			<a href="summary" ng-class="{active: type == 'total'}">итоговые данные</a>
			<a href="summary/payments" ng-class="{active: type == 'payments'}">детализация по платежам</a>
		</div>
    </div>

	@include('summary.total')
	@include('summary.payments')

    <pagination
    	  ng-model="current_page"
    	  ng-change="pageChanged()"
    	  ng-hide="{{ $per_page > $item_cnt }}"
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

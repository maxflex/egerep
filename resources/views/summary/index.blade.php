@extends('app')
@section('title', 'Итоги')
@section('controller', 'SummaryIndex')

@section('content')
    <div class="top-links">
        <a href="summary/@{{ period_id }}"
           ng-repeat="(period_id, period) in {day:'дни', week:'недели', month:'месяцы', year:'годы'}"
           ng-class="{active : filter == period_id}">@{{ period }}</a>
        <span class='pull-right'>
            общий дебет на сегодня: @{{ total_debt | number}}, обновлено @{{ formatDateTime(debt_updated) }}
            <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='updateDebt()' ng-class="{
                'spinning': debt_updating
            }"></span>
        </span>
    </div>

    <table class="table table-hover summary-table">
		<thead>
			<tr>
				<td width="150">
				</td>
				<td>
					заявок
				</td>
				<td>
					стыковок
				</td>
				<td ng-show="user.show_summary">
					полученные наличные
				</td>
				<td ng-show="user.show_summary">
					проведенные занятия
				</td>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="(date, summary) in summaries">
				<td>
					@{{ date | date:'dd MMMM yyyy' }}
				</td>
				<td>
					@{{ summary.requests.cnt | hideZero }}
				</td>
				<td>
					@{{ summary.attachments.cnt | hideZero }}
				</td>
				<td ng-show="user.show_summary">
					@{{ summary.received.sum | hideZero | number }}
				</td>
				<td ng-show="user.show_summary">
					@{{ summary.commission.sum | hideZero | number }}
				</td>
			</tr>
		</tbody>
	</table>

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

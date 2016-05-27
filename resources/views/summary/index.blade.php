@extends('app')
@section('title', 'Итоги')
@section('controller', 'SummaryIndex')

@section('content')
    <table class="table table-hover summary-table">
		<thead>
			<tr>
				<td width="150">
				</td>
				<td width="100">
					заявок
				</td>
				<td width="100">
					стыковок
				</td>
				<td ng-show="user.show_stat">
					прогноз
				</td>
				<td ng-show="user.show_stat">
					новых клиентов
				</td>
				<td ng-show="user.show_stat">
					активных клиентов
				</td>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="(date, summary) in summaries">
				<td width="150">
					@{{ date | date:'dd MMMM yyyy' }}
				</td>
				<td width="100">
					@{{ summary.requests.cnt }}
				</td>
				<td width="100">
					@{{ summary.attachments.cnt }}
				</td>
				<td ng-show="user.show_stat">
					@{{ summary.forecast }}
				</td>
				<td ng-show="user.show_stat">
					@{{ summary.clients.cnt }}
				</td>
				<td ng-show="user.show_stat">
					@{{ summary.active_clients.cnt }}
				</td>
			</tr>
		</tbody>
	</table>

    <pagination
    	  ng-model="current_page"
    	  ng-change="pageChanged()"
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

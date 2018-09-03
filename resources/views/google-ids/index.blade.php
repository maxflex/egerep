@extends('app')
@section('title', 'Google IDS')
@section('controller', 'GoogleIdsController')

@section('content')
<div class="row flex-list">
    <div>
        <input type='text' ng-model="google_ids" placeholder="google ids" class="form-control" />
    </div>
    <div style='margin-right: 0; width: 200px; flex: none'>
        <button class="btn btn-primary full-width" ng-click="show()">показать</button>
    </div>
</div>
<div class="row" style='margin-top: 30px'>
    <div class="col-sm-12">
        <table class="table reverse-borders" ng-if="data">
			<thead class="table-header">
				<tr>
					<td>
						ID google
					</td>
					<td>
						Дата заявки
					</td>
					<td>
						ID заявки
					</td>
					<td>
						Общая комиссия
					</td>
				</tr>
			</thead>
            <tbody>
                <tr ng-repeat="(google_id, d) in data" ng-class="">
                    <td width='300' ng-class="{'quater-opacity': !d}">
                        @{{ google_id }}
                    </td>
                    <td ng-if="d" width='150'>
                        <div ng-repeat="request in d.requests">
                            @{{ formatDate(request.created_at) }}
                        </div>
                    </td>
                    <td ng-if="d" width='150'>
                        <div ng-repeat="request in d.requests">
                            <a href="/requests/@{{ request.id }}/edit">@{{ request.id }}</a>
                        </div>
                    </td>
                    <td ng-if="d">
                        <span ng-show="d.commission">
                            @{{ d.commission | number }} руб.
                        </span>
                    </td>
                    <td ng-if='!d' colspan='3'></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                    </td>
                    <td colspan='2'>
                        @{{ totals.requests }}
                    </td>
                    <td>
                        @{{ totals.commission | number }}  руб.
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@stop

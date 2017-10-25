@extends('app')
@section('title')
    Статистика
    <a href="payments" class="title-link">назад в стрим</a>
@stop
@section('controller', 'PaymentStats')

@section('content')
    <div class="row flex-list">
        <div>
            <label>кошелёк</label>
            <select multiple title="не выбрано" ng-model="search.wallet_ids" class="selectpicker">
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
        <div>
            <label>in-out</label>
            <select multiple title="не выбрано" ng-model="search.in_out" class="selectpicker">
                <option value="1">in</option>
                <option value="2">out</option>
            </select>
        </div>
        <div>
            <label>статьи</label>
            <select multiple title="не выбрано" ng-model="search.expenditure_ids" class="selectpicker">
                <option ng-repeat="expenditure in expenditures" value="@{{ expenditure.id }}">@{{ expenditure.name }}</option>
            </select>
        </div>
        <div>
            <div class="form-group">
                <label>начало</label>
                <input type="text" readonly placeholder="не указано" class="form-control bs-date-clear pointer" ng-model="search.date_start">
            </div>
        </div>
        <div>
            <div class="form-group">
                <label>конец</label>
                <input type="text" readonly placeholder="не указано" class="form-control bs-date-clear pointer" ng-model="search.date_end">
            </div>
        </div>
        <div>
            <button type="button" ng-disabled="!(search.wallet_ids && search.wallet_ids.length) || stats_loading" class="btn btn-primary full-width" style='margin-top: 21px' ng-click="load()">показать</button>
        </div>
    </div>

    <div ng-repeat="(year, data) in stats_data" ng-if="stats_data">
        <h4>@{{ year }}</h4>
        <table class="table reverse-borders">
            <tr ng-repeat="d in data">
                <td width='200'>
                    @{{ formatStatDate(d.date) }}
                </td>
                <td>
                    <span ng-show="d.sum != 0">@{{ d.sum | number }}</span>
                </td>
            </tr>
        </table>
    </div>
    <table class="table reverse-borders" ng-if="stats_data" style='margin: 0; margin-top: -20px'>
        <tr>
            <td width='200'>

            </td>
            <td>
                <b>@{{ totalStatsSum() | number:2 }}</b>
            </td>
        </tr>
    </table>
@stop
{{-- drag & drop --}}
{{-- копировать платеж --}}

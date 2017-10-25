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
    <div class="vertical-center" ng-if="stats_data === ''">нет данных</div>
    <div ng-repeat="(year, data) in stats_data" ng-if="stats_data && stats_data !== ''">
        <h4>@{{ year }}</h4>
        <table class="table reverse-borders">
            <tr ng-repeat="d in data">
                <td width='250'>
                    @{{ formatStatDate(d.date) }}
                </td>
                <td width='150'>
                    <span ng-show="d.in != 0" class="text-success">+@{{ d.in | number }}</span>
                </td>
                <td width='150'>
                    <span ng-show="d.out != 0" class="text-danger">-@{{ d.out | number }}</span>
                </td>
                <td>
                    <span ng-show="d.sum != 0">@{{ d.sum | number }}</span>
                </td>
            </tr>
        </table>
    </div>
    <table class="table reverse-borders" ng-if="totals" style='margin-top: -20px'>
        <tr>
            <td width='250'>

            </td>
            <td width='150'>
                <b ng-show="totals.in != 0" class="text-success">+@{{ totals.in | number:2 }}</b>
            </td>
            <td width='150'>
                <b ng-show="totals.out != 0" class="text-danger">-@{{ totals.out | number:2 }}</b>
            </td>
            <td>
                <b>@{{ totals.sum | number:2 }}</b>
            </td>
        </tr>
    </table>
    <div ng-if="stats_data && stats_data !== ''">
        <h4>статьи расхода</h4>
        <table class="table reverse-borders" tyle='margin: 0'>
            <tr ng-repeat="(expenditure_id, data) in expenditure_data">
                <td width='250'>
                    @{{ expenditure_id ? findById(expenditures, expenditure_id).name : 'не указано' }}
                </td>
                <td width='150'>
                    <span ng-show="data.in != 0" class="text-success">+@{{ data.in }}</span>
                </td>
                <td width='150'>
                    <span ng-show="data.out != 0" class="text-danger">-@{{ data.out }}</span>
                </td>
                <td>
                    <span>@{{ data.sum | number }}</span>
                </td>
            </tr>
        </table>
    </div>
    <table class="table reverse-borders" ng-if="totals" style='margin-top: -20px'>
        <tr>
            <td width='250'>

            </td>
            <td width='150'>
                <b ng-show="totals.in != 0" class="text-success">+@{{ totals.in | number:2 }}</b>
            </td>
            <td width='150'>
                <b ng-show="totals.out != 0" class="text-danger">-@{{ totals.out | number:2 }}</b>
            </td>
            <td>
                <b>@{{ totals.sum | number:2 }}</b>
            </td>
        </tr>
    </table>
@stop
{{-- drag & drop --}}
{{-- копировать платеж --}}

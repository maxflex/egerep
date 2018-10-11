<div ng-show="tab == 'stats'">
    <div class="row flex-list">
        <div>
            <label>кошелёк</label>
            <select multiple title="не выбрано" ng-model="search_stats.wallet_ids" class="selectpicker" ng-change="loadStats()">
                <option ng-repeat="source in sources" value="@{{ source.id }}">@{{ source.name }}</option>
            </select>
        </div>
        <div>
            <label>статьи</label>
            <select multiple title="не выбрано" ng-model="search_stats.expenditure_ids" class="selectpicker" ng-change="loadStats()">
                <optgroup ng-repeat="expenditure in expenditures" label="@{{ expenditure.name }}">
                    <option ng-repeat="d in expenditure.data" value="@{{ d.id }}">@{{ d.name }}</option>
                </optgroup>
            </select>
        </div>
        <div>
            <div class="form-group">
                <label>начало</label>
                <input type="text" readonly placeholder="не указано" class="form-control bs-date-clear pointer" ng-model="search_stats.date_start" ng-change="loadStats()">
            </div>
        </div>
        <div>
            <div class="form-group">
                <label>конец</label>
                <input type="text" readonly placeholder="не указано" class="form-control bs-date-clear pointer" ng-model="search_stats.date_end" ng-change="loadStats()">
            </div>
        </div>
        {{-- <div>
            <button type="button" ng-disabled="!(search_stats.wallet_ids && search_stats.wallet_ids.length) || stats_loading" class="btn btn-primary full-width" style='margin-top: 21px' ng-click="loadStats()">показать</button>
        </div> --}}
    </div>
    <div class="vertical-center" ng-if="stats_data === null">нет данных</div>
    <div ng-if="stats_data && stats_data !== null">
        <table class="table no-borders">
            <tr ng-repeat="d in stats_data">
                <td width='300'>
                    @{{ d.date }}
                </td>
                <td width='150'>
                    <span ng-show="d.in != 0" class="text-success">+@{{ formatDecimal(d.in) }}</span>
                </td>
                <td width='150'>
                    <span ng-show="d.out != 0" class="text-danger">-@{{ formatDecimal(d.out) }}</span>
                </td>
                <td>
                    <span ng-show="d.sum != 0">@{{ formatDecimal(d.sum) }}</span>
                </td>
            </tr>
        </table>
        <table class="table" style='margin-top: -20px'>
            <tr>
                <td width='300'>

                </td>
                <td width='150'>
                    <b ng-show="totals.in != 0" class="text-success">+@{{ formatDecimal(totals.in) }}</b>
                </td>
                <td width='150'>
                    <b ng-show="totals.out != 0" class="text-danger">-@{{ formatDecimal(totals.out) }}</b>
                </td>
                <td>
                    <b>@{{ formatDecimal(totals.sum) }}</b>
                </td>
            </tr>
        </table>
        <h4>статьи расхода</h4>
        <table class="table no-borders" tyle='margin: 0'>
            <tr ng-repeat="(expenditure_id, data) in expenditure_data">
                <td width='300'>
                    @{{ expenditure_id ? getExpenditure(expenditure_id).name : 'не указано' }}
                </td>
                <td width='150'>
                    <span ng-show="data.in != 0" class="text-success">+@{{ formatDecimal(data.in) }}</span>
                </td>
                <td width='150'>
                    <span ng-show="data.out != 0" class="text-danger">-@{{ formatDecimal(data.out) }}</span>
                </td>
                <td>
                    <span>@{{ formatDecimal(data.sum) }}</span>
                </td>
            </tr>
        </table>
        <table class="table" style='margin-top: -20px'>
            <tr>
                <td width='300'>

                </td>
                <td width='150'>
                    <b ng-show="totals.in != 0" class="text-success">+@{{ formatDecimal(totals.in)  }}</b>
                </td>
                <td width='150'>
                    <b ng-show="totals.out != 0" class="text-danger">-@{{ formatDecimal(totals.out)  }}</b>
                </td>
                <td>
                    <b>@{{ formatDecimal(totals.sum) }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>

<style>
    .br-left {
        border-left: 1px solid #ddd;
    }
</style>

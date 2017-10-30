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
                <option ng-repeat="expenditure in expenditures" value="@{{ expenditure.id }}">@{{ expenditure.name }}</option>
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
        <div ng-repeat="(year, data) in stats_data">
            <h4>@{{ year }}</h4>
            <table class="table no-borders">
                <tr ng-repeat="d in data">
                    <td width='300'>
                        @{{ formatStatDate(d.date) }}
                    </td>
                    <td width='150'>
                        <span ng-show="d.total_in != 0" class="text-success">+@{{ d.total_in | number }}</span>
                    </td>
                    <td width='150'>
                        <span ng-show="d.total_out != 0" class="text-danger">-@{{ d.total_out | number }}</span>
                    </td>
                    <td>
                        <span ng-show="d.total_sum != 0">@{{ d.total_sum | number }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <table class="table" style='margin-top: -20px'>
            <tr>
                <td width='300'>

                </td>
                <td width='150'>
                    <b ng-show="totals.total_in != 0" class="text-success">+@{{ totals.total_in | number:2 }}</b>
                </td>
                <td width='150'>
                    <b ng-show="totals.total_out != 0" class="text-danger">-@{{ totals.total_out | number:2 }}</b>
                </td>
                <td>
                    <b>@{{ totals.total_sum | number:2 }}</b>
                </td>
            </tr>
        </table>
        <h4>статьи расхода</h4>
        <table class="table no-borders" tyle='margin: 0'>
            <tr ng-repeat="(expenditure_id, data) in expenditure_data">
                <td width='300'>
                    @{{ expenditure_id ? findById(expenditures, expenditure_id).name : 'не указано' }}
                </td>
                <td width='150'>
                    <span ng-show="data.in != 0" class="text-success">+@{{ data.in | number }}</span>
                </td>
                <td width='150'>
                    <span ng-show="data.out != 0" class="text-danger">-@{{ data.out | number }}</span>
                </td>
                <td>
                    <span>@{{ data.sum | number }}</span>
                </td>
            </tr>
        </table>
        <table class="table" style='margin-top: -20px'>
            <tr>
                <td width='300'>

                </td>
                <td width='150'>
                    <b ng-show="totals.total_in != 0" class="text-success">+@{{ totals.total_in | number:2 }}</b>
                </td>
                <td width='150'>
                    <b ng-show="totals.total_out != 0" class="text-danger">-@{{ totals.total_out | number:2 }}</b>
                </td>
                <td>
                    <b>@{{ totals.total_sum | number:2 }}</b>
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

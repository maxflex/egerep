@extends('app')
@section('title', 'Итоги')
@section('title-right')
    общий дебет на сегодня: @{{ total_debt | number}}, обновлено @{{ formatDateTime(debt_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='updateDebt()' ng-class="{
        'spinning': debt_updating
    }"></span>
@stop
@section('controller', 'SummaryUsers')

@section('content')
    <div class="row">
        <div class='col-sm-4' style='width: 320px'>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="double-input">
                            <div class="input-group custom" style="width: 45%">
                              <span class="input-group-addon">от </span>
                              <input type="text" class="form-control bs-date-clear pointer" ng-model="search.date_from">
                            </div>
                            <div class="input-group custom" style="width: 55%">
                              <span class="input-group-addon">до </span>
                              <input type="text" class="form-control bs-date-clear pointer" ng-model="search.date_to">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <select class="form-control selectpicker" multiple id='change-user' ng-model='search.user_ids' data-none-selected-text="пользователь">
                        <option
                            ng-repeat="user in UserService.getAll()"
                            value="@{{ user.id }}"
                            data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span>"
                        ></option>
                    </select>
                </div>
                <div class="col-sm-12" style='margin-top: 8px'>
                    <button type="button" class="btn btn-primary full-width" ng-click='update()'>обновить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" ng-if='stats'>
        <div class="col-sm-12">
            <div class="result-line">Всего обработано заявок – @{{ stats.requests.total }}:</div>
            <div ng-repeat="(key, name) in RequestStates">
                @{{ name }} – @{{ stats.requests[key] }}
            </div>

            <div class="result-line">Всего стыковок – @{{ stats.attachments.total }}:</div>
            <div>новых – @{{ stats.attachments.newest }}</div>
            <div>рабочих – @{{ stats.attachments.active }}</div>
            <div>завершенных (занятий нет) – @{{ stats.attachments.archived.no_lessons }}</div>
            <div>завершенных (1 занятие) – @{{ stats.attachments.archived.one_lesson }}</div>
            <div>завершенных (2 занятия) – @{{ stats.attachments.archived.two_lessons }}</div>
            <div>завершенных (3 и более занятий) – @{{ stats.attachments.archived.three_or_more_lessons }}</div>

            <div class="result-line">Распределение стыковок по пользователям:</div>
            <div ng-repeat='(user_id, count) in stats.attachments.users' ng-show='count'>
                @{{ UserService.getLogin(user_id) }} – @{{ count }}
            </div>

            <div class="result-line">Эффективность:</div>
            <div>Общая конверсия заявок в покупающего клиента – @{{ stats.efficency.conversion | number }}</div>
            <div>Средний прогноз – @{{ stats.efficency.forecast | number }} руб.</div>
            <div>Средняя заявка – @{{ stats.efficency.request_avg | number }} руб.</div>
            <div>Средняя стыковка – @{{ stats.efficency.attachment_avg | number }} руб.</div>
            <div>Общая комиссия – @{{ stats.efficency.total_commission | number }} руб.</div>

            <div class="result-line">Распределение комиссии по месяцам:</div>
            <div ng-repeat='commission in stats.commissions'>
                <span style='display: inline-block; width: 150px'>@{{ monthYear(commission.date) }}</span> @{{ commission.sum | number }}
            </div>
        </div>
    </div>
@stop

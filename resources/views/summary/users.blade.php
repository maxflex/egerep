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
                        <option
                                ng-repeat="user in UserService.getBannedUsers()"
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

            <div>
                <table class="table" style="margin-top: 20px; font-size: 0.8em;">
                    <thead class="bold">
                    <tr>
                        <td align="left">Cтыковка</td>
                        <td>Преподаватель</td>
                        <td>Cтыковка</td>
                        <td>Статус</td>
                        <td>Прогноз</td>
                        <td>Реквизиты</td>
                        <td>Заявка</td>
                        <td>Эффективность</td>
                        <td>Доля заявки</td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat-start="request in stats.efficency.data" ng-if="!request.attachments.length">
                            <td align="left" colspan="5" width="44%"></td>
                            <td width='20%'>
                                @{{ UserService.getLogin(request.user_id) }}
                            </td>
                            <td><a href="requests/@{{ request.id }}/edit">@{{ request.id }}</a></td>
                            <td>0</td>
                            <td>@{{ isDenied(request) ? 0 : 1 }}</td>
                        </tr>
                        <tr ng-repeat-end ng-repeat="attachment in request.attachments">
                            <td align="left" width="5%">
                                <a href="requests/@{{ request.id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">@{{ attachment.id }}</a>
                            </td>
                            <td align="left" width="23%">
                                <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a>
                            </td>
                            <td width="6%">
                                @{{ attachment.date }}
                            </td>
                            <td width='10%'>
                                @{{ AttachmentService.getStatus(attachment) }}
                            </td>
                            <td>
                                @{{ attachment.forecast }}
                            </td>
                            <td width='20%'>
                                @{{ UserService.getLogin(attachment.user_id) }}: @{{ formatDateTime(attachment.created_at) }}
                            </td>
                            <td><a href="requests/@{{ request.id }}/edit">@{{ request.id }}</a></td>
                            <td>@{{ attachment.rate }}</td>
                            <td>@{{ attachment.share }}</td>
                        </tr>
                        <tr>
                            <td align="left" colspan="7"></td>
                            <td>@{{ sumEfficency() | number }}</td>
                            <td>@{{ sumShare() | number }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

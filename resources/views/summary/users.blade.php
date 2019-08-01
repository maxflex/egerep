@extends('app')
@section('title', 'Эффективность')
@section('controller', 'SummaryUsers')

@section('title-right')
    <span ng-hide="efficency_updating == 1">обновлено @{{ formatDateTime(efficency_updated) }}</span>
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='updateEfficency()' ng-class="{
        'spinning full-opacity-disabled': efficency_updating == 1,
    }"></span>
@stop

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
                    <div class="form-group">
                        <select ng-disabled='!allowed_all' class="form-control selectpicker" multiple id='change-user'
                            ng-model='search.user_ids' data-none-selected-text="пользователь">
                            <option
                                ng-repeat="user in UserService.getAll()"
                                value="@{{ user.id }}"
                                data-content="@{{ user.nickname }}"
                            ></option>
                            <option
                                    ng-repeat="user in UserService.getBannedUsers()"
                                    value="@{{ user.id }}"
                                    data-content="<span style='color: gray'>@{{ user.nickname }}</span>"
                            ></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <select class='form-control selectpicker' id='change-type'
                        ng-model='search.type' data-none-selected-text='по месяцам'>
                        <option value='months'>по месяцам</option>
                        <option value='users'>по пользователям</option>
                    </select>
                </div>
                <div class="col-sm-12" style='margin-top: 8px'>
                    <button type="button" class="btn btn-primary full-width" ng-click='update()'>обновить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" ng-if='stats && stats.data'>
        <div class="col-sm-12" style='overflow-x: scroll; margin-top: 20px'>
            <table class='table table-divlike table-blackborder'>
                <tr>
                    <td></td>
                    <td ng-repeat='(title, s) in stats.data' style='width: 40px'>
                        <span class="vertical-text" style='height: 60px'>@{{ title }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style='width: 380px'>
                            всего обработано заявок
                        </div>
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.requests.total | hideZero }}
                    </td>
                </tr>
                <tr ng-repeat="(key, name) in RequestStates">
                    <td>
                        @{{ name }}
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.requests[key] | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        доля отказов
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.requests.deny_percentage | hideZero }}<span ng-show='s.requests.deny_percentage | hideZero'>%</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        всего стыковок
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.total | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        новых
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.newest | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        рабочих
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.active | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        завершенных (занятий нет)
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.archived.no_lessons | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        завершенных (1 занятие)
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.archived.one_lesson | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        завершенных (2 занятия)
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.archived.two_lessons | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        завершенных (3 и более занятий)
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.attachments.archived.three_or_more_lessons | hideZero }}
                    </td>
                </tr>
                <tr>
                    <td>
                        общая конверсия заявок в покупающего клиента
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.efficency.conversion | hideZero | number }}
                    </td>
                </tr>
                <tr>
                    <td>
                        средний прогноз среди рабочих и завершенных (3+)
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.efficency.forecast | hideZero | number }}
                    </td>
                </tr>
                <tr>
                    <td>
                        средняя заявка
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.efficency.request_avg | hideZero | number }}
                    </td>
                </tr>
                <tr>
                    <td>
                        средняя стыковка
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.efficency.attachment_avg | hideZero | number }}
                    </td>
                </tr>
                <tr>
                    <td>
                        общая комиссия
                    </td>
                    <td ng-repeat='s in stats.data'>
                        @{{ s.efficency.total_commission | hideZero | number }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="result-line">Распределение комиссии по месяцам:</div>
            <div ng-repeat='(date, sum) in stats.commissions'>
                <span style='display: inline-block; width: 150px'>@{{ monthYear(date) }}</span> @{{ sum | number }}
            </div>

            @include('summary.explanation_by_attachments')
            @include('summary.explanation_by_tutors')
        </div>
    </div>
    <div class="row" ng-if='stats === null'>
        <div class="col-sm-12">
            <p align="center" class='text-gray' style="margin: 100px 0">нет данных</p>
        </div>
    </div>
@stop

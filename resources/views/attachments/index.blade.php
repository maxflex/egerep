@extends('app')
@section('title', 'Стыковки')
@section('controller', 'AttachmentsIndex')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-tabs nav-tabs-links" style="margin-bottom: 20px">
             <li ng-repeat="(state_id, state) in AttachmentStates" data-id="@{{state_id }}"
                ng-class="{'active' : chosen_state_id == state_id || !chosen_state_id && state_id == 'new', 'request-status-li': state_id != 'all' && (chosen_state_id != state_id)}"
                >
                <a class="list-link" href="@{{state_id}}" ng-click="changeState(state_id)" data-toggle="tab" aria-expanded="@{{$index == 0}}">
                    @{{ state.label }} (@{{ state_counts[state_id] }})
                </a>
             </li>
        </ul>
    </div>
</div>


<div ng-if="chosen_state_id == 'new'" ng-repeat="attachment in attachments" class="attachment-list">
    <div class="attachment-list-item">
        <div>
            <span ng-show="attachment.client.name">@{{ attachment.client.name }},</span>
            <span ng-show="attachment.client.grade > 0">@{{ Grades[attachment.client.grade] }},</span>
            <span ng-show="attachment.client.address">@{{ attachment.client.address }},</span>
            <span ng-repeat="phone_field in ['phone', 'phone2', 'phone3']">
                <span ng-show="attachment.client[phone_field]">
                    <span class="underline-hover inline-block"
                          ng-click="PhoneService.call(attachment.client[phone_field])">
                          @{{ PhoneService.format(attachment.client[phone_field]) }}</span>
                    <span class="glyphicon glyphicon-envelope sms-in-list"
                          ng-click="PhoneService.sms(attachment.client[phone_field])"
                          ng-show="PhoneService.isMobile(PhoneService.format(attachment.client[phone_field]))"></span>
                </span>
            </span>
        </div>
        <div>
            Репетитор: <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a>
            (<span ng-repeat="subject_id in attachment.subjects">@{{ Subjects.all[subject_id] + ($last ? '' : ', ') }}</span>),
            <span ng-show="!attachment.account_data_count">занятий не было</span>
            <span ng-show="attachment.account_data_count">
                проведено @{{ attachment.account_data_count }}
                <plural count='attachment.account_data_count' type='lesson' text-only></plural>
            </span>
        </div>
        <div ng-show="attachment.comment">Условия: @{{ attachment.comment }}</div>
        <br/>
        <div class="attachment-list-item-comments">
            <comments entity-type='attachment' entity-id='attachment.id' user='{{ $user }}' track-loading='1'></comments>
        </div>
        <div>
            Стыковку №@{{ attachment.id }} создал: @{{ UserService.getLogin(attachment.user_id) }} @{{ formatDateTime(attachment.created_at) }} <a href="requests/@{{ attachment.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">редактировать</a>
        </div>
        <hr class='list-separate' ng-hide="$last">
    </div>
</div>

<div ng-if="['inprogress', 'ended'].indexOf(chosen_state_id) != -1">
    <table class="table attachment-table">
        <thead class="bold">
            <tr>
                <td class="col-sm-2">
                    Преподаватель
                </td>
                <td class="col-sm-2">
                    <span ng-click="sort('created_at')" role="button">
                        Cтыковка
                    </span>
                </td>
                <td class="col-sm-2">
                    <span ng-click="sort('lesson_count')" role="button">
                        Занятия
                    </span>
                </td>
                <td ng-if="chosen_state_id == 'ended'" class="col-sm-3">
                    <span ng-click="sort('total_lessons_missing')" role="button">
                        Занятия к проводке
                    </span>
                </td>
                <td class="col-sm-1">
                    <span ng-click="sort('forecast')" role="button">
                        Прогноз
                    </span>
                </td>
                <td ng-if="chosen_state_id == 'ended'" class="col-sm-2">
                    <span ng-click="sort('archive_date')" role="button">
                        Архивация
                    </span>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="attachment in attachments">
                <td align="left"><a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a></td>
                <td>@{{ attachment.date }}</td>
                <td>@{{ attachment.lesson_count | hideZero }}</td>
                <td ng-if="chosen_state_id == 'ended'">@{{ attachment.archive.total_lessons_missing | hideZero }}</td>
                <td>@{{ attachment.forecast | number }}</td>
                <td ng-if="chosen_state_id == 'ended'">@{{ formatDate(attachment.archive.created_at) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div ng-if="chosen_state_id == 'all'">
    <table class="table attachment-table" style="font-size: 0.8em;">
        <thead class="bold">
        <tr>
            <td></td>
            <td class="col-sm-2"  align="left">
                Преподаватель
            </td>
            <td class="col-sm-1">
                <span ng-click="sort('created_at')" role="button">
                    Cтыковка
                </span>
            </td>
            <td class="col-sm-1">
                <span ng-click="sort('lesson_count')" role="button">
                    Занятия
                </span>
            </td>
            <td class="col-sm-1">
                <span ng-click="sort('total_lessons_missing')" role="button">
                    Занятия к проводке
                </span>
            </td>
            <td class="col-sm-1">
                <span ng-click="sort('forecast')" role="button">
                    Прогноз
                </span>
            </td>
            <td class="col-sm-1">
                <span ng-click="sort('archive_date')" role="button">
                    Архивация
                </span>
            </td>
            <td class="col-sm-1">Статус</td>
            <td>Реквизиты</td>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="attachment in attachments">
            <td align="left">
                <a href="requests/@{{ attachment.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">стыковка @{{ attachment.id }}</a>
            </td>
            <td align="left">
                <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a>
            </td>
            <td>
                @{{ attachment.date }}
            </td>
            <td>
                @{{ attachment.lesson_count | hideZero }}
            </td>
            <td>
                @{{ attachment.archive.total_lessons_missing | hideZero }}
            </td>
            <td>
                @{{ attachment.forecast | hideZero | number}}
            </td>
            <td>
                @{{ formatDate(attachment.archive.created_at) }}
            </td>
            <td>
                @{{ AttachmentService.getStatus(attachment) }}
            </td>
            <td>
                @{{ UserService.getLogin(attachment.user_id) }}: @{{ formatDateTime(attachment.created_at) }}
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="row" ng-hide="attachments.length">
    <div class="col-sm-12">
        <h3 style="text-align: center; margin: 50px 0; color: #ddd;">
            <span ng-show="frontend_loading">Загрузка данных...</span>
            <span ng-hide="frontend_loading">Список стыковок пуст</span>
        </h3>
    </div>
</div>

<pagination style="margin-top: 30px"
        ng-hide='data.last_page <= 1'
        ng-model="current_page"
        ng-change="pageChanged()"
        total-items="data.total"
        max-size="10"
        items-per-page="data.per_page"
        first-text="«"
        last-text="»"
        previous-text="«"
        next-text="»"
    >
</pagination>
@stop

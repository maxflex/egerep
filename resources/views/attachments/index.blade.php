@extends('app')
@section('title', 'Стыковки')
@section('controller', 'AttachmentsIndex')

@section('content')

<div class="row flex-list">
    <div>
        <select ng-model='search.state' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.state[''] || '' }}">все статусы</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in AttachmentStates'
                data-subtext="@{{ counts.state[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.hide' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.hide[''] || '' }}">любая видимость</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in AttachmentVisibility'
                data-subtext="@{{ counts.hide[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.account_data' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.account_data[''] || '' }}">занятия в отчетности</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence[1]'
                data-subtext="@{{ counts.account_data[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.total_lessons_missing' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.total_lessons_missing[''] || '' }}">занятия к проводке</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence[1]'
                data-subtext="@{{ counts.total_lessons_missing[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.forecast' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.forecast[''] || '' }}">прогноз</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Presence[1]'
                data-subtext="@{{ counts.forecast[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
</div>

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

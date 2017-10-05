@extends('app')
@section('title', 'Стыковки')
@section('title-right')
    ошибки обновлены @{{ formatDateTime(attachment_errors_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='recalcAttachmentErrors()' ng-class="{
        'spinning': attachment_errors_updating == 1
    }"></span>
@stop
@section('controller', 'AttachmentsIndex')

@section('content')

@include('attachments._mode')

<div class="row flex-list attachment-filters">
    <div>
        @include('modules.user-select')
    </div>
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
    <div>
        <select ng-model='search.debtor' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.debtor[''] || '' }}">вечный должник</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in YesNo'
                data-subtext="@{{ counts.debtor[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.hide' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.hide[''] || '' }}">все</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in AttachmentVisibility'
                data-subtext="@{{ counts.hide[id] || '' }}"
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
    <div>
        <select ng-model='search.error' class='selectpicker fix-viewport' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.error[''] || '' }}">все</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in AttachmentErrors' data-title='test'
                data-content="<div title='@{{ name }}'>@{{ id }}<small class='text-muted'>@{{ counts.error[id] || '' }}</small></div>"
                value="@{{id}}"></option>
        </select>
    </div>
</div>

<table class="table" style="font-size: 0.8em;">
    <thead class="bold">
        <tr>
            <td></td>
            <td align="left">
                Преподаватель
            </td>
            <td>
                Cтыковка
            </td>
            <td>
                Занятий
            </td>
            <td>
                Прогноз
            </td>
            <td>
                Архивация
            </td>
            <td>Статус</td>
            <td>Реквизиты</td>
            <td>Класс</td>
            <td>Ошибки</td>
        </tr>
    </thead>
    <tbody>
    <tr ng-repeat="attachment in attachments">
        <td align="left" width="5%">
            <a href="requests/@{{ attachment.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">@{{ attachment.id }}</a>
        </td>
        <td align="left" width="23%">
            <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a> 
            <span class='remove-space' ng-hide="attachment.tutor.margin === null">(M@{{attachment.tutor.margin}})</span>
        </td>
        <td width="6%">
            @{{ attachment.date }}
        </td>
        <td width="6%">
            @{{ attachment.lesson_count | hideZero }}<plus previous='attachment.lesson_count' count='attachment.archive.total_lessons_missing'></plus>
        </td>
        <td width="6%">
            @{{ attachment.forecast | hideZero | number}}
        </td>
        <td width='6%'>
            @{{ attachment.archive.date }}
        </td>
        <td width='10%'>
            @{{ AttachmentService.getStatus(attachment) }}
        </td>
        <td width='20%'>
            @{{ UserService.getLogin(attachment.user_id) }}: @{{ formatDateTime(attachment.created_at) }}
        </td>
        <td>
            @{{ Grades[attachment.grade] }}
        </td>
        <td width='10%'>
            <span ng-repeat='code in attachment.errors' ng-attr-aria-label="@{{ AttachmentErrors[code] }}" class='hint--bottom-left'>@{{ code }}@{{ $last ? '' : ',  ' }}</span>
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

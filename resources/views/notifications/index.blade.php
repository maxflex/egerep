@extends('app')
@section('title', 'Напоминания')
@section('controller', 'NotificationsIndex')

@section('content')

<div class="row flex-list" style="width: 100%">
    <div>
        <select ng-highlight ng-model='search.approved' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.approved[''] || '' }}">все</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Notify'
                    data-subtext="@{{ counts.approved[id] || '' }}"
                    value="@{{id}}">@{{ name }}</option>
        </select>
    </div>


    <div>
        <select ng-highlight ng-model='search.state' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.state[''] || '' }}">все статусы</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in AttachmentStates'
                    data-subtext="@{{ counts.state[id] || '' }}"
                    value="@{{id}}">@{{ name }}</option>
        </select>
    </div>

    <div>
        @include('modules.user-select')
    </div>
    <div>
        <select ng-highlight class="form-control selectpicker" ng-model='search.type' ng-change="filter()" id='change-type'>
            <option value="" data-subtext="@{{ counts.type[''] || '' }}">все</option>
            <option disabled>──────────────</option>
            <option data-subtext="@{{ counts.type[0] || '' }}" value="0">требующие звонка</option>
            <option data-subtext="@{{ counts.type[1] || '' }}" value="1">не требующие звонка</option>
        </select>
    </div>
    <div>
        <select ng-highlight class="form-control selectpicker" ng-model='search.created' ng-change="filter()" id='change-created'>
            <option value="" data-subtext="@{{ counts.created[''] || '' }}">все</option>
            <option disabled>──────────────</option>
            <option data-subtext="@{{ counts.created[1] || '' }}" value="1">созданные</option>
            <option data-subtext="@{{ counts.created[0] || '' }}" value="0">автоматические</option>
        </select>
    </div>
</div>

<table class="table">
    <thead class="bold">
        <tr>
            <td></td>
            <td align="left">ПРЕПОДАВАТЕЛЬ</td>
            <td align="left">СТЫКОВКА</td>
            <td align="left">РЕКВИЗИТЫ СТЫКОВКИ</td>
            <td align="left">КОММЕНТАРИЙ</td>
            <td>НАПОМИНАНИЕ</td>
            <td>СТАТУС</td>
        </tr>
    </thead>
    <tbody>
    <tr ng-repeat="attachment in attachments">
        <td align="left" width="9%">
            <a href="requests/@{{ attachment.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">стыковка @{{ attachment.id }}</a>
        </td>
        <td align="left" width="17%">
            <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a>
        </td>
        <td width="6%">
            @{{ attachment.date }}
        </td>
        <td width='18%'>
            @{{ UserService.getLogin(attachment.user_id) }}: @{{ formatDateTime(attachment.created_at) }}
        </td>
        <td ng-class="{'quater-opacity': !attachment.notification_id}" width="20%">
            @{{ attachment.notification_comment ? attachment.notification_comment : 'комментарий отсутствует' }}
        </td>
        <td ng-class="{'quater-opacity': !attachment.notification_id}" width="9%">
            <span ng-show='attachment.notification_date'>
                @{{ formatDate(attachment.notification_date) }}
            </span>
            <span ng-show='!attachment.notification_date'>
                @{{ formatDate(addDays(attachment.original_date, 2)) }}
            </span>
        </td>
        <td ng-class="{'quater-opacity': !attachment.notification_id}" width='10%'>
            @{{ Notify[attachment.notification_approved || 0] }}
        </td>
        <td>
            <span class='text-danger' ng-show='needsCall(attachment)'>требует звонка</span>
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

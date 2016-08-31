@extends('app')
@section('title', 'Напоминания')
@section('controller', 'NotificationsIndex')

@section('content')

<div class="row flex-list attachment-filters" style="width: 50%">
    <div>
        <select ng-model='search.approved' class='selectpicker' ng-change='filter()'>
            <option value="" data-subtext="@{{ counts.approved[''] || '' }}">все</option>
            <option disabled>──────────────</option>
            <option ng-repeat='(id, name) in Approved'
                    data-subtext="@{{ counts.approved[id] || '' }}"
                    value="@{{id}}">@{{ name }}</option>
        </select>
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
        <select class="form-control selectpicker" ng-model='search.user_id' ng-change="filter()" id='change-user'>
            <option value="" data-subtext="@{{ counts.user_id[''] || '' }}">пользователь</option>
            <option disabled>──────────────</option>
            <option
                    ng-repeat="user in UserService.getWithSystem()"
                    value="@{{ user.id }}"
                    data-content="<span style='color: @{{ user.color || 'black' }}'>@{{ user.login }}</span><small class='text-muted'>@{{ counts.user_id[user.id] || '' }}</small>"
            ></option>
        </select>
    </div>
</div>

<table class="table" style="font-size: 0.8em;">
    <thead class="bold">
        <tr>
            <td></td>
            <td align="left">ПРЕПОДАВАТЕЛЬ</td>
            <td align="left">СТЫКОВКА</td>
            <td>РЕКВИЗИТЫ НАПОМИНАНИЯ</td>
            <td align="left">КОММЕНТАРИЙ</td>
            <td>ДАТА НАПОМИНАНИЯ</td>
            <td>СТАТУС</td>
        </tr>
    </thead>
    <tbody>
    <tr ng-repeat="attachment in attachments" ng-class="{'quater-opacity': !attachment.notification_id}">
        <td align="left" width="9%">
            <a href="requests/@{{ attachment.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">стыковка @{{ attachment.id }}</a>
        </td>
        <td align="left" width="15%">
            <a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a>
        </td>
        <td width="6%">
            @{{ attachment.date }}
        </td>
        <td width='15%'>
            <span ng-if="attachment.notification_created_at">
                @{{ UserService.getLogin(attachment.notification_user_id) }}: @{{ formatDateTime(attachment.notification_created_at) }}
            </span>
        </td>
        <td width="30%">
            @{{ attachment.notification_comment ? attachment.notification_comment : 'комментарий отсутствует' }}
        </td>
        <td width="10%">
            <span ng-show='attachment.notification_date' ng-class="{
                'phone-duplicate-new' : !attachment.notification_approved && pastDate(attachment.notification_date)
            }">
                @{{ formatDate(attachment.notification_date) }}
            </span>
            <span ng-show='!attachment.notification_date' ng-class="{
                'phone-duplicate-new' : pastDate(addDays(attachment.original_date, 2))
            }">
                @{{ formatDate(addDays(attachment.original_date, 2)) }}
            </span>
        </td>
        <td width='10%'>
            @{{ Approved[attachment.notification_approved || 0] }}
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

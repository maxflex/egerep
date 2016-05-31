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
                    @{{ state }} <span ng-if="state_id != 'all'">(@{{ state_counts[state_id] }})</span>
                </a>
             </li>
        </ul>
    </div>
</div>

<div ng-if="chosen_state_id == 'all'" ng-repeat="attachment in attachments" class="attachment-list">
    <a href="#">стыковка @{{ attachment.id }}</a>
</div>

<div ng-if="chosen_state_id == 'new'" ng-repeat="attachment in attachments" class="attachment-list">
    <div class="attachment-list-item">
        <div>
            <span ng-show="attachment.client.name">@{{ attachment.client.name }},</span>
            <span ng-show="attachment.client.grade > 0">@{{ Grades[attachment.client.grade] }},</span>
            <span ng-show="attachment.client.address">@{{ attachment.client.address }}</span>
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
            <comments entity-type='attachment' entity-id='attachment.id' user='{{ $user }}'></comments>
        </div>
        <div>
            Стыковку №@{{ attachment.id }} создал: @{{ UserService.getLogin(attachment.user_id) }} @{{ formatDateTime(attachment.created_at) }} <a href="requests/@{{ attachment.request_list.request_id }}/edit#@{{ attachment.request_list_id }}#@{{ attachment.id }}">редактировать</a>
        </div>
        <hr ng-hide="   $last"/>
    </div>
</div>

<div ng-if="['inprogress', 'ended'].indexOf(chosen_state_id) != -1">
    <table class="attachment-table-items">
        <thead>
            <tr>
                <td class="col-sm-3">Преподаватель</td>
                <td>Дата стыковки</td>
                <td>Количество занятий</td>
                <td>Прогноз</td>
                <td ng-if="chosen_state_id == 'ended'">Дата ахривации</td>
                <td ng-if="chosen_state_id == 'ended'">Количество не проставленных занятий</td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="attachment in attachments">
                <td><a href="tutors/@{{ attachment.tutor_id }}/edit">@{{ attachment.tutor.full_name}}</a></td>
                <td>@{{ formatDate(attachment.created_at) }}</td>
                <td>@{{ attachment.account_data_count }}</td>
                <td>@{{ attachment.forecast }}</td>
                <td ng-if="chosen_state_id == 'ended'">@{{ formatDate(attachment.archive.created_at) }}</td>
                <td ng-if="chosen_state_id == 'ended'">@{{ attachment.archive.total_lessons_missing }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="row" ng-hide="attachments.length">
    <div class="col-sm-12">
        <h3 style="text-align: center; margin: 50px 0">Список стыковок пуст</h3>
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

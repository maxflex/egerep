@extends('app')
@section('title', 'Стыковки')
@section('title-right')
    ошибки обновлены @{{ formatDateTime(attachment_errors_updated) }}
    <span class="glyphicon glyphicon-refresh opacity-pointer" ng-click='recalcAttachmentErrors()' ng-class="{
        'spinning': attachment_errors_updating == 1
    }"></span>
@stop
@section('controller', 'AttachmentsNew')

@section('content')

@include('attachments._mode')
<div ng-repeat='attachment in attachments' style='border-top: 1px solid #ddd; padding-top: 5px'>
    <div class="row">
        <div class="col-sm-8 vcenter">
            <div>
                Репетитор: <a href='tutors/@{{ attachment.tutor.id }}/edit'>@{{ attachment.tutor.last_name }} @{{ attachment.tutor.first_name }} @{{ attachment.tutor.middle_name }}</a>
            </div>
        </div>
        <div class="col-sm-4 vcenter" style="text-align: right; color: #999999">
            @{{ attachment.user_login }} @{{ formatDateTime(attachment.created_at) }}
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8 vcenter">
            <div>
                Клиент: @{{ attachment.client.name }}, @{{ attachment.client.address }}, <span ng-repeat="phone_field in ['phone', 'phone2', 'phone3', 'phone4']">
                    <span ng-show="attachment.client[phone_field]">
                        <span class="underline-hover inline-block"
                              ng-click="PhoneService.call(attachment.client[phone_field])"
                              ng-class="{'phone-duplicate-new': attachment.client[phone_field + '_duplicate']}"
                        >
                              @{{ PhoneService.format(attachment.client[phone_field]) }}</span>
                    </span>
                </span>
            </div>
            <div>
                Стыковка: @{{ attachment.date }}, <a href="@{{ attachment.link }}">@{{ attachment.comment || 'нет описания' }}</a>
            </div>
            <div>
                Проведено занятий: @{{ attachment.account_data_count }}
            </div>
        </div>
        <div class="col-sm-4 vcenter" style="text-align: right; font-size: 24px" ng-init="days_ago = daysAgo(attachment.clean_date)">
            <span ng-show="!days_ago">сегодня</span>
            <span ng-show="days_ago">
                <plural count='days_ago' type='day'></plural>
            </span>
        </div>
    </div>
    <div style="margin-top: 10px">
        <comments entity-type='attachment' entity-id='attachment.id' user='{{ $user }}' track-loading='1'></comments>
    </div>
</div>
@include('modules.pagination')
@stop

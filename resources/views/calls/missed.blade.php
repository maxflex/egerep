@extends('app')
@section('title', 'Пропушенные вызовы')
@section('controller', 'CallsMissed')

@section('content')
    <div ng-show="!calls.length" style="padding: 260px" class="small half-black center">
        нет пропущенных вызовов за сегодня
    </div>
    <style>
        tbody tr:first-child td {
            border: none;
        }
    </style>
    <table class="table">
        <tr ng-repeat='call in calls'>
            <td width="200">
                @{{ formatTimestamp(call.start) }}
            </td>
            <td width="200">
                <span class="underline-hover inline-block" ng-click="PhoneService.call(call.phone_formatted)">@{{ PhoneService.format(call.from_number) }}</span>
            </td>
            <td>
                <span ng-if="call.caller.type == 'tutor'">
                    преподаватель <a target='_blank' href='tutors/@{{ call.caller.id }}/edit'>@{{ call.caller.last_name }} @{{ call.caller.first_name }} @{{ call.caller.middle_name }}</a>
                </span>
                <span ng-if="call.caller.type == 'client'">клиент <a target='_blank' href='client/@{{ call.caller.id }}' >@{{ call.caller.name }}</a></span>
                <span ng-if="!call.caller.type">неизвестный номер</span>
            </td>
            <td>
                <span ng-click="deleteCall(call)" class='link-like text-danger'>удалить</span>
            </td>
        </tr>
    </table>
@stop

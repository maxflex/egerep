@extends('app')
@section('title', 'Пропушенные вызовы')
@section('controller', 'CallsMissed')

@section('content')
    <div ng-show="!calls.length" style="padding: 100px" class="small half-black center">
        нет пропущенных вызовов за сегодня
    </div>
    <table class="table border-reverse">
        <tr ng-repeat='call in calls'>
            <td width="200">
                @{{ formatTimestamp(call.start) }}
            </td>
            <td width="200">
                <span class="underline-hover inline-block" ng-click="PhoneService.call(call.phone_formatted)">@{{ PhoneService.format(call.from_number) }}</span>
            </td>
            <td>@{{ call.entry_id }}</td>
            <td>
                <span ng-if="call.caller.type == 'tutor'">
                    преподаватель <a target='_blank' href='tutor/@{{ call.caller.id }}/edit'>@{{ call.caller.last_name }} @{{ call.caller.first_name }} @{{ call.caller.middle_name }}</a>
                </span>
                <span ng-if="call.caller.type == 'client'">клиент <a target='_blank' href='client/@{{ call.caller.id }}' >@{{ call.caller.name }}</a></span>
                <span ng-if="!call.caller.type">неизвестный номер</span>
            </td>
            <td>
                <span ng-click="deleteCall(call)" class="glyphicon glyphicon-remove pointer" aria-hidden="true"></span>
            </td>
        </tr>
    </table>
@stop
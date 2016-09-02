@extends('app')
@section('title', 'Отпуски')
@section('title-right')
    <a href='vocations/create'>создать заявку</a>
@stop
@section('controller', 'VocationsIndex')

@section('content')
    @include('vocations._form')
    <table class="table reverse-borders">
        <tr ng-repeat='v in vocations'>
            <td width='120'>
                <a href="vocations/@{{ v.id }}/edit">Заявление №@{{ v.id }}</a>
            </td>
            <td>
                @{{ UserService.getLogin(v.user_id) }} от @{{ formatDateTime(v.created_at) }}
            </td>
            <td>
                <span ng-if="v.approved_by == ''" class="text-gray">не подтверждено</span>
                <span ng-if="v.approved_by != ''">
                    <span ng-repeat='user_id in getApprovedUsers(v)'>
                        <user model='UserService.get(user_id)'></user>
                        <span class='remove-space' ng-show='!$last'>,</span>
                    </span>
                </span>
            </td>
            <td>
                <span ng-if="v.work_off" class="text-gray">отработка</span>
            </td>
        </tr>
    </table>
@stop

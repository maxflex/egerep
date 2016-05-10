@extends('app')

@section('title', 'Статистика по сотрудникам')
@section('controller', 'UserStats')

@section('content')
    <style>
        #user-stat-list thead td{
           padding: 5px 5px 20px;
        }
        #user-stat-list tbody td{
            padding: 5px;
        }
        .login-width {
            width: 150px;
        }
        .state-width {
            width: 100px;
            text-align: center;
        }
        .date-title {
            padding: 15px 5px 20px;
            font-size: 1.3em;
        }
    </style>
    <table id="user-stat-list">
        <thead>
            <tr>
                <td class="login-width"></td>
                <td class="state-width" ng-repeat="(state_id, label) in TutorStates">
                    <span class="label tutor-state-@{{state_id}}">@{{label}}</span>
                </td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat-start="(stat_date, stat_data) in stats">
                <td class="date-title" colspan="@{{ state_cnt }}">
                    Таблица измененных статусов на момент @{{ stat_date | date:'dd MMMM yyyy' }} года на 19:00
                </td>
            </tr>
            <tr ng-repeat-end>
                <td colspan="@{{ state_cnt }}">
                    <table>
                        <tr ng-repeat="(user_id, data) in stat_data" ng-show="@{{ UserService.getUser(+user_id).id }}">
                            <td class="login-width">
                                <span style="color:#556DA7;">@{{ UserService.getLogin(+user_id) }}</span>
                            </td>
                            <td class="state-width"
                                ng-repeat="(state_id, label) in TutorStates">
                                @{{ data[state_id] ? data[state_id] : '' }}
                            </td>
                            <td>
                                <b>итого: @{{ sum(data) }}</b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
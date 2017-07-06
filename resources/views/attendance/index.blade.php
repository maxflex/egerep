@extends('app')
@section('title', 'Посещаемость')
@section('controller', 'Attendance')

@section('content')

<div class="row">
    <div class="col-sm-2">
        <select ng-model='month' class='sp'>
            <option ng-repeat='(id, name) in Months'
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
</div>

<div style='width: 100%; overflow-x: scroll; margin-top: 30px; padding-bottom: 10px'>
    <table class="accounts-table" ng-show="hasData()">
        <tbody>
            <tr>
                <td style="border: none; width: 120px"></td>
                <td style="border-top: none; text-align: center; width: 50px" ng-repeat='day in getDays()' style="text-align: center">
                    @{{ day }}
                </td>
            </tr>
            <tr ng-repeat="(user_id, user_data) in data" ng-show="UserService.getLogin(user_id) != 'system'">
                    <td style="text-align: left">
                        @{{ UserService.getLogin(user_id) }}
                    </td>
                    <td ng-repeat='day in getDays()' style="text-align: center" ng-class="{
                        'light-red': late(user_data[day]),
                    }">
                    <div style='width: 40px'>
                        @{{ user_data[day] }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="row center" ng-show="!frontend_loading && !hasData()" style='padding: 220px 0'>
    <span class="text-gray">нет данных</span>
</div>
@stop

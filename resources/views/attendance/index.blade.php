@extends('app')
@section('title', 'Посещаемость')
@section('controller', 'Attendance')

@section('content')

<div ng-repeat="(year_month, year_month_data) in data">
    <h3 class="attachment-stats-year" style='margin-top: 0'>@{{ formatYearMonth(year_month) }}</h3>
    <table class="accounts-table" style='margin-bottom: 30px'>
        <tbody>
            <tr>
                <td style="border: none; width: 120px"></td>
                <td style="border-top: none; text-align: center; width: 50px" ng-repeat='day in getDays()' style="text-align: center">
                    @{{ day }}
                </td>
            </tr>
            <tr ng-repeat="(user_login, user_data) in year_month_data">
                    <td style="text-align: left">
                        @{{ user_login }}
                    </td>
                    <td ng-repeat='day in getDays()' style="text-align: center" ng-class="{
                        'light-green': user_data[day] !== undefined,
                        'light-red': late(user_data[day]),
                    }">
                        <span ng-show='late(user_data[day])'>@{{ user_data[day] }}</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@stop

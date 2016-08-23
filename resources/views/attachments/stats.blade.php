@extends('app')
@section('title', 'Итоги по стыковкам')
@section('controller', 'AttachmentsStats')

@section('content')

<div class="row">
    <div class="col-sm-2">
        <select ng-model='month' class='sp'>
            <option ng-repeat='(id, name) in Months'
                value="@{{id}}">@{{ name }}</option>
        </select>
    </div>
</div>

<div ng-repeat="year in getYears()" style="width: 100%; display: inline-block; margin-bottom: @{{ $last ? '0' : '20' }}px" ng-show='getUsersByYear(year).length'>
    <h3 class="attachment-stats-year">@{{ year }}</h3>
    <table class="accounts-table" style="width: 100%">
        <tbody>
            <tr>
                <td style="border: none; width: 120px"></td>
                <td style="border-top: none; text-align: center; width: 50px" ng-repeat='day in getDays()' style="text-align: center">
                    @{{ day }}
                </td>
                <td style="border: none; width: 50px"></td>
            </tr>
            <tr ng-repeat="user_id in getUsersByYear(year)">
                <td style="text-align: left">
                    @{{ UserService.getLogin(user_id) }}
                </td>
                <td ng-repeat='day in getDays()' style="text-align: center">
                    @{{ getValue(day, year, user_id) }}
                </td>
                <td style="text-align: center">
                    @{{ getUserTotal(year, user_id) }}
                </td>
            </tr>
            <tr>
                <td style="border-bottom: none"></td>
                <td ng-repeat='day in getDays()' style="text-align: center; border-bottom: none">
                    @{{ getDayTotal(year, day) }}
                </td>
                <td style="text-align: center; border-bottom: none">
                    @{{ getDayTotal(year) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
@stop

@extends('app')
@section('title', 'Звонки')
@section('controller', 'CallsIndex')

@section('content')
    <div class="row flex-list">
        <div>
            @include('modules.user-select-light')
        </div>
        <div>
            <select ng-model='search.type' class='selectpicker' ng-change='filter()'>
                <option value="">тип звонка</option>
                <option disabled>──────────────</option>
                <option value="1">входящий</option>
                <option value="2">исходящий</option>
            </select>
        </div>
        <div>
            <select ng-model='search.status' class='selectpicker' ng-change='filter()'>
                <option value="">статус</option>
                <option disabled>──────────────</option>
                <option ng-repeat='(id, name) in CallStatuses' value="@{{id}}">@{{ name }}</option>
            </select>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <table class="table reverse-borders">
                <tr ng-repeat="call in calls">
                    <td>
                        @{{ formatTimestamp(call.start) }}
                    </td>
                    <td>
                        @{{ call.from_extension ? 'исходящий' : 'входящий' }}
                    </td>
                    <td>
                        @{{ UserService.getLogin(call.from_extension || call.to_extension) }}
                    </td>
                    <td>
                        @{{ call.from_extension ? call.to_number : call.from_number }}
                    </td>
                    <td>
                        @{{ callDuration(call) }}
                    </td>
                    <td>
                        <span ng-repeat="status in call.statuses">@{{ CallStatuses[status.status] }}@{{ $last ? '' : ', ' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @include('modules.pagination')
@stop
